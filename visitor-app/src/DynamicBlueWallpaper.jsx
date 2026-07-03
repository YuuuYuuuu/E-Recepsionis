import { motion } from 'framer-motion'

const blobTransition = {
  duration: 7,
  repeat: Infinity,
  ease: 'easeInOut',
}

const blobTransitionAlt = {
  duration: 8,
  repeat: Infinity,
  ease: 'easeInOut',
}

export default function DynamicBlueWallpaper() {
  return (
    <div
      aria-hidden
      className="tw-pointer-events-none tw-fixed tw-inset-0 tw-z-0 tw-overflow-hidden"
    >
      <motion.div
        className="tw-absolute tw-inset-0 tw-bg-gradient-to-br tw-from-[#030712] tw-via-[#0c4a6e] tw-to-[#0e3a5f]"
        animate={{
          opacity: [0.92, 1, 0.88, 0.95, 0.92],
        }}
        transition={{ duration: 5, repeat: Infinity, ease: 'easeInOut' }}
      />

      <motion.div
        className="tw-absolute -tw-left-[15%] -tw-top-[20%] tw-h-[min(85vw,720px)] tw-w-[min(85vw,720px)] tw-rounded-full tw-bg-sky-400/40 tw-blur-[72px] tw-will-change-transform"
        animate={{
          x: [0, 140, -90, 70, -40, 0],
          y: [0, 90, 120, -70, 50, 0],
          scale: [1, 1.22, 1.08, 1.18, 1.05, 1],
        }}
        transition={blobTransition}
      />
      <motion.div
        className="tw-absolute -tw-right-[10%] tw-top-[10%] tw-h-[min(75vw,640px)] tw-w-[min(75vw,640px)] tw-rounded-full tw-bg-blue-500/38 tw-blur-[80px] tw-will-change-transform"
        animate={{
          x: [0, -130, 75, -55, 40, 0],
          y: [0, 100, -85, 65, -45, 0],
          scale: [1, 1.15, 1.25, 1.1, 1.12, 1],
        }}
        transition={blobTransitionAlt}
      />
      <motion.div
        className="tw-absolute tw-left-[20%] tw-bottom-[-15%] tw-h-[min(90vw,680px)] tw-w-[min(90vw,680px)] tw-rounded-full tw-bg-sky-500/32 tw-blur-[68px] tw-will-change-transform"
        animate={{
          x: [0, -120, 95, -70, 55, 0],
          y: [0, -100, 70, -55, 80, 0],
          scale: [1.05, 1.12, 0.98, 1.2, 1.08, 1.05],
        }}
        transition={{
          duration: 6.5,
          repeat: Infinity,
          ease: 'easeInOut',
        }}
      />
      <motion.div
        className="tw-absolute tw-left-1/2 tw-top-1/2 tw-h-[min(100vw,900px)] tw-w-[min(100vw,900px)] -tw-translate-x-1/2 -tw-translate-y-1/2 tw-rounded-full tw-bg-cyan-300/18 tw-blur-[90px] tw-will-change-transform"
        animate={{
          rotate: [0, 360],
          scale: [1, 1.14, 1],
        }}
        transition={{
          rotate: { duration: 22, repeat: Infinity, ease: 'linear' },
          scale: { duration: 5, repeat: Infinity, ease: 'easeInOut' },
        }}
      />

      <motion.div
        className="tw-absolute tw-inset-0 tw-opacity-[0.45]"
        style={{
          backgroundImage:
            'linear-gradient(rgba(255,255,255,0.055) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.055) 1px, transparent 1px)',
          backgroundSize: '48px 48px',
        }}
        animate={{ backgroundPosition: ['0px 0px', '48px 48px'] }}
        transition={{ duration: 6, repeat: Infinity, ease: 'linear' }}
      />

      <motion.div
        className="tw-absolute tw-inset-0 tw-bg-gradient-to-t tw-from-slate-950/35 tw-via-transparent tw-to-blue-600/25"
        animate={{ opacity: [0.72, 1, 0.8, 0.92, 0.72] }}
        transition={{ duration: 4, repeat: Infinity, ease: 'easeInOut' }}
      />
    </div>
  )
}
