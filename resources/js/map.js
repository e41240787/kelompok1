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

    // Koordinat dummy awal (Jakarta Monas)
    const dummyLat = -6.1754;
    const dummyLng = 106.8272;
    const dummyZoom = 13;

    // Inisialisasi Leaflet Map
    const map = L.map('map', {
        zoomControl: true,
        scrollWheelZoom: true,
        dragging: true,
    }).setView([dummyLat, dummyLng], dummyZoom);

    // OpenStreetMap Tile Layer
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Marker awal dummy
    let currentMarker = L.marker([dummyLat, dummyLng])
        .addTo(map)
        .bindPopup('<b>Posisi Awal (Dummy)</b><br>Monas, Jakarta')
        .openPopup();

    // Elemen UI
    const btnLokasi = document.getElementById('btn-lokasi-saya');
    const statusDot = document.getElementById('status-dot');
    const statusText = document.getElementById('status-text');
    const valLat = document.getElementById('val-latitude');
    const valLng = document.getElementById('val-longitude');

    // Handling Tombol "Lokasi Saya"
    if (btnLokasi) {
        btnLokasi.addEventListener('click', () => {
            if (!navigator.geolocation) {
                if (statusText) statusText.textContent = 'Geolocation tidak didukung browser ini';
                if (statusDot) statusDot.className = 'w-2.5 h-2.5 rounded-full bg-rose-500';
                return;
            }

            // Update UI saat mencari sinyal lokasi
            if (statusText) statusText.textContent = 'Mencari Sinyal GPS...';
            if (statusDot) statusDot.className = 'w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse';
            btnLokasi.disabled = true;
            btnLokasi.classList.add('opacity-75', 'cursor-not-allowed');

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const accuracy = Math.round(position.coords.accuracy);

                    // Set nilai Latitude & Longitude
                    if (valLat) valLat.textContent = lat.toFixed(6);
                    if (valLng) valLng.textContent = lng.toFixed(6);

                    // Update Status UI
                    if (statusText) statusText.textContent = 'Lokasi Ditemukan';
                    if (statusDot) statusDot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500';

                    // Pindahkan Peta & Marker
                    map.flyTo([lat, lng], 16, { duration: 1.5 });
                    
                    if (currentMarker) {
                        currentMarker.setLatLng([lat, lng])
                            .bindPopup(`<b>Lokasi Anda Saat Ini</b><br>Lat: ${lat.toFixed(5)}, Lng: ${lng.toFixed(5)}<br>Akurasi: ~${accuracy}m`)
                            .openPopup();
                    } else {
                        currentMarker = L.marker([lat, lng])
                            .addTo(map)
                            .bindPopup(`<b>Lokasi Anda Saat Ini</b><br>Lat: ${lat.toFixed(5)}, Lng: ${lng.toFixed(5)}<br>Akurasi: ~${accuracy}m`)
                            .openPopup();
                    }

                    // Reset Tombol
                    btnLokasi.disabled = false;
                    btnLokasi.classList.remove('opacity-75', 'cursor-not-allowed');
                },
                (error) => {
                    let errorMessage = 'Gagal Mengambil Lokasi';
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'Izin GPS Ditolak Browser';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'Sinyal Lokasi Tidak Tersedia';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'Waktu Request GPS Habis';
                            break;
                    }

                    if (statusText) statusText.textContent = errorMessage;
                    if (statusDot) statusDot.className = 'w-2.5 h-2.5 rounded-full bg-rose-500';

                    // Reset Tombol
                    btnLokasi.disabled = false;
                    btnLokasi.classList.remove('opacity-75', 'cursor-not-allowed');
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    }
});
