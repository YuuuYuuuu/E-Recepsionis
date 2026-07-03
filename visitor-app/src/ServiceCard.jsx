import { motion } from 'framer-motion'

const themes = {
  green: {
    iconBg: 'tw-bg-gradient-to-br tw-from-emerald-500 tw-to-green-600',
    cta: 'tw-bg-emerald-600 group-hover:tw-bg-emerald-700 tw-text-white',
    ring: 'tw-ring-emerald-400/80',
    softBorder: 'tw-border-emerald-100',
  },
  orange: {
    iconBg: 'tw-bg-gradient-to-br tw-from-amber-500 tw-to-orange-600',
    cta: 'tw-bg-orange-500 group-hover:tw-bg-orange-600 tw-text-white',
    ring: 'tw-ring-orange-400/80',
    softBorder: 'tw-border-orange-100',
  },
  blue: {
    iconBg: 'tw-bg-gradient-to-br tw-from-blue-500 tw-to-blue-700',
    cta: 'tw-bg-blue-600 group-hover:tw-bg-blue-700 tw-text-white',
    ring: 'tw-ring-blue-400/80',
    softBorder: 'tw-border-blue-100',
  },
}

export default function ServiceCard({
  id,
  theme,
  icon: Icon,
  title,
  description,
  ctaLabel,
  onActivate,
  highlighted,
  delay = 0,
}) {
  const t = themes[theme] || themes.blue

  return (
    <motion.div
      id={id}
      layout
      role="button"
      tabIndex={0}
      initial={{ opacity: 0, y: 20 }}
      animate={{
        opacity: 1,
        y: 0,
        scale: highlighted ? 1.03 : 1,
      }}
      transition={{
        layout: { type: 'spring', stiffness: 300, damping: 28 },
        delay,
        duration: 0.45,
      }}
      className={[
        'group tw-relative tw-flex tw-h-full tw-cursor-pointer tw-flex-col tw-rounded-xl tw-border tw-bg-white tw-p-6 tw-shadow-md tw-outline-none tw-transition-shadow focus-visible:tw-ring-2 focus-visible:tw-ring-blue-600 focus-visible:tw-ring-offset-2',
        highlighted
          ? `tw-ring-2 tw-ring-offset-2 tw-shadow-xl ${t.ring}`
          : 'tw-border-slate-200/80 hover:tw-shadow-xl',
      ].join(' ')}
      style={{
        boxShadow: highlighted
          ? '0 0 0 3px rgba(59, 130, 246, 0.35), 0 20px 40px rgba(15, 23, 42, 0.12)'
          : undefined,
      }}
      whileHover={{ scale: 1.05, transition: { duration: 0.2 } }}
      whileTap={{ scale: 0.97 }}
      onClick={onActivate}
      onKeyDown={(e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault()
          onActivate()
        }
      }}
    >
      <div
        className={`tw-mx-auto tw-mb-5 tw-flex tw-h-20 tw-w-20 tw-items-center tw-justify-center tw-rounded-2xl tw-text-4xl tw-text-white tw-shadow-md ${t.iconBg}`}
      >
        <Icon aria-hidden />
      </div>
      <h3 className="tw-mb-2 tw-text-center tw-text-xl tw-font-bold tw-text-slate-900">{title}</h3>
      <p className="tw-mb-6 tw-flex-1 tw-text-center tw-text-sm tw-leading-relaxed tw-text-slate-600 sm:tw-text-base">
        {description}
      </p>
      <span
        className={`tw-pointer-events-none tw-block tw-w-full tw-rounded-xl tw-py-3.5 tw-text-center tw-text-sm tw-font-semibold tw-shadow-sm tw-transition-colors sm:tw-text-base ${t.cta}`}
      >
        {ctaLabel}
      </span>
    </motion.div>
  )
}
