import { useEffect, useMemo, useState } from 'react'
import { motion } from 'framer-motion'

function formatDateTime(d) {
  return new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
  }).format(d)
}

function timeGreeting(hour) {
  if (hour < 11) return { text: 'Selamat pagi', emoji: '☀️' }
  if (hour < 15) return { text: 'Selamat siang', emoji: '' }
  if (hour < 18) return { text: 'Selamat sore', emoji: '' }
  return { text: 'Selamat malam', emoji: '🌙' }
}

export default function HeaderSection() {
  const [now, setNow] = useState(() => new Date())

  useEffect(() => {
    const t = setInterval(() => setNow(new Date()), 1000)
    return () => clearInterval(t)
  }, [])

  const greeting = useMemo(() => timeGreeting(now.getHours()), [now])

  return (
    <motion.header
      className="tw-mx-auto tw-mb-8 tw-max-w-6xl tw-px-4 tw-pt-6 sm:tw-pt-8"
      initial={{ opacity: 0, y: 24 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.55, ease: [0.22, 1, 0.36, 1] }}
    >
      <div className="tw-relative tw-overflow-hidden tw-rounded-2xl tw-bg-gradient-to-br tw-from-blue-600 tw-via-blue-500 tw-to-slate-200 tw-px-8 tw-py-12 tw-shadow-md sm:tw-px-12 sm:tw-py-14">
        <div className="tw-pointer-events-none tw-absolute tw-inset-0 tw-bg-gradient-to-tr tw-from-white/10 tw-to-transparent" />
        <div className="tw-relative tw-text-center">
          <p className="tw-mb-2 tw-text-sm tw-font-medium tw-text-white/90 sm:tw-text-base">
            {greeting.text} {greeting.emoji}
          </p>
          <h1 className="tw-mb-3 tw-text-3xl tw-font-bold tw-tracking-tight tw-text-white sm:tw-text-4xl md:tw-text-5xl">
            Selamat Datang
          </h1>
          <p className="tw-mb-8 tw-text-base tw-text-white/95 sm:tw-text-lg">
            Silakan pilih layanan yang Anda butuhkan
          </p>
          <div className="tw-inline-flex tw-items-center tw-rounded-full tw-bg-white/20 tw-px-4 tw-py-2 tw-text-sm tw-font-medium tw-text-white tw-backdrop-blur-sm sm:tw-text-base">
            <span className="tw-mr-2 tw-opacity-90">🕐</span>
            <time dateTime={now.toISOString()}>{formatDateTime(now)}</time>
          </div>
        </div>
      </div>
    </motion.header>
  )
}
