import { Html5Qrcode, Html5QrcodeSupportedFormats } from 'html5-qrcode';

document.addEventListener('DOMContentLoaded', function () {
    const scannerEl = document.getElementById('scanner');
    const scannerContainer = document.getElementById('scanner-container');
    const detectHighlight = document.getElementById('detect-highlight');
    const scanFlash = document.getElementById('scan-flash');
    const scanStatus = document.getElementById('scan-status');
    const manualCode = document.getElementById('manual-code');
    const manualLookupBtn = document.getElementById('manual-lookup-btn');
    const resultModal = document.getElementById('result-modal');
    const closeModal = document.getElementById('close-modal');
    const receiveBtn = document.getElementById('receive-btn');
    const modeQr = document.getElementById('mode-qr');
    const modeBarcode = document.getElementById('mode-barcode');

    let currentDocumentId = null;
    let html5QrCode = null;
    let scanningPaused = false;
    let currentMode = 'qr';

    function getFormats() {
        if (currentMode === 'qr') {
            return [Html5QrcodeSupportedFormats.QR_CODE];
        }
        return [
            Html5QrcodeSupportedFormats.CODE_128,
            Html5QrcodeSupportedFormats.CODE_39,
            Html5QrcodeSupportedFormats.CODE_93,
            Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.EAN_8,
            Html5QrcodeSupportedFormats.UPC_A,
            Html5QrcodeSupportedFormats.UPC_E,
            Html5QrcodeSupportedFormats.ITF,
            Html5QrcodeSupportedFormats.CODABAR,
            Html5QrcodeSupportedFormats.RSS_14,
            Html5QrcodeSupportedFormats.DATA_MATRIX,
        ];
    }

    function getVideoDimensions() {
        try {
            const videos = document.querySelectorAll('video');
            for (const v of videos) {
                if (v.videoWidth > 0 && v.videoHeight > 0) {
                    return { width: v.videoWidth, height: v.videoHeight };
                }
            }
        } catch (e) {}
        const cr = scannerContainer.getBoundingClientRect();
        return { width: cr.width, height: cr.height };
    }

    function getVideoDisplaySize() {
        try {
            const videos = document.querySelectorAll('video');
            for (const v of videos) {
                if (v.offsetWidth > 0 && v.offsetHeight > 0) {
                    return { width: v.offsetWidth, height: v.offsetHeight };
                }
            }
        } catch (e) {}
        const cr = scannerContainer.getBoundingClientRect();
        return { width: cr.width, height: cr.height };
    }

    function showDetectHighlight(corners) {
        if (!corners || corners.length < 4) {
            showFlash();
            return;
        }

        const containerRect = scannerContainer.getBoundingClientRect();
        const videoSize = getVideoDimensions();
        const displaySize = getVideoDisplaySize();

        const xs = corners.map(p => p.x);
        const ys = corners.map(p => p.y);
        const minX = Math.min(...xs);
        const maxX = Math.max(...xs);
        const minY = Math.min(...ys);
        const maxY = Math.max(...ys);

        const scaleX = displaySize.width / videoSize.width;
        const scaleY = displaySize.height / videoSize.height;

        const ox = (containerRect.width - displaySize.width) / 2;
        const oy = (containerRect.height - displaySize.height) / 2;

        let left = minX * scaleX + ox;
        let top = minY * scaleY + oy;
        let width = (maxX - minX) * scaleX;
        let height = (maxY - minY) * scaleY;

        if (width < 10 || height < 10 || left < -50 || top < -50 || left > containerRect.width + 50 || top > containerRect.height + 50) {
            width = Math.min(containerRect.width * 0.5, 180);
            height = width;
            left = (containerRect.width - width) / 2;
            top = (containerRect.height - height) / 2;
        }

        const padding = 6;
        detectHighlight.style.left = (left - padding) + 'px';
        detectHighlight.style.top = (top - padding) + 'px';
        detectHighlight.style.width = (width + padding * 2) + 'px';
        detectHighlight.style.height = (height + padding * 2) + 'px';
        detectHighlight.style.opacity = '1';
        detectHighlight.style.transform = 'scale(1)';
        detectHighlight.classList.remove('hidden');

        requestAnimationFrame(() => {
            detectHighlight.style.transition = 'all 0.4s ease-out';
            const cx = (containerRect.width - (width + padding * 2)) / 2;
            const cy = (containerRect.height - (height + padding * 2)) / 2;
            detectHighlight.style.left = cx + 'px';
            detectHighlight.style.top = cy + 'px';
            detectHighlight.style.transform = 'scale(1.2)';
            detectHighlight.style.opacity = '0';
        });
    }

    function showFlash() {
        scanFlash.classList.remove('hidden');
        setTimeout(() => scanFlash.classList.add('hidden'), 600);
    }

    async function startScanner() {
        if (html5QrCode) {
            try { await html5QrCode.stop(); } catch (e) {}
            html5QrCode.clear();
        }

        html5QrCode = new Html5Qrcode('scanner');

        const loading = document.getElementById('scanner-loading');
        detectHighlight.classList.add('hidden');
        detectHighlight.style.transition = 'none';
        detectHighlight.style.transform = '';
        detectHighlight.style.opacity = '';
        scanFlash.classList.add('hidden');
        if (loading) loading.classList.remove('hidden');

        try {
            await html5QrCode.start(
                { facingMode: 'environment' },
                { fps: 24, formatsToSupport: getFormats() },
                onScanSuccess,
                onScanFailure
            );
            if (loading) loading.classList.add('hidden');
        } catch (err) {
            if (loading) loading.textContent = 'Camera access denied or unavailable. Enter the code manually below.';
        }
    }

    function onScanSuccess(decodedText, decodedResult) {
        if (scanningPaused) return;

        const detectedFormat = decodedResult?.format?.formatName || '';
        if (detectedFormat) {
            const isQr = /qr/i.test(detectedFormat);
            if (currentMode === 'qr' && !isQr) return;
            if (currentMode === 'barcode' && isQr) return;
        }

        scanningPaused = true;
        if (html5QrCode) html5QrCode.pause();

        let corners = null;
        const res = decodedResult?.result;

        if (res?.location) {
            const loc = res.location;
            corners = [loc.topLeft, loc.topRight, loc.bottomRight, loc.bottomLeft];
        } else if (res?.cornerPoints) {
            corners = res.cornerPoints;
        } else if (res?.boundingBox) {
            const b = res.boundingBox;
            corners = [
                { x: b.left, y: b.top },
                { x: b.right, y: b.top },
                { x: b.right, y: b.bottom },
                { x: b.left, y: b.bottom },
            ];
        }

        if (corners && corners.length >= 4) {
            showDetectHighlight(corners);
        } else {
            showFlash();
        }

        setTimeout(() => {
            lookupDocument(decodedText);
        }, 500);
    }

    function onScanFailure() {}

    function lookupDocument(code) {
        scanStatus.textContent = 'Looking up document...';
        scanStatus.className = 'mt-3 text-sm text-gray-500';

        fetch('/system/documents/receive/lookup', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({ code })
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                scanStatus.textContent = data.error;
                scanStatus.className = 'mt-3 text-sm text-red-600';
                resumeScanning();
                return;
            }
            scanStatus.textContent = '';
            showDocument(data);
        })
        .catch(() => {
            scanStatus.textContent = 'An error occurred. Please try again.';
            scanStatus.className = 'mt-3 text-sm text-red-600';
            resumeScanning();
        });
    }

    function resumeScanning() {
        detectHighlight.classList.add('hidden');
        detectHighlight.style.transition = 'none';
        detectHighlight.style.transform = '';
        detectHighlight.style.opacity = '';
        scanFlash.classList.add('hidden');
        if (html5QrCode) html5QrCode.resume();
        scanningPaused = false;
    }

    function showDocument(data) {
        const doc = data.document;
        currentDocumentId = doc.id;

        document.getElementById('doc-title').textContent = doc.title;
        document.getElementById('doc-type').textContent = 'Type: ' + doc.document_type;
        document.getElementById('doc-creator').textContent = 'Created by: ' + (doc.creator?.firstname || '') + ' ' + (doc.creator?.lastname || '');
        document.getElementById('doc-created-at').textContent = new Date(doc.created_at).toLocaleString();
        document.getElementById('doc-updated-at').textContent = new Date(doc.updated_at).toLocaleString();
        document.getElementById('doc-notes').textContent = doc.notes || 'No notes.';
        document.getElementById('doc-qr-image').src = data.qr_data_url;

        const currentHolderInfo = document.getElementById('current-holder-info');
        if (data.current_holder) {
            const ch = data.current_holder;
            let name = ch.firstname + ' ' + ch.lastname;
            let info = '';
            if (ch.department) info += ch.department.name;
            if (ch.office) info += (info ? ' - ' : '') + ch.office.name;
            currentHolderInfo.innerHTML = '<span class="font-medium">' + name + '</span>' + (info ? '<br><span class="text-indigo-700 text-xs">' + info + '</span>' : '');
        } else {
            currentHolderInfo.innerHTML = '<span class="font-medium text-gray-700">No current holder / Available for receiving.</span>';
        }

        const pastList = document.getElementById('past-holders-list');
        pastList.innerHTML = '';
        if (data.past_tracks && data.past_tracks.length > 0) {
            data.past_tracks.forEach(t => {
                const u = t.user;
                const div = document.createElement('div');
                div.className = 'flex items-center justify-between bg-gray-50 rounded-lg px-4 py-2.5 text-sm';
                let name = u.firstname + ' ' + u.lastname;
                let dept = '';
                if (u.department) dept += u.department.name;
                if (u.office) dept += (dept ? ' - ' : '') + u.office.name;
                div.innerHTML = '<div><span class="font-medium text-gray-900">' + name + '</span>' + (dept ? '<br><span class="text-gray-500 text-xs">' + dept + '</span>' : '') + '</div>' +
                    '<div class="text-gray-500 text-xs text-right">Received: ' + new Date(t.received_at).toLocaleDateString() + '<br>Released: ' + new Date(t.released_at).toLocaleDateString() + '</div>';
                pastList.appendChild(div);
            });
        } else {
            pastList.innerHTML = '<p class="text-sm text-gray-500 italic">No past holders.</p>';
        }

        receiveBtn.disabled = false;
        receiveBtn.textContent = 'Receive This Document';
        resultModal.classList.remove('hidden');
    }

    function switchMode(mode) {
        if (mode === currentMode) return;
        scanningPaused = false;
        currentMode = mode;
        modeQr.className = 'px-4 py-1.5 text-sm font-medium transition-colors ' +
            (mode === 'qr' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100');
        modeBarcode.className = 'px-4 py-1.5 text-sm font-medium transition-colors ' +
            (mode === 'barcode' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100');
        startScanner();
    }

    modeQr.addEventListener('click', function () { switchMode('qr'); });
    modeBarcode.addEventListener('click', function () { switchMode('barcode'); });

    manualLookupBtn.addEventListener('click', function () {
        const code = manualCode.value.trim();
        if (!code) {
            scanStatus.textContent = 'Please enter a QR or barcode value.';
            scanStatus.className = 'mt-3 text-sm text-red-600';
            return;
        }
        lookupDocument(code);
    });

    manualCode.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') manualLookupBtn.click();
    });

    closeModal.addEventListener('click', function () {
        resultModal.classList.add('hidden');
        resumeScanning();
        manualCode.value = '';
    });

    resultModal.addEventListener('click', function (e) {
        if (e.target === resultModal) closeModal.click();
    });

    receiveBtn.addEventListener('click', function () {
        if (!currentDocumentId) return;
        receiveBtn.disabled = true;
        receiveBtn.textContent = 'Receiving...';

        fetch('/system/documents/receive/' + currentDocumentId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                receiveBtn.disabled = false;
                receiveBtn.textContent = 'Receive This Document';
                return;
            }
            alert('Document received successfully!');
            closeModal.click();
            manualCode.value = '';
        })
        .catch(() => {
            alert('An error occurred. Please try again.');
            receiveBtn.disabled = false;
            receiveBtn.textContent = 'Receive This Document';
        });
    });

    startScanner();
});
