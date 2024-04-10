import QrScanner from "./qr-scanner.min.js";

const video = document.getElementById('qr-video');
const camQrResult = document.getElementById('cam-qr-result');
const successCode = document.getElementById('success-code');
const badCode = document.getElementById('bad-code');
const flashUrl = document.getElementById('flash-url');

function setResult(label, result) {
    const pattern = /flash\/([a-zA-Z0-9]+)$/;

    if (pattern.test(result.data)) {
        successCode.style.display = 'block';
        scanner.stop();
        const qrcode = result.data.match(pattern)[1];
        window.location = flashUrl.innerText.replace('---code---', qrcode);
    } else {
        badCode.style.display = 'block';
        setTimeout(() => {
            badCode.style.display = 'none';
        }, 2000);
    }
}

const scanner = new QrScanner(video, result => setResult(camQrResult, result), {
    highlightScanRegion: true,
    highlightCodeOutline: true,
    maxScansPerSecond: 5
});

scanner.start();