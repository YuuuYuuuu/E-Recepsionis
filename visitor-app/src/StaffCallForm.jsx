import { useEffect, useState } from 'react'
import { motion, AnimatePresence } from 'framer-motion'
import { getLiveCategoriesUrl } from './getLiveCategoriesUrl.js'
import { getCallStaffUrl } from './getCallStaffUrl.js'

export default function StaffCallForm({ onClose }) {
  const [step, setStep] = useState('form')
  const [categories, setCategories] = useState([])
  const [form, setForm] = useState({
    visitor_name: '',
    visitor_phone: '',
    category_id: '',
    message: '',
  })
  const [error, setError] = useState('')
  const [submitting, setSubmitting] = useState(false)

  useEffect(() => {
    const url = getLiveCategoriesUrl()
    fetch(url, { credentials: 'same-origin' })
      .then(async (r) => {
        const text = await r.text()
        let d
        try {
          d = JSON.parse(text)
        } catch {
          throw new Error('invalid_json')
        }
        if (!r.ok) throw new Error('http_' + r.status)
        if (!d.success || !Array.isArray(d.categories)) {
          throw new Error('bad_payload')
        }
        return d.categories
      })
      .then((list) => {
        setCategories(list)
        if (list.length === 0) {
          setError('Belum ada kategori aktif. Hubungi admin atau coba lagi nanti.')
        } else {
          setError('')
        }
      })
      .catch(() => {
        setError('Gagal memuat kategori. Periksa koneksi atau jalankan migrasi tabel complaint_categories.')
      })
  }, [])

  const submitForm = async (e) => {
    e.preventDefault()
    setError('')
    setSubmitting(true)
    try {
      const body = new FormData()
      body.append('visitor_name', form.visitor_name.trim())
      body.append('visitor_phone', form.visitor_phone.trim())
      body.append('category_id', String(form.category_id))
      body.append('message', form.message.trim())

      const res = await fetch(getCallStaffUrl(), {
        method: 'POST',
        body,
        credentials: 'same-origin',
      })
      const text = await res.text()
      let data
      try {
        data = JSON.parse(text)
      } catch {
        throw new Error('invalid_json')
      }
      if (!data.success) {
        setError(data.message || 'Gagal mengirim panggilan staff.')
        return
      }
      setStep('success')
    } catch {
      setError('Koneksi gagal. Periksa jaringan lalu coba lagi.')
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <motion.div
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      exit={{ opacity: 0 }}
      className="tw-fixed tw-inset-0 tw-z-[1000] tw-flex tw-items-center tw-justify-center tw-bg-slate-950/90 tw-p-4 tw-backdrop-blur-sm"
    >
      <motion.div
        layout
        className="tw-flex tw-h-[min(92vh,720px)] tw-w-full tw-max-w-lg tw-flex-col tw-overflow-hidden tw-rounded-2xl tw-border tw-border-slate-700 tw-bg-slate-900 tw-shadow-2xl"
      >
        <div className="tw-flex tw-items-center tw-justify-between tw-border-b tw-border-slate-700 tw-bg-gradient-to-r tw-from-blue-600 tw-to-sky-600 tw-px-4 tw-py-3">
          <div className="tw-flex tw-items-center tw-gap-3">
            <div className="tw-flex tw-h-11 tw-w-11 tw-items-center tw-justify-center tw-rounded-xl tw-bg-white/20 tw-text-xl">
              📞
            </div>
            <div>
              <p className="tw-text-sm tw-font-semibold tw-text-white">Panggil Staff</p>
              <p className="tw-text-xs tw-text-white/80">
                {step === 'form' && 'Isi formulir, admin akan dihubungi via WhatsApp'}
                {step === 'success' && 'Permintaan terkirim'}
              </p>
            </div>
          </div>
          <motion.button
            type="button"
            onClick={onClose}
            whileHover={{ scale: 1.08 }}
            whileTap={{ scale: 0.92 }}
            className="live-support-btn-close tw-flex tw-h-9 tw-w-9 tw-items-center tw-justify-center tw-rounded-full tw-transition-colors"
            aria-label="Tutup"
          >
            <svg className="tw-h-4 tw-w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden>
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M6 18L18 6M6 6l12 12" />
            </svg>
          </motion.button>
        </div>

        <AnimatePresence mode="wait">
          {step === 'form' && (
            <motion.form
              key="form"
              initial={{ opacity: 0, x: 20 }}
              animate={{ opacity: 1, x: 0 }}
              exit={{ opacity: 0, x: -20 }}
              onSubmit={submitForm}
              className="tw-flex tw-flex-1 tw-flex-col tw-gap-3 tw-overflow-y-auto tw-p-4"
            >
              {error && (
                <div className="tw-rounded-lg tw-bg-red-500/15 tw-px-3 tw-py-2 tw-text-sm tw-text-red-200">{error}</div>
              )}
              <label className="tw-block tw-text-xs tw-font-medium tw-text-slate-400">Nama</label>
              <input
                className="tw-w-full tw-rounded-xl tw-border tw-border-slate-600 tw-bg-slate-800 tw-px-3 tw-py-2 tw-text-sm tw-text-white placeholder:tw-text-slate-500"
                value={form.visitor_name}
                onChange={(e) => setForm((f) => ({ ...f, visitor_name: e.target.value }))}
                placeholder="Nama lengkap"
                required
              />
              <label className="tw-block tw-text-xs tw-font-medium tw-text-slate-400">No. Telepon</label>
              <input
                className="tw-w-full tw-rounded-xl tw-border tw-border-slate-600 tw-bg-slate-800 tw-px-3 tw-py-2 tw-text-sm tw-text-white placeholder:tw-text-slate-500"
                value={form.visitor_phone}
                onChange={(e) => setForm((f) => ({ ...f, visitor_phone: e.target.value }))}
                placeholder="08xxxxxxxxxx"
                required
              />
              <label className="tw-block tw-text-xs tw-font-medium tw-text-slate-400">Kategori Pengaduan</label>
              <select
                className="tw-w-full tw-rounded-xl tw-border tw-border-slate-600 tw-bg-slate-800 tw-px-3 tw-py-2 tw-text-sm tw-text-white"
                value={form.category_id}
                onChange={(e) => setForm((f) => ({ ...f, category_id: e.target.value }))}
                required
              >
                <option value="">— Pilih —</option>
                {categories.map((c) => (
                  <option key={c.id} value={c.id}>
                    {c.nama_kategori}
                  </option>
                ))}
              </select>
              <label className="tw-block tw-text-xs tw-font-medium tw-text-slate-400">Keperluan</label>
              <textarea
                className="tw-min-h-[100px] tw-w-full tw-rounded-xl tw-border tw-border-slate-600 tw-bg-slate-800 tw-px-3 tw-py-2 tw-text-sm tw-text-white placeholder:tw-text-slate-500"
                value={form.message}
                onChange={(e) => setForm((f) => ({ ...f, message: e.target.value }))}
                placeholder="Jelaskan keperluan Anda…"
                required
              />
              <motion.button
                type="submit"
                disabled={submitting}
                whileHover={submitting ? undefined : { scale: 1.02, y: -1 }}
                whileTap={submitting ? undefined : { scale: 0.98 }}
                className="live-support-btn-primary tw-mt-3 tw-flex tw-w-full tw-items-center tw-justify-center tw-gap-2.5 tw-rounded-2xl tw-py-3.5 tw-text-sm tw-font-bold tw-tracking-wide tw-transition-transform"
              >
                {submitting ? (
                  <>
                    <span className="tw-h-4 tw-w-4 tw-animate-spin tw-rounded-full tw-border-2 tw-border-white/30 tw-border-t-white" />
                    Mengirim…
                  </>
                ) : (
                  <>
                    <svg className="tw-h-5 tw-w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden>
                      <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth={2}
                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                      />
                    </svg>
                    Kirim Panggilan Staff
                  </>
                )}
              </motion.button>
            </motion.form>
          )}

          {step === 'success' && (
            <motion.div
              key="success"
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              className="tw-flex tw-flex-1 tw-flex-col tw-items-center tw-justify-center tw-gap-4 tw-p-8 tw-text-center"
            >
              <div className="tw-text-5xl">✅</div>
              <p className="tw-text-slate-200">Panggilan staff berhasil dikirim.</p>
              <p className="tw-text-sm tw-text-slate-400">
                Operator kategori yang dipilih akan menerima notifikasi WhatsApp dan menindaklanjuti sesuai keperluan Anda.
              </p>
              <button
                type="button"
                onClick={onClose}
                className="live-support-btn-primary tw-mt-2 tw-rounded-2xl tw-px-6 tw-py-2.5 tw-text-sm tw-font-semibold"
              >
                Tutup
              </button>
            </motion.div>
          )}
        </AnimatePresence>
      </motion.div>
    </motion.div>
  )
}
