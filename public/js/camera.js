document.addEventListener('DOMContentLoaded', function () {
    const openCameraBtn = document.getElementById('openCameraBtn');
    const cameraContainer = document.getElementById('cameraContainer');
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureBtn = document.getElementById('captureBtn');
    const closeCameraBtn = document.getElementById('closeCameraBtn');
    const fotoInput = document.getElementById('Foto');
    let stream = null;

    if (!openCameraBtn || !cameraContainer || !video || !canvas || !captureBtn || !closeCameraBtn || !fotoInput) return;

    function showError(msg) {
        try { alert(msg); } catch (_) { }
    }

    async function startCamera() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showError('Kamera tidak didukung oleh browser ini. Coba gunakan Chrome/Edge/Firefox terbaru.');
            return false;
        }
        try {
            // Gunakan ideal agar tidak error jika kamera belakang tidak tersedia
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: { ideal: 'environment' } },
                audio: false
            });
            video.srcObject = stream;
            return true;
        } catch (e) {
            try {
                // Fallback generic video
                stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                video.srcObject = stream;
                return true;
            } catch (err) {
                console.error('Camera access error:', err);
                let reason = 'Tidak dapat mengakses kamera. Pastikan izin kamera diberikan.';
                if (err && err.name === 'NotAllowedError') reason = 'Akses kamera ditolak. Mohon izinkan akses kamera di browser.';
                if (err && (err.name === 'NotFoundError' || err.name === 'OverconstrainedError')) reason = 'Kamera tidak ditemukan pada perangkat ini.';
                showError(reason);
                return false;
            }
        }
    }

    openCameraBtn.addEventListener('click', async function () {
        cameraContainer.style.display = 'block';
        openCameraBtn.style.display = 'none';
        const ok = await startCamera();
        if (!ok) {
            // pulihkan UI jika gagal
            cameraContainer.style.display = 'none';
            openCameraBtn.style.display = 'inline-block';
        }
    });

    captureBtn.addEventListener('click', function () {
        if (!video.srcObject) return;
        // Ambil gambar dari video ke canvas
        canvas.width = video.videoWidth || 1280;
        canvas.height = video.videoHeight || 720;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

        // Konversi canvas ke blob lalu ke File dan set ke input file
        canvas.toBlob(function (blob) {
            if (!blob) return;
            const file = new File([blob], 'camera-capture.jpg', { type: 'image/jpeg' });
            const dt = new DataTransfer();
            dt.items.add(file);
            fotoInput.files = dt.files;
        }, 'image/jpeg', 0.95);

        // Sembunyikan kamera, tampilkan tombol ambil lagi
        stopCamera();
    });

    closeCameraBtn.addEventListener('click', function () {
        stopCamera();
    });

    function stopCamera() {
        if (stream) {
            try { stream.getTracks().forEach(track => track.stop()); } catch(_) {}
            stream = null;
        }
        try { video.srcObject = null; } catch(_) {}
        cameraContainer.style.display = 'none';
        openCameraBtn.style.display = 'inline-block';
    }

    // Pastikan stream ditutup saat berpindah halaman/tab
    window.addEventListener('pagehide', stopCamera);
    window.addEventListener('beforeunload', stopCamera);
});