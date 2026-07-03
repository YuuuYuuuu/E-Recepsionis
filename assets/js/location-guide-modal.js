/**
 * Zoom/pan + overlay sync untuk modal Petunjuk Lokasi (visitor).
 * Inisialisasi: LocationGuideModal.init(config)
 */
(function (global) {
  'use strict';

  function LocationGuideModal(config) {
    this.config = config || {};
    this.scale = 1;
    this.translateX = 0;
    this.translateY = 0;
    this.isDragging = false;
    this.dragStart = { x: 0, y: 0, tx: 0, ty: 0 };
    this.minScale = 0.5;
    this.maxScale = 4;

    this.viewport = document.getElementById(this.config.viewportId || 'locationMapViewport');
    this.stage = document.getElementById(this.config.stageId || 'locationMapStage');
    this.inner = document.getElementById(this.config.innerId || 'locationMapInner');
    this.img = document.getElementById(this.config.imageId || 'locationMapImage');

    if (!this.viewport || !this.stage) {
      return;
    }

    this.bindControls();
    this.bindPanZoom();
    this.fitImageToViewport();
    this.resetView();
  }

  LocationGuideModal.prototype.fitImageToViewport = function () {
    const self = this;
    if (!this.img || !this.inner) return;

    const apply = function () {
      const nw = self.img.naturalWidth || self.img.width;
      const nh = self.img.naturalHeight || self.img.height;
      if (!nw || !nh) return;
      const vw = self.viewport.clientWidth;
      const vh = self.viewport.clientHeight;
      const fit = Math.min(vw / nw, vh / nh, 1);
      const w = Math.max(nw * fit, 200);
      self.inner.style.width = w + 'px';
      self.img.style.width = '100%';
    };

    if (this.img.complete) {
      apply();
    } else {
      this.img.addEventListener('load', apply, { once: true });
    }
  };

  LocationGuideModal.prototype.applyTransform = function () {
    this.stage.style.transform =
      'translate(calc(-50% + ' + this.translateX + 'px), calc(-50% + ' + this.translateY + 'px)) scale(' + this.scale + ')';
  };

  LocationGuideModal.prototype.resetView = function () {
    this.scale = 1;
    this.translateX = 0;
    this.translateY = 0;
    this.applyTransform();
  };

  LocationGuideModal.prototype.zoomBy = function (delta) {
    const next = Math.max(this.minScale, Math.min(this.maxScale, this.scale + delta));
    this.scale = next;
    this.applyTransform();
  };

  LocationGuideModal.prototype.bindControls = function () {
    const self = this;
    const zoomIn = document.getElementById('locationZoomIn');
    const zoomOut = document.getElementById('locationZoomOut');
    const zoomReset = document.getElementById('locationZoomReset');
    const fullscreen = document.getElementById('locationFullscreen');

    if (zoomIn) {
      zoomIn.addEventListener('click', function () {
        self.zoomBy(0.25);
      });
    }
    if (zoomOut) {
      zoomOut.addEventListener('click', function () {
        self.zoomBy(-0.25);
      });
    }
    if (zoomReset) {
      zoomReset.addEventListener('click', function () {
        self.resetView();
      });
    }
    if (fullscreen) {
      fullscreen.addEventListener('click', function () {
        const modal = document.getElementById('locationGuideModal');
        if (modal) {
          modal.classList.toggle('modal-fullscreen-map');
        }
        setTimeout(function () {
          self.resetView();
        }, 100);
      });
    }
  };

  LocationGuideModal.prototype.bindPanZoom = function () {
    const self = this;

    this.viewport.addEventListener(
      'wheel',
      function (e) {
        if (!self.inner) return;
        e.preventDefault();
        const delta = e.deltaY > 0 ? -0.15 : 0.15;
        self.zoomBy(delta);
      },
      { passive: false }
    );

    this.viewport.addEventListener('pointerdown', function (e) {
      if (e.button !== 0) return;
      self.isDragging = true;
      self.viewport.classList.add('is-dragging');
      self.dragStart = {
        x: e.clientX,
        y: e.clientY,
        tx: self.translateX,
        ty: self.translateY,
      };
      self.viewport.setPointerCapture(e.pointerId);
    });

    this.viewport.addEventListener('pointermove', function (e) {
      if (!self.isDragging) return;
      self.translateX = self.dragStart.tx + (e.clientX - self.dragStart.x);
      self.translateY = self.dragStart.ty + (e.clientY - self.dragStart.y);
      self.applyTransform();
    });

    const endDrag = function (e) {
      if (!self.isDragging) return;
      self.isDragging = false;
      self.viewport.classList.remove('is-dragging');
      try {
        self.viewport.releasePointerCapture(e.pointerId);
      } catch (err) {
        /* ignore */
      }
    };

    this.viewport.addEventListener('pointerup', endDrag);
    this.viewport.addEventListener('pointercancel', endDrag);
  };

  LocationGuideModal.init = function (config) {
    return new LocationGuideModal(config);
  };

  global.LocationGuideModal = LocationGuideModal;
})(typeof window !== 'undefined' ? window : this);
