<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Peta & Lokasi - Fitur GPS</title>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans antialiased flex flex-col">

    <!-- Header Sederhana -->
    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-indigo-600/20 text-indigo-400 rounded-lg border border-indigo-500/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-white">Peta & Lokasi</h1>
                    <p class="text-xs text-slate-400">Modul Pelacakan GPS & Visualisasi Peta</p>
                </div>
            </div>

            <!-- Badge Status GPS -->
            <div class="flex items-center space-x-2 bg-slate-800/80 px-3 py-1.5 rounded-full border border-slate-700/60">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>
                <span class="text-xs font-medium text-slate-300">GPS Siap</span>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col gap-6">

        <!-- Banner Aksi & Deskripsi Singkat -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-800/40 p-4 rounded-xl border border-slate-800">
            <div>
                <h2 class="text-base font-semibold text-slate-200">Area Pemetaan & Pelacakan</h2>
                <p class="text-sm text-slate-400">Klik tombol di sebelah kanan untuk memperbarui koordinat posisi Anda.</p>
            </div>
            
            <!-- Tombol Lokasi Saya -->
            <button id="btn-lokasi-saya" type="button" 
                class="inline-flex items-center justify-center space-x-2 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-lg shadow-indigo-600/25 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-slate-900 cursor-pointer">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0013 3.06V1h-2v2.06A8.994 8.994 0 003.06 11H1v2h2.06A8.994 8.994 0 0011 20.94V23h2v-2.06A8.994 8.994 0 0020.94 13H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"></path>
                </svg>
                <span>Lokasi Saya</span>
            </button>
        </div>

        <!-- Grid Layout Container Map & Panel Informasi (Responsive Desktop/Mobile) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 flex-1 min-h-[450px]">

            <!-- Container Peta (Desktop: 2 kolom, Mobile: full width) -->
            <div class="lg:col-span-2 bg-slate-800/60 rounded-xl border border-slate-700/60 overflow-hidden relative flex flex-col min-h-[380px]">
                
                <!-- Leaflet Map Container -->
                <div id="map" class="w-full h-full min-h-[420px] rounded-xl z-0"></div>


            </div>

            <!-- Panel Informasi Lokasi (Desktop: 1 kolom, Mobile: full width) -->
            <div class="lg:col-span-1 flex flex-col gap-4">
                
                <div class="bg-slate-800/60 rounded-xl border border-slate-700/60 p-5 flex flex-col gap-5">
                    <div class="flex items-center space-x-2 border-b border-slate-700/50 pb-3">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-base font-semibold text-slate-200">Panel Informasi Lokasi</h3>
                    </div>

                    <!-- Item Status Lokasi -->
                    <div class="space-y-1.5">
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Status Lokasi</span>
                        <div class="flex items-center space-x-2 bg-slate-900/60 px-3.5 py-2.5 rounded-lg border border-slate-700/40">
                            <span id="status-dot" class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span id="status-text" class="text-sm font-medium text-slate-300">Belum Didapatkan</span>
                        </div>
                    </div>

                    <!-- Item Latitude -->
                    <div class="space-y-1.5">
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Latitude</span>
                        <div class="bg-slate-900/60 px-3.5 py-2.5 rounded-lg border border-slate-700/40 flex items-center justify-between">
                            <span id="val-latitude" class="font-mono text-sm text-slate-200 font-medium">-</span>
                            <span class="text-xs text-slate-500">° N/S</span>
                        </div>
                    </div>

                    <!-- Item Longitude -->
                    <div class="space-y-1.5">
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Longitude</span>
                        <div class="bg-slate-900/60 px-3.5 py-2.5 rounded-lg border border-slate-700/40 flex items-center justify-between">
                            <span id="val-longitude" class="font-mono text-sm text-slate-200 font-medium">-</span>
                            <span class="text-xs text-slate-500">° E/W</span>
                        </div>
                    </div>

                    <!-- Info Tambahan / Tips -->
                    <div class="mt-2 bg-indigo-950/40 border border-indigo-800/40 rounded-lg p-3 text-xs text-indigo-300/80 leading-relaxed">
                        <span class="font-semibold text-indigo-300">Info:</span> Data lokasi akan ditampilkan di atas ketika fitur GPS diaktifkan.
                    </div>
                </div>

            </div>

        </div>

    </main>

    <!-- Footer Sederhana -->
    <footer class="border-t border-slate-800 bg-slate-900 py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} Project Kelompok 1 &bull; Modul Frontend GPS & Map
        </div>
    </footer>

</body>
</html>
