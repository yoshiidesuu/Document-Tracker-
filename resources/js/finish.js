import { Html5Qrcode, Html5QrcodeSupportedFormats } from 'html5-qrcode';

document.addEventListener('DOMContentLoaded', function () {
    const scannerContainer = document.getElementById('scanner-container');
    const detectHighlight = document.getElementById('detect-highlight');
    const scanFlash = document.getElementById('scan-flash');
    const scanStatus = document.getElementById('scan-status');
    const manualCode = document.getElementById('manual-code');
    const manualLookupBtn = document.getElementById('manual-lookup-btn');
    const resultModal = document.getElementById('result-modal');
    const closeModal = document.getElementById('close-modal');
    const finishBtn = document.getElementById('finish-btn');
    const modeQr = document.getElementById('mode-qr');
    const modeBarcode = document.getElementById('mode-barcode');

    let currentDocumentId = null;
    let html5QrCode = null;
    let scanningPaused = false;
    let currentMode = 'qr';

    function getFormats() {
        if (currentMode === 'qr') return [Html5QrcodeSupportedFormats.QR_CODE];
        return [
            Html5QrcodeSupportedFormats.CODE_128, Html5QrcodeSupportedFormats.CODE_39,
            Html5QrcodeSupportedFormats.CODE_93, Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.EAN_8, Html5QrcodeSupportedFormats.UPC_A,
            Html5QrcodeSupportedFormats.UPC_E, Html5QrcodeSupportedFormats.ITF,
            Html5QrcodeSupportedFormats.CODABAR, Html5QrcodeSupportedFormats.RSS_14,
            Html5QrcodeSupportedFormats.DATA_MATRIX,
        ];
    }

    function getVideoDimensions() {
        try {
            for (const v of document.querySelectorAll('video')) {
                if (v.videoWidth > 0 && v.videoHeight > 0) return { width: v.videoWidth, height: v.videoHeight };
            }
        } catch (e) {}
        const cr = scannerContainer.getBoundingClientRect();
        return { width: cr.width, height: cr.height };
    }

    function getVideoDisplaySize() {
        try {
            for (const v of document.querySelectorAll('video')) {
                if (v.offsetWidth > 0 && v.offsetHeight > 0) return { width: v.offsetWidth, height: v.offsetHeight };
            }
        } catch (e) {}
        const cr = scannerContainer.getBoundingClientRect();
        return { width: cr.width, height: cr.height };
    }

    function showDetectHighlight(corners) {
        if (!corners || corners.length < 4) { showFlash(); return; }
        const cr = scannerContainer.getBoundingClientRect();
        const vs = getVideoDimensions();
        const ds = getVideoDisplaySize();
        const xs = corners.map(p => p.x), ys = corners.map(p => p.y);
        let left = Math.min(...xs) * (ds.width / vs.width) + (cr.width - ds.width) / 2;
        let top = Math.min(...ys) * (ds.height / vs.height) + (cr.height - ds.height) / 2;
        let w = (Math.max(...xs) - Math.min(...xs)) * (ds.width / vs.width);
        let h = (Math.max(...ys) - Math.min(...ys)) * (ds.height / vs.height);
        if (w < 10 || h < 10 || left < -50 || top < -50 || left > cr.width + 50 || top > cr.height + 50) {
            w = h = Math.min(cr.width * 0.5, 180);
            left = (cr.width - w) / 2; top = (cr.height - h) / 2;
        }
        const p = 6;
        detectHighlight.style.cssText = `left:${left-p}px;top:${top-p}px;width:${w+p*2}px;height:${h+p*2}px;opacity:1;transform:scale(1)`;
        detectHighlight.classList.remove('hidden');
        requestAnimationFrame(() => {
            detectHighlight.style.transition = 'all 0.4s ease-out';
            detectHighlight.style.left = ((cr.width - (w + p * 2)) / 2) + 'px';
            detectHighlight.style.top = ((cr.height - (h + p * 2)) / 2) + 'px';
            detectHighlight.style.transform = 'scale(1.2)';
            detectHighlight.style.opacity = '0';
        });
    }

    function showFlash() { scanFlash.classList.remove('hidden'); setTimeout(() => scanFlash.classList.add('hidden'), 600); }

    async function startScanner() {
        if (html5QrCode) { try { await html5QrCode.stop(); } catch (e) {} html5QrCode.clear(); }
        html5QrCode = new Html5Qrcode('scanner');
        const loading = document.getElementById('scanner-loading');
        detectHighlight.classList.add('hidden'); detectHighlight.style.transition = 'none'; detectHighlight.style.transform = ''; detectHighlight.style.opacity = '';
        scanFlash.classList.add('hidden');
        if (loading) loading.classList.remove('hidden');
        try {
            await html5QrCode.start({ facingMode: 'environment' }, { fps: 24, formatsToSupport: getFormats() }, onScanSuccess, onScanFailure);
            if (loading) loading.classList.add('hidden');
        } catch (err) { if (loading) loading.textContent = 'Camera access denied or unavailable.'; }
    }

    function onScanSuccess(decodedText, decodedResult) {
        if (scanningPaused) return;
        const fmt = decodedResult?.format?.formatName || '';
        if (fmt) { const qr = /qr/i.test(fmt); if (currentMode === 'qr' && !qr) return; if (currentMode === 'barcode' && qr) return; }
        scanningPaused = true; if (html5QrCode) html5QrCode.pause();
        let corners = null; const r = decodedResult?.result;
        if (r?.location) { const l = r.location; corners = [l.topLeft, l.topRight, l.bottomRight, l.bottomLeft]; }
        else if (r?.cornerPoints) corners = r.cornerPoints;
        else if (r?.boundingBox) { const b = r.boundingBox; corners = [{x:b.left,y:b.top},{x:b.right,y:b.top},{x:b.right,y:b.bottom},{x:b.left,y:b.bottom}]; }
        corners && corners.length >= 4 ? showDetectHighlight(corners) : showFlash();
        setTimeout(() => lookupDocument(decodedText), 500);
    }

    function onScanFailure() {}

    function lookupDocument(code) {
        scanStatus.textContent = 'Looking up...'; scanStatus.className = 'mt-3 text-sm text-gray-500';
        fetch('/system/documents/finish/lookup', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
            body: JSON.stringify({ code })
        }).then(r => r.json()).then(d => {
            if (d.error) { scanStatus.textContent = d.error; scanStatus.className = 'mt-3 text-sm text-red-600'; resumeScanning(); return; }
            scanStatus.textContent = ''; showDocument(d);
        }).catch(() => { scanStatus.textContent = 'Error.'; scanStatus.className = 'mt-3 text-sm text-red-600'; resumeScanning(); });
    }

    function resumeScanning() {
        detectHighlight.classList.add('hidden'); detectHighlight.style.transition = 'none'; detectHighlight.style.transform = ''; detectHighlight.style.opacity = '';
        scanFlash.classList.add('hidden'); if (html5QrCode) html5QrCode.resume(); scanningPaused = false;
    }

    function showDocument(data) {
        const doc = data.document; currentDocumentId = doc.id;
        document.getElementById('doc-title').textContent = doc.title;
        document.getElementById('doc-type').textContent = 'Type: ' + doc.document_type;
        document.getElementById('doc-creator').textContent = 'Created by: ' + (doc.creator?.firstname||'') + ' ' + (doc.creator?.lastname||'');
        document.getElementById('doc-created-at').textContent = new Date(doc.created_at).toLocaleString();
        document.getElementById('doc-updated-at').textContent = new Date(doc.updated_at).toLocaleString();
        document.getElementById('doc-notes').textContent = doc.notes || 'No notes.';
        document.getElementById('doc-qr-image').src = data.qr_data_url;
        const ci = document.getElementById('current-holder-info');
        if (data.current_holder) {
            const ch = data.current_holder; let n = ch.firstname + ' ' + ch.lastname; let i = '';
            if (ch.department) i += ch.department.name; if (ch.office) i += (i?' - ':'') + ch.office.name;
            ci.innerHTML = '<span class="font-medium">' + n + '</span>' + (i?'<br><span class="text-indigo-700 text-xs">'+i+'</span>':'');
        } else { ci.innerHTML = '<span class="font-medium text-yellow-700">No current holder.</span>'; }
        const pl = document.getElementById('past-holders-list'); pl.innerHTML = '';
        if (data.past_tracks && data.past_tracks.length > 0) {
            data.past_tracks.forEach(t => {
                const u = t.user; const d = document.createElement('div');
                d.className = 'flex items-center justify-between bg-gray-50 rounded-lg px-4 py-2.5 text-sm';
                let n = u.firstname + ' ' + u.lastname; let dp = '';
                if (u.department) dp += u.department.name; if (u.office) dp += (dp?' - ':'') + u.office.name;
                d.innerHTML = '<div><span class="font-medium text-gray-900">'+n+'</span>'+(dp?'<br><span class="text-gray-500 text-xs">'+dp+'</span>':'')+'</div>'+
                    '<div class="text-gray-500 text-xs text-right">Received: '+new Date(t.received_at).toLocaleDateString()+'<br>Released: '+new Date(t.released_at).toLocaleDateString()+'</div>';
                pl.appendChild(d);
            });
        } else { pl.innerHTML = '<p class="text-sm text-gray-500 italic">No past holders.</p>'; }
        finishBtn.disabled = !data.current_holder;
        finishBtn.textContent = data.current_holder ? 'Finish Transaction' : 'No Current Holder';
        resultModal.classList.remove('hidden');
    }

    function switchMode(mode) {
        if (mode === currentMode) return; scanningPaused = false; currentMode = mode;
        modeQr.className = 'px-4 py-1.5 text-sm font-medium transition-colors ' + (mode==='qr'?'bg-indigo-600 text-white':'text-gray-600 hover:bg-gray-100');
        modeBarcode.className = 'px-4 py-1.5 text-sm font-medium transition-colors ' + (mode==='barcode'?'bg-indigo-600 text-white':'text-gray-600 hover:bg-gray-100');
        startScanner();
    }

    modeQr.addEventListener('click', () => switchMode('qr'));
    modeBarcode.addEventListener('click', () => switchMode('barcode'));
    manualLookupBtn.addEventListener('click', () => { const c = manualCode.value.trim(); if (!c) { scanStatus.textContent='Enter a code.'; scanStatus.className='mt-3 text-sm text-red-600'; return; } lookupDocument(c); });
    manualCode.addEventListener('keydown', e => { if (e.key === 'Enter') manualLookupBtn.click(); });
    closeModal.addEventListener('click', () => { resultModal.classList.add('hidden'); resumeScanning(); manualCode.value = ''; });
    resultModal.addEventListener('click', e => { if (e.target === resultModal) closeModal.click(); });
    finishBtn.addEventListener('click', function() {
        if (!currentDocumentId) return; finishBtn.disabled = true; finishBtn.textContent = 'Finishing...';
        fetch('/system/documents/finish/' + currentDocumentId, {
            method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' }
        }).then(r => r.json()).then(d => {
            if (d.error) { alert(d.error); finishBtn.disabled = false; finishBtn.textContent = 'Finish Transaction'; return; }
            alert('Transaction finished!'); closeModal.click(); manualCode.value = '';
        }).catch(() => { alert('Error.'); finishBtn.disabled = false; finishBtn.textContent = 'Finish Transaction'; });
    });
    startScanner();
});
