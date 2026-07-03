import { useCallback, useEffect, useState } from 'react'
import { MdMeetingRoom, MdSchool, MdPhoneInTalk } from 'react-icons/md'
import DynamicBlueWallpaper from './DynamicBlueWallpaper.jsx'
import HeaderSection from './HeaderSection.jsx'
import ServiceCard from './ServiceCard.jsx'
import VirtualReceptionist from './VirtualReceptionist.jsx'
import StaffCallForm from './StaffCallForm.jsx'

function openBootstrapModal(elementId) {
  const el = document.getElementById(elementId)
  if (el && window.bootstrap?.Modal) {
    window.bootstrap.Modal.getOrCreateInstance(el).show()
    return true
  }
  return false
}

function getVisitorPhpBaseUrl() {
  if (typeof window !== 'undefined' && window.__VISITOR_BASE_URL__) {
    return String(window.__VISITOR_BASE_URL__)
  }
  const envUrl = import.meta.env?.VITE_VISITOR_BASE_URL
  if (envUrl && String(envUrl).trim()) {
    return String(envUrl).trim().replace(/\/?$/, '/')
  }
  if (import.meta.env?.DEV) {
    return 'http://127.0.0.1:8000/Recepsionis/visitor/'
  }
  try {
    const href = new URL('index.php', window.location.href)
    const base = href.href.replace(/index\.php(?:[?#].*)?$/, '')
    return base.endsWith('/') ? base : `${base}/`
  } catch {
    return '/Recepsionis/visitor/'
  }
}

export default function App() {
  const [highlighted, setHighlighted] = useState(null)
  const [staffCallOpen, setStaffCallOpen] = useState(false)

  useEffect(() => {
    if (!highlighted) return undefined
    const t = window.setTimeout(() => setHighlighted(null), 4800)
    return () => window.clearTimeout(t)
  }, [highlighted])

  const activateRooms = useCallback(() => {
    setHighlighted('rooms')
    if (!openBootstrapModal('roomsModal')) {
      window.location.href = `${getVisitorPhpBaseUrl()}index.php?open=rooms`
    }
  }, [])

  const activateProdi = useCallback(() => {
    setHighlighted('prodi')
    window.location.href = `${getVisitorPhpBaseUrl()}prodi.php`
  }, [])

  const activateStaff = useCallback(() => {
    setHighlighted('staff')
    setStaffCallOpen(true)
  }, [])

  return (
    <div className="tw-relative tw-min-h-0 tw-pb-28">
      {staffCallOpen && <StaffCallForm onClose={() => setStaffCallOpen(false)} />}
      <DynamicBlueWallpaper />

      <div className="tw-relative tw-z-10">
        <HeaderSection />

        <div className="tw-mx-auto tw-max-w-6xl tw-px-4">
          <div className="tw-grid tw-grid-cols-1 tw-gap-6 md:tw-grid-cols-2 lg:tw-grid-cols-3">
          <ServiceCard
            id="service-daftar-ruangan"
            theme="green"
            icon={MdMeetingRoom}
            title="Daftar Ruangan"
            description="Lihat informasi ruangan dan lokasinya"
            ctaLabel="Lihat Ruangan"
            highlighted={highlighted === 'rooms'}
            delay={0.08}
            onActivate={activateRooms}
          />
          <ServiceCard
            id="service-program-studi"
            theme="orange"
            icon={MdSchool}
            title="Program Studi"
            description="Lihat daftar program studi yang tersedia di kampus"
            ctaLabel="Lihat Prodi"
            highlighted={highlighted === 'prodi'}
            delay={0.16}
            onActivate={activateProdi}
          />
          <ServiceCard
            id="service-panggil-staff"
            theme="blue"
            icon={MdPhoneInTalk}
            title="Panggil Staff"
            description="Isi formulir keperluan, operator akan dihubungi via WhatsApp"
            ctaLabel="Panggil Staff"
            highlighted={highlighted === 'staff'}
            delay={0.24}
            onActivate={activateStaff}
          />
          </div>
        </div>
      </div>

      <VirtualReceptionist />
    </div>
  )
}
