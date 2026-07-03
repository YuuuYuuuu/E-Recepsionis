import { useCallback, useEffect, useRef, useState } from 'react'
import { io } from 'socket.io-client'
import { getLiveSocketUrl } from './getLiveSocketUrl.js'

function playIncomingBeep() {
  try {
    const Ctx = window.AudioContext || window.webkitAudioContext
    if (!Ctx) return
    const ctx = new Ctx()
    if (ctx.state === 'suspended') ctx.resume()
    const now = ctx.currentTime
    ;[0, 0.35].forEach((off) => {
      const t = now + off
      const osc = ctx.createOscillator()
      const g = ctx.createGain()
      osc.type = 'sine'
      osc.frequency.setValueAtTime(880, t)
      g.gain.setValueAtTime(0.0001, t)
      g.gain.exponentialRampToValueAtTime(0.12, t + 0.02)
      g.gain.exponentialRampToValueAtTime(0.0001, t + 0.25)
      osc.connect(g)
      g.connect(ctx.destination)
      osc.start(t)
      osc.stop(t + 0.28)
    })
  } catch {
    /* ignore */
  }
}

export default function AdminLiveApp() {
  const [status, setStatus] = useState('connecting')
  const [error, setError] = useState('')
  const [chats, setChats] = useState([])
  const [active, setActive] = useState(null)
  const [messages, setMessages] = useState([])
  const [input, setInput] = useState('')
  const [typingFrom, setTypingFrom] = useState(null)
  const [syncDiag, setSyncDiag] = useState('')
  const [soundEnabled, setSoundEnabled] = useState(() => {
    if (typeof window === 'undefined') return true
    return window.localStorage.getItem('recepsionis_staff_call_sound_enabled') !== '0'
  })
  const [soundSaving, setSoundSaving] = useState(false)
  const [routingCount, setRoutingCount] = useState(0)
  const socketRef = useRef(null)
  const activeRef = useRef(null)
  const prevActiveSessionRef = useRef(null)
  const urlAcceptHandledRef = useRef(false)
  /** Samakan riwayat pesan jika event datang sebelum activeRef terbaru */
  const messageHistorySessionRef = useRef(null)
  const refreshListTimerRef = useRef(null)
  const listRef = useRef(null)
  const audioUnlocked = useRef(false)
  const soundEnabledRef = useRef(true)
  const apiBaseUrl = useCallback(() => {
    const raw =
      typeof window !== 'undefined' && window.__RECEPSIONIS_API_BASE_URL__
        ? window.__RECEPSIONIS_API_BASE_URL__
        : '../api/'
    return String(raw).replace(/\/?$/, '/')
  }, [])

  useEffect(() => {
    soundEnabledRef.current = soundEnabled
    if (typeof window !== 'undefined') {
      window.localStorage.setItem('recepsionis_staff_call_sound_enabled', soundEnabled ? '1' : '0')
    }
  }, [soundEnabled])

  useEffect(() => {
    activeRef.current = active
    const sid = active?.session_id ?? null
    if (sid !== prevActiveSessionRef.current) {
      prevActiveSessionRef.current = sid
      if (sid) {
        setMessages([])
        if (
          messageHistorySessionRef.current &&
          messageHistorySessionRef.current !== sid
        ) {
          messageHistorySessionRef.current = null
        }
      } else {
        setMessages([])
        messageHistorySessionRef.current = null
      }
    }
  }, [active])

  useEffect(() => {
    fetch(`${apiBaseUrl()}admin_notification_preferences.php`, { credentials: 'same-origin' })
      .then((res) => res.json())
      .then((data) => {
        if (!data?.success) return
        setSoundEnabled(Boolean(data.preferences?.sound_enabled))
        setRoutingCount(Number(data.routing?.count || 0))
      })
      .catch(() => {
        /* ignore */
      })
  }, [apiBaseUrl])

  useEffect(() => {
    const s = socketRef.current
    const sid = active?.session_id
    if (!s || !sid || s.disconnected) return undefined
    if (active?.status === 'pending') return undefined
    s.emit('admin_fetch_messages', { session_id: sid })
    // Saat membuka chat: tandai terbaca agar badge unread turun.
    s.emit('admin_mark_read', { session_id: sid })
    return undefined
  }, [active?.session_id])

  const unlockAudio = useCallback(() => {
    audioUnlocked.current = true
  }, [])

  const toggleSound = useCallback(async () => {
    const nextValue = !soundEnabledRef.current
    setSoundSaving(true)
    try {
      const res = await fetch(`${apiBaseUrl()}admin_notification_preferences.php`, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ sound_enabled: nextValue }),
      })
      const data = await res.json()
      if (data?.success) {
        setSoundEnabled(Boolean(data.preferences?.sound_enabled))
      }
    } catch {
      /* ignore */
    } finally {
      setSoundSaving(false)
    }
  }, [apiBaseUrl])

  const scrollBottom = useCallback(() => {
    requestAnimationFrame(() => {
      if (listRef.current) listRef.current.scrollTop = listRef.current.scrollHeight
    })
  }, [])

  useEffect(() => {
    scrollBottom()
  }, [messages, scrollBottom])

  useEffect(() => {
    const tokenUrl =
      typeof window !== 'undefined' && window.__SOCKET_TOKEN_URL__
        ? window.__SOCKET_TOKEN_URL__
        : `${apiBaseUrl()}socket_token.php`

    let cancelled = false

    function adminPreviewUrl() {
      try {
        return new URL(`${apiBaseUrl()}live_session_admin_preview.php`, window.location.href).href
      } catch {
        return `${apiBaseUrl()}live_session_admin_preview.php`
      }
    }

    async function handleUrlAccept(socket, acceptSid) {
      try {
        const pr = await fetch(
          `${adminPreviewUrl()}?session_id=${encodeURIComponent(acceptSid)}`,
          { credentials: 'same-origin' }
        )
        const ptext = await pr.text()
        let pdata
        try {
          pdata = JSON.parse(ptext)
        } catch {
          setSyncDiag(
            'Gagal membaca JSON preview (cek PHP warning). Cuplikan: ' + String(ptext).slice(0, 120)
          )
          urlAcceptHandledRef.current = false
          return
        }
        if (!pdata.success) {
          setSyncDiag(pdata.message || 'Gagal memuat sesi dari URL')
          urlAcceptHandledRef.current = false
          return
        }
        const req = {
          session_id: pdata.session_id,
          staff_call_id: pdata.staff_call_id,
          guest_name: pdata.guest_name,
          visitor_phone: pdata.visitor_phone || '',
          category: pdata.category,
          category_id: pdata.category_id,
          message_preview: pdata.message_preview,
        }
        socket.emit('accept_request', { session_id: req.session_id }, (res) => {
          if (!res?.ok) {
            const err = res?.error
            setSyncDiag(
              err === 'taken'
                ? 'Sesi sudah ditangani admin lain'
                : err === 'ended'
                  ? 'Sesi sudah berakhir'
                  : err === 'forbidden_category'
                    ? 'Anda tidak ditugaskan untuk topik chat ini'
                  : err === 'already_handled'
                    ? 'Permintaan sudah tidak pending'
                    : 'Gagal menerima dari URL'
            )
            urlAcceptHandledRef.current = false
            return
          }
          messageHistorySessionRef.current = req.session_id
          setActive(req)
          socket.emit('admin_list_chats', {})
          try {
            const u = new URL(window.location.href)
            u.searchParams.delete('accept_session')
            u.searchParams.delete('staff_call_id')
            window.history.replaceState({}, '', u.pathname + (u.search || '') + u.hash)
          } catch {
            /* ignore */
          }
        })
      } catch (e) {
        setSyncDiag(String(e.message || e))
        urlAcceptHandledRef.current = false
      }
    }

    async function run() {
      try {
        const res = await fetch(tokenUrl, { credentials: 'same-origin' })
        const data = await res.json()
        if (!data.success || !data.token) {
          setError(data.message || 'Gagal mendapatkan token')
          setStatus('error')
          return
        }
        if (cancelled) return

        const url = getLiveSocketUrl()
        const socket = io(url, {
          auth: { token: data.token },
          // Polling dulu: di localhost/MAMP WebSocket sering gagal dulu → error "websocket error".
          transports: ['polling', 'websocket'],
          reconnection: true,
          reconnectionAttempts: 20,
          reconnectionDelay: 2000,
        })
        socketRef.current = socket

        socket.on('connect', () => {
          setStatus('online')
          setError('')
          setSyncDiag('')
          if (urlAcceptHandledRef.current) return
          const sp = new URLSearchParams(window.location.search)
          const acceptSid = sp.get('accept_session')?.trim()
          if (!acceptSid) return
          urlAcceptHandledRef.current = true
          void handleUrlAccept(socket, acceptSid)
        })

        socket.on('connect_error', (err) => {
          setError(err.message || 'Koneksi gagal')
          setStatus('error')
          setSyncDiag('')
        })

        const scheduleRefreshList = () => {
          if (refreshListTimerRef.current) return
          refreshListTimerRef.current = setTimeout(() => {
            refreshListTimerRef.current = null
            socket.emit('admin_list_chats', {})
          }, 350)
        }

        socket.on('admin_chat_list', (p) => {
          if (!p?.ok || !Array.isArray(p.chats)) return
          setChats(p.chats)
          if (p.chats.length === 0) {
            setSyncDiag('Belum ada chat.')
          } else {
            const unreadTotal = p.chats.reduce((n, c) => n + (Number(c.unread) || 0), 0)
            setSyncDiag(unreadTotal > 0 ? `${unreadTotal} pesan belum dibaca` : '')
          }
        })

        socket.on('admin_chat_changed', (p) => {
          if (soundEnabledRef.current && audioUnlocked.current && (p?.reason === 'guest_message' || p?.reason === 'guest_request')) {
            playIncomingBeep()
          }
          scheduleRefreshList()
        })

        // Legacy: tetap refresh list jika server masih mengirim antrian lama.
        socket.on('admin_notification', () => scheduleRefreshList())

        socket.on('admin_session_restored', (payload) => {
          if (!payload?.session_id) return
          messageHistorySessionRef.current = payload.session_id
          setActive({
            session_id: payload.session_id,
            staff_call_id: payload.staff_call_id,
            guest_name: payload.guest_name,
            visitor_phone: payload.visitor_phone || '',
            category: payload.category,
            category_id: payload.category_id,
            message_preview: payload.message_preview,
          })
        })

        socket.on('message_history', (p) => {
          if (!p?.session_id || !Array.isArray(p.messages)) return
          const sid = p.session_id
          if (activeRef.current?.session_id === sid) {
            setMessages(p.messages)
            return
          }
          if (messageHistorySessionRef.current === sid) {
            setMessages(p.messages)
            messageHistorySessionRef.current = null
          }
        })

        socket.on('receive_message', (msg) => {
          const a = activeRef.current
          if (!a || msg.session_id !== a.session_id) return
          setMessages((m) => [
            ...m,
            {
              sender: msg.sender,
              body: msg.body,
              created_at: msg.created_at,
              admin_name: msg.admin_name,
            },
          ])
          // Jika sedang membuka chat ini dan tamu kirim pesan → mark read (biar badge tetap 0)
          if (msg.sender === 'guest') {
            socket.emit('admin_mark_read', { session_id: msg.session_id })
          }
        })

        socket.on('typing_start', (p) => {
          const a = activeRef.current
          if (a && p?.session_id === a.session_id && p?.from === 'guest') setTypingFrom('guest')
        })
        socket.on('typing_stop', (p) => {
          const a = activeRef.current
          if (a && p?.session_id === a.session_id) setTypingFrom(null)
        })
      } catch (e) {
        setError(String(e.message || e))
        setStatus('error')
      }
    }

    run()
    return () => {
      cancelled = true
      if (socketRef.current) {
        socketRef.current.disconnect()
        socketRef.current = null
      }
    }
  }, [apiBaseUrl])

  const accept = (req) => {
    const socket = socketRef.current
    if (!socket) return
    socket.emit('accept_request', { session_id: req.session_id }, (res) => {
      if (!res?.ok) {
        setSyncDiag(
          res?.error === 'forbidden_category'
            ? 'Anda tidak lagi ditugaskan untuk topik ini.'
            : res?.error === 'taken'
              ? 'Chat sudah diambil admin lain.'
              : 'Gagal menerima chat.'
        )
        return
      }
      messageHistorySessionRef.current = req.session_id
      setActive(req)
      socket.emit('admin_list_chats', {})
    })
  }

  const reject = (req) => {
    const socket = socketRef.current
    if (!socket) return
    socket.emit('reject_request', { session_id: req.session_id }, () => {
      socket.emit('admin_list_chats', {})
    })
  }

  const deleteChat = (req) => {
    const socket = socketRef.current
    if (!socket) return
    const ok = window.confirm('Hapus chat dari daftar? (Pesan tidak dihapus dari database)')
    if (!ok) return
    socket.emit('admin_delete_chat', { session_id: req.session_id }, (res) => {
      if (!res?.ok) return
      if (activeRef.current?.session_id === req.session_id) {
        setActive(null)
      }
    })
  }

  const sendMessage = () => {
    const t = input.trim()
    const act = activeRef.current
    if (!t || !act || !socketRef.current) return
    socketRef.current.emit('send_message', { session_id: act.session_id, text: t }, (res) => {
      if (res?.ok) setInput('')
    })
    socketRef.current.emit('typing_stop', { session_id: act.session_id })
  }

  const endSession = () => {
    const act = activeRef.current
    if (!act || !socketRef.current) return
    socketRef.current.emit('end_session', { session_id: act.session_id }, () => {
      setActive(null)
    })
  }

  const onInputChange = (v) => {
    setInput(v)
    const act = activeRef.current
    if (!act?.session_id || !socketRef.current) return
    socketRef.current.emit('typing_start', { session_id: act.session_id })
  }

  return (
    <div
      className="tw-min-h-screen tw-p-4"
      onClick={unlockAudio}
      onKeyDown={unlockAudio}
      role="presentation"
    >
      <div className="tw-mb-4 tw-flex tw-flex-wrap tw-items-center tw-justify-between tw-gap-2">
        <div>
          <h1 className="tw-text-xl tw-font-bold tw-text-slate-800">Helpdesk IT Live Chat</h1>
          <p className="tw-text-sm tw-text-slate-500">
            Status:{' '}
            <span
              className={
                status === 'online' ? 'tw-font-semibold tw-text-emerald-600' : 'tw-text-amber-600'
              }
            >
              {status === 'online' ? 'Online' : status === 'connecting' ? 'Menghubungkan…' : error || status}
            </span>
          </p>
          {syncDiag && (
            <p className="tw-mt-1 tw-text-xs tw-text-slate-500">{syncDiag}</p>
          )}
          <p className="tw-mt-1 tw-text-xs tw-text-slate-400">Topik yang ditugaskan: {routingCount}</p>
        </div>
        <div className="tw-flex tw-items-center tw-gap-2">
          <button
            type="button"
            onClick={toggleSound}
            className="tw-rounded-full tw-border tw-border-slate-200 tw-bg-white tw-px-3 tw-py-1.5 tw-text-xs tw-font-medium tw-text-slate-700 hover:tw-bg-slate-50"
          >
            {soundSaving ? 'Menyimpan...' : soundEnabled ? 'Suara aktif' : 'Suara mati'}
          </button>
          <p className="tw-text-xs tw-text-slate-400">Klik sekali di halaman untuk mengaktifkan audio browser</p>
        </div>
      </div>

      {error && status === 'error' && (
        <div className="tw-mb-4 tw-rounded-lg tw-bg-red-50 tw-px-4 tw-py-3 tw-text-sm tw-text-red-800">{error}</div>
      )}

      <div className="tw-grid tw-grid-cols-1 tw-gap-4 lg:tw-grid-cols-3">
        <div className="tw-rounded-xl tw-border tw-border-slate-200 tw-bg-white tw-p-4 tw-shadow-sm lg:tw-col-span-1">
          <div className="tw-mb-3 tw-flex tw-items-center tw-justify-between tw-gap-2">
            <h2 className="tw-text-sm tw-font-semibold tw-text-slate-700">Daftar chat</h2>
            <button
              type="button"
              onClick={() => socketRef.current?.emit('admin_list_chats', {})}
              className="tw-rounded-md tw-border tw-border-slate-200 tw-bg-white tw-px-2 tw-py-1 tw-text-xs tw-text-slate-600 hover:tw-bg-slate-50"
            >
              Muat ulang
            </button>
          </div>
          <div className="tw-max-h-[65vh] tw-space-y-2 tw-overflow-y-auto tw-pr-1">
            {chats.length === 0 && (
              <p className="tw-text-sm tw-text-slate-400">Belum ada chat</p>
            )}
            {chats.map((c) => {
              const isActive = active?.session_id === c.session_id
              return (
                <button
                  key={c.session_id}
                  type="button"
                  onClick={() => {
                    setActive(c)
                    if (c.status !== 'pending' && Number(c.unread) > 0) {
                      socketRef.current?.emit('admin_mark_read', { session_id: c.session_id })
                    }
                  }}
                  className={`tw-w-full tw-rounded-lg tw-border tw-p-3 tw-text-left tw-text-sm hover:tw-bg-slate-50 ${
                    isActive ? 'tw-border-blue-200 tw-bg-blue-50' : 'tw-border-slate-100 tw-bg-white'
                  }`}
                >
                  <div className="tw-flex tw-items-start tw-justify-between tw-gap-2">
                    <div className="tw-min-w-0">
                      <p className="tw-truncate tw-font-semibold tw-text-slate-800">{c.guest_name}</p>
                      <p className="tw-text-xs tw-text-slate-500">{c.visitor_phone}</p>
                    </div>
                    <div className="tw-flex tw-items-center tw-gap-2">
                      {Number(c.unread) > 0 && (
                        <span className="tw-rounded-full tw-bg-rose-600 tw-px-2 tw-py-0.5 tw-text-[10px] tw-font-semibold tw-text-white">
                          {c.unread}
                        </span>
                      )}
                      <button
                        type="button"
                        onClick={(e) => {
                          e.preventDefault()
                          e.stopPropagation()
                          deleteChat(c)
                        }}
                        className="tw-rounded-md tw-border tw-border-slate-200 tw-bg-white tw-px-2 tw-py-1 tw-text-[10px] tw-text-slate-600 hover:tw-bg-slate-50"
                      >
                        Hapus
                      </button>
                    </div>
                  </div>
                  <p className="tw-mt-1 tw-text-xs tw-text-blue-600">{c.category}</p>
                  <p className="tw-mt-2 tw-line-clamp-2 tw-text-slate-600">
                    {c.last_message || c.message_preview || ''}
                  </p>
                  {c.status === 'pending' && (
                    <div className="tw-mt-3 tw-flex tw-gap-2">
                      <span className="tw-rounded-md tw-bg-amber-100 tw-px-2 tw-py-1 tw-text-[10px] tw-font-semibold tw-text-amber-800">
                        Belum diterima
                      </span>
                      <button
                        type="button"
                        onClick={(e) => {
                          e.preventDefault()
                          e.stopPropagation()
                          accept(c)
                        }}
                        className="tw-rounded-lg tw-bg-emerald-600 tw-px-3 tw-py-1.5 tw-text-xs tw-font-medium tw-text-white hover:tw-bg-emerald-500"
                      >
                        Terima
                      </button>
                      <button
                        type="button"
                        onClick={(e) => {
                          e.preventDefault()
                          e.stopPropagation()
                          reject(c)
                        }}
                        className="tw-rounded-lg tw-bg-slate-200 tw-px-3 tw-py-1.5 tw-text-xs tw-font-medium tw-text-slate-700 hover:tw-bg-slate-300"
                      >
                        Tolak
                      </button>
                    </div>
                  )}
                </button>
              )
            })}
          </div>
        </div>

        <div className="tw-flex tw-min-h-[420px] tw-flex-col tw-rounded-xl tw-border tw-border-slate-200 tw-bg-white tw-shadow-sm lg:tw-col-span-2">
          {!active && (
            <div className="tw-flex tw-flex-1 tw-items-center tw-justify-center tw-p-8 tw-text-center tw-text-slate-400">
              Pilih &quot;Terima&quot; pada permintaan untuk mulai chat
            </div>
          )}
          {active && (
            <>
              <div className="tw-flex tw-items-center tw-justify-between tw-border-b tw-border-slate-100 tw-bg-gradient-to-r tw-from-blue-600 tw-to-sky-600 tw-px-4 tw-py-3">
                <div>
                  <p className="tw-text-sm tw-font-semibold tw-text-white">{active.guest_name}</p>
                  <p className="tw-text-xs tw-text-white/80">{active.visitor_phone}</p>
                </div>
                <button
                  type="button"
                  onClick={endSession}
                  className="tw-rounded-lg tw-bg-white/20 tw-px-3 tw-py-1.5 tw-text-xs tw-font-medium tw-text-white hover:tw-bg-white/30"
                >
                  Akhiri sesi
                </button>
              </div>
              <div ref={listRef} className="tw-flex-1 tw-space-y-3 tw-overflow-y-auto tw-bg-slate-50 tw-p-4">
                {messages.map((m, i) => (
                  <div
                    key={`${m.created_at}-${i}`}
                    className={`tw-flex ${m.sender === 'admin' ? 'tw-justify-end' : 'tw-justify-start'}`}
                  >
                    <div
                      className={`tw-max-w-[85%] tw-rounded-2xl tw-px-3 tw-py-2 tw-text-sm ${
                        m.sender === 'admin'
                          ? 'tw-bg-blue-600 tw-text-white'
                          : 'tw-bg-white tw-text-slate-800 tw-shadow-sm'
                      }`}
                    >
                      <p className="tw-whitespace-pre-wrap">{m.body}</p>
                      <p className="tw-mt-1 tw-text-[10px] tw-opacity-70">
                        {m.created_at
                          ? new Date(m.created_at).toLocaleTimeString('id-ID', {
                              hour: '2-digit',
                              minute: '2-digit',
                            })
                          : ''}
                      </p>
                    </div>
                  </div>
                ))}
                {typingFrom === 'guest' && (
                  <p className="tw-text-xs tw-italic tw-text-slate-400">Tamu mengetik…</p>
                )}
              </div>
              <div className="tw-border-t tw-border-slate-100 tw-p-3">
                <div className="tw-flex tw-gap-2">
                  <input
                    className="tw-min-w-0 tw-flex-1 tw-rounded-xl tw-border tw-border-slate-200 tw-bg-white tw-px-3 tw-py-2 tw-text-sm"
                    value={input}
                    onChange={(e) => onInputChange(e.target.value)}
                    onKeyDown={(e) => e.key === 'Enter' && !e.shiftKey && (e.preventDefault(), sendMessage())}
                    placeholder="Balas tamu…"
                  />
                  <button
                    type="button"
                    onClick={sendMessage}
                    className="tw-rounded-xl tw-bg-blue-600 tw-px-4 tw-py-2 tw-text-sm tw-font-medium tw-text-white hover:tw-bg-blue-500"
                  >
                    Kirim
                  </button>
                </div>
              </div>
            </>
          )}
        </div>
      </div>
    </div>
  )
}
