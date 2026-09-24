<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Peta & Pelacakan GPS Real-Time</title>

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
                    <h1 class="text-xl font-bold tracking-tight text-white">Peta & Lokasi GPS</h1>
                    <p class="text-xs text-slate-400">Monitoring Sensor & Database MySQL</p>
                </div>
            </div>

            <!-- Badge Status GPS & Database -->
            <div class="flex items-center space-x-3">
                <div class="flex items-center space-x-2 bg-slate-800/80 px-3 py-1.5 rounded-full border border-slate-700/60">
                    <span class="relative flex h-2.5 w-2.5">
                        <span id="sync-ping" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span id="sync-dot" class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span id="sync-status" class="text-xs font-medium text-slate-300">Live Database Sync</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col gap-6">

        <!-- Banner Aksi & Control Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-800/40 p-4 rounded-xl border border-slate-800">
            <div>
                <h2 class="text-base font-semibold text-slate-200">Monitoring Koordinat GPS</h2>
                <p class="text-sm text-slate-400">Ambil data lokasi dari Database MySQL atau kirim posisi GPS browser saat ini.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <!-- Tombol Refresh / Fetch dari Database -->
                <button id="btn-fetch-db" type="button" 
                    class="inline-flex items-center justify-center space-x-2 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white font-medium px-4 py-2 rounded-lg shadow-md transition-all duration-200 text-sm cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Cek Data Terbaru</span>
                </button>

                <!-- Tombol Lokasi Saya (Browser GPS) -->
                <button id="btn-lokasi-saya" type="button" 
                    class="inline-flex items-center justify-center space-x-2 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white font-medium px-4 py-2 rounded-lg shadow-md transition-all duration-200 text-sm cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0013 3.06V1h-2v2.06A8.994 8.994 0 003.06 11H1v2h2.06A8.994 8.994 0 0011 20.94V23h2v-2.06A8.994 8.994 0 0020.94 13H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"></path>
                    </svg>
                    <span>GPS Browser & Simpan</span>
                </button>
            </div>
        </div>

        <!-- Grid Layout Container Map & Panel Informasi -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 flex-1 min-h-[480px]">

            <!-- Container Peta Leaflet -->
            <div class="lg:col-span-2 bg-slate-800/60 rounded-xl border border-slate-700/60 overflow-hidden relative flex flex-col min-h-[420px]">
                <div id="map" class="w-full h-full min-h-[450px] rounded-xl z-0"></div>
            </div>

            <!-- Panel Informasi Lokasi (Synchronized with Database Table Variables) -->
            <div class="lg:col-span-1 flex flex-col gap-4">
                
                <div class="bg-slate-800/60 rounded-xl border border-slate-700/60 p-5 flex flex-col gap-4">
                    <div class="flex items-center justify-between border-b border-slate-700/50 pb-3">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-base font-semibold text-slate-200">Detail Variabel GPS</h3>
                        </div>
                        <span id="badge-source" class="text-xs px-2 py-0.5 rounded bg-slate-700 text-slate-300">MySQL Database</span>
                    </div>

                    <!-- Item Status Lokasi -->
                    <div class="space-y-1">
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Status Sinyal</span>
                        <div class="flex items-center space-x-2 bg-slate-900/60 px-3 py-2 rounded-lg border border-slate-700/40">
                            <span id="status-dot" class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span id="status-text" class="text-sm font-medium text-slate-300">Memuat Data...</span>
                        </div>
                    </div>

                    <!-- Variable: device_id -->
                    <div class="space-y-1">
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Device ID (<code class="text-indigo-300">device_id</code>)</span>
                        <div class="bg-slate-900/60 px-3 py-2 rounded-lg border border-slate-700/40 flex items-center justify-between">
                            <span id="val-device-id" class="font-mono text-sm text-indigo-400 font-semibold">-</span>
                        </div>
                    </div>

                    <!-- Grid Latitude & Longitude -->
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Variable: latitude -->
                        <div class="space-y-1">
                            <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Latitude (<code class="text-indigo-300">latitude</code>)</span>
                            <div class="bg-slate-900/60 px-3 py-2 rounded-lg border border-slate-700/40 flex items-center justify-between">
                                <span id="val-latitude" class="font-mono text-xs text-slate-200 font-medium">-</span>
                            </div>
                        </div>

                        <!-- Variable: longitude -->
                        <div class="space-y-1">
                            <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Longitude (<code class="text-indigo-300">longitude</code>)</span>
                            <div class="bg-slate-900/60 px-3 py-2 rounded-lg border border-slate-700/40 flex items-center justify-between">
                                <span id="val-longitude" class="font-mono text-xs text-slate-200 font-medium">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Grid Speed, Altitude, Satellites -->
                    <div class="grid grid-cols-3 gap-2">
                        <!-- Variable: speed -->
                        <div class="space-y-1">
                            <span class="text-[10px] font-medium text-slate-400 uppercase">Speed (<code class="text-indigo-300">speed</code>)</span>
                            <div class="bg-slate-900/60 px-2 py-1.5 rounded-lg border border-slate-700/40 text-center">
                                <span id="val-speed" class="font-mono text-xs text-slate-200 font-medium">-</span> <span class="text-[10px] text-slate-500">km/h</span>
                            </div>
                        </div>

                        <!-- Variable: altitude -->
                        <div class="space-y-1">
                            <span class="text-[10px] font-medium text-slate-400 uppercase">Altitude (<code class="text-indigo-300">altitude</code>)</span>
                            <div class="bg-slate-900/60 px-2 py-1.5 rounded-lg border border-slate-700/40 text-center">
                                <span id="val-altitude" class="font-mono text-xs text-slate-200 font-medium">-</span> <span class="text-[10px] text-slate-500">m</span>
                            </div>
                        </div>

                        <!-- Variable: satellites -->
                        <div class="space-y-1">
                            <span class="text-[10px] font-medium text-slate-400 uppercase">Satelit (<code class="text-indigo-300">satellites</code>)</span>
                            <div class="bg-slate-900/60 px-2 py-1.5 rounded-lg border border-slate-700/40 text-center">
                                <span id="val-satellites" class="font-mono text-xs text-slate-200 font-medium">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Variable: recorded_at / created_at -->
                    <div class="space-y-1">
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Waktu Waktu (<code class="text-indigo-300">recorded_at</code>)</span>
                        <div class="bg-slate-900/60 px-3 py-2 rounded-lg border border-slate-700/40 text-xs font-mono text-slate-300">
                            <span id="val-recorded-at">-</span>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="mt-1 bg-indigo-950/40 border border-indigo-800/40 rounded-lg p-3 text-xs text-indigo-300/80 leading-relaxed">
                        <span class="font-semibold text-indigo-300">Petunjuk:</span> Data di panel ini terhubung secara langsung dengan kolom database MySQL (<code class="text-white">gps_data</code>).
                    </div>
                </div>

            </div>

        </div>

    </main>

    <!-- Footer Sederhana -->
    <footer class="border-t border-slate-800 bg-slate-900 py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} Project Kelompok 1 &bull; Modul Frontend & Backend GPS Tracking
        </div>
    </footer>

</body>
</html>
