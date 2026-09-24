import L from 'leaflet';
import markerIconUrl from 'leaflet/dist/images/marker-icon.png';
import markerIconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadowUrl from 'leaflet/dist/images/marker-shadow.png';

// Perbaikan path icon default Leaflet di Vite
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconUrl: markerIconUrl,
    iconRetinaUrl: markerIconRetinaUrl,
    shadowUrl: markerShadowUrl,
});

document.addEventListener('DOMContentLoaded', () => {
    const mapElement = document.getElementById('map');
    if (!mapElement) return;

    // Koordinat default awal (Jakarta Monas)
    const defaultLat = -6.1754;
    const defaultLng = 106.8272;
    const defaultZoom = 13;

    // Inisialisasi Leaflet Map
    const map = L.map('map', {
        zoomControl: true,
        scrollWheelZoom: true,
        dragging: true,
    }).setView([defaultLat, defaultLng], defaultZoom);

    // OpenStreetMap Tile Layer
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>'
    }).addTo(map);

    let currentMarker = null;

    // Elemen UI DOM
    const btnFetchDb = document.getElementById('btn-fetch-db');
    const btnLokasi = document.getElementById('btn-lokasi-saya');
    const statusDot = document.getElementById('status-dot');
    const statusText = document.getElementById('status-text');
    const badgeSource = document.getElementById('badge-source');

    // Variabel Database MySQL
    const valDeviceId = document.getElementById('val-device-id');
    const valLat = document.getElementById('val-latitude');
    const valLng = document.getElementById('val-longitude');
    const valSpeed = document.getElementById('val-speed');
    const valAltitude = document.getElementById('val-altitude');
    const valSatellites = document.getElementById('val-satellites');
    const valRecordedAt = document.getElementById('val-recorded-at');

    /**
     * Mengubah data ke tampilan UI
     */
    function updateUI(data, isFromDb = true) {
        if (!data) return;

        const lat = parseFloat(data.latitude);
        const lng = parseFloat(data.longitude);
        const deviceId = data.device_id || 'UNKNOWN';
        const speed = data.speed !== null && data.speed !== undefined ? data.speed : '-';
        const altitude = data.altitude !== null && data.altitude !== undefined ? data.altitude : '-';
        const satellites = data.satellites !== null && data.satellites !== undefined ? data.satellites : '-';
        const recordedAt = data.recorded_at ? new Date(data.recorded_at).toLocaleString('id-ID') : new Date().toLocaleString('id-ID');

        // Render ke elemen HTML
        if (valDeviceId) valDeviceId.textContent = deviceId;
        if (valLat) valLat.textContent = lat.toFixed(6);
        if (valLng) valLng.textContent = lng.toFixed(6);
        if (valSpeed) valSpeed.textContent = speed;
        if (valAltitude) valAltitude.textContent = altitude;
        if (valSatellites) valSatellites.textContent = satellites;
        if (valRecordedAt) valRecordedAt.textContent = recordedAt;

        if (statusText) statusText.textContent = isFromDb ? 'Terkoneksi ke Database' : 'Lokasi Browser Ditemukan';
        if (statusDot) statusDot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500';
        if (badgeSource) badgeSource.textContent = isFromDb ? 'MySQL Database' : 'Browser Sensor';

        // Pindahkan Peta & Update Marker
        map.flyTo([lat, lng], 15, { duration: 1.2 });

        const popupContent = `
            <div style="font-family: sans-serif; font-size: 12px; line-height: 1.5;">
                <b style="color: #4f46e5;">Perangkat: ${deviceId}</b><br>
                <b>Lat:</b> ${lat.toFixed(6)} | <b>Lng:</b> ${lng.toFixed(6)}<br>
                <b>Kecepatan:</b> ${speed} km/h | <b>Ketinggian:</b> ${altitude} m<br>
                <small style="color: #64748b;">Waktu: ${recordedAt}</small>
            </div>
        `;

        if (currentMarker) {
            currentMarker.setLatLng([lat, lng]).bindPopup(popupContent).openPopup();
        } else {
            currentMarker = L.marker([lat, lng]).addTo(map).bindPopup(popupContent).openPopup();
        }
    }

    /**
     * Ambil Data GPS Terkini dari API Backend Laravel (/api/gps/latest)
     */
    async function fetchLatestGpsData() {
        try {
            if (statusText) statusText.textContent = 'Mengambil data dari DB...';
            if (statusDot) statusDot.className = 'w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse';

            const response = await fetch('/api/gps/latest');
            const result = await response.json();

            if (response.ok && result.status === 'success' && result.data) {
                updateUI(result.data, true);
            } else {
                if (statusText) statusText.textContent = 'Belum Ada Data di DB';
                if (statusDot) statusDot.className = 'w-2.5 h-2.5 rounded-full bg-amber-500';
            }
        } catch (error) {
            console.error('Gagal mengambil data dari API:', error);
            if (statusText) statusText.textContent = 'Gagal Koneksi Backend';
            if (statusDot) statusDot.className = 'w-2.5 h-2.5 rounded-full bg-rose-500';
        }
    }

    /**
     * Kirim Data GPS ke Backend API Laravel (/api/gps) untuk Disimpan ke MySQL
     */
    async function saveGpsToDatabase(gpsPayload) {
        try {
            const response = await fetch('/api/gps', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(gpsPayload)
            });

            const result = await response.json();
            if (response.ok && result.status === 'success') {
                console.log('Berhasil disimpan ke database:', result.data);
                updateUI(result.data, true);
            }
        } catch (err) {
            console.error('Gagal menyimpan GPS ke database:', err);
        }
    }

    // Event Listener Tombol "Cek Data Terbaru" (Fetch dari DB)
    if (btnFetchDb) {
        btnFetchDb.addEventListener('click', () => {
            fetchLatestGpsData();
        });
    }

    // Event Listener Tombol "GPS Browser & Simpan"
    if (btnLokasi) {
        btnLokasi.addEventListener('click', () => {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung Geolocation.');
                return;
            }

            if (statusText) statusText.textContent = 'Mencari Sinyal GPS...';
            if (statusDot) statusDot.className = 'w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse';
            btnLokasi.disabled = true;

            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    const payload = {
                        device_id: 'BROWSER-CLIENT',
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        altitude: position.coords.altitude ? Math.round(position.coords.altitude) : 0,
                        speed: position.coords.speed ? parseFloat((position.coords.speed * 3.6).toFixed(1)) : 0,
                        satellites: 1,
                        recorded_at: new Date().toISOString()
                    };

                    btnLokasi.disabled = false;
                    // Simpan koordinat browser langsung ke MySQL via Backend API
                    await saveGpsToDatabase(payload);
                },
                (error) => {
                    btnLokasi.disabled = false;
                    alert('Gagal mengambil lokasi dari browser: ' + error.message);
                    if (statusText) statusText.textContent = 'GPS Browser Ditolak';
                    if (statusDot) statusDot.className = 'w-2.5 h-2.5 rounded-full bg-rose-500';
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        });
    }

    // Ambil data pertama kali saat halaman dimuat
    fetchLatestGpsData();

    // Auto Refresh / Polling dari database setiap 5 detik (Real-time Live Tracking)
    setInterval(() => {
        fetchLatestGpsData();
    }, 5000);
});
