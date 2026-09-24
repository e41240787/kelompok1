<?php

namespace App\Http\Controllers;

use App\Models\GpsData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GpsDataController extends Controller
{
    /**
     * Menampilkan daftar histori data GPS (Untuk Front-End).
     * Dapat difilter berdasarkan device_id dan limit.
     */
    public function index(Request $request): JsonResponse
    {
        $query = GpsData::query();

        // Filter berdasarkan ID Perangkat jika dikirim oleh Front-End
        if ($request->has('device_id') && !empty($request->device_id)) {
            $query->where('device_id', $request->device_id);
        }

        $limit = $request->get('limit', 50);
        $gpsData = $query->orderBy('created_at', 'desc')->paginate($limit);

        return response()->json([
            'status' => 'success',
            'message' => 'Data GPS berhasil diambil',
            'data' => $gpsData->items(),
            'pagination' => [
                'total' => $gpsData->total(),
                'per_page' => $gpsData->perPage(),
                'current_page' => $gpsData->currentPage(),
                'last_page' => $gpsData->lastPage(),
            ]
        ], 200);
    }

    /**
     * Menerima data GPS baru dari Sensor / Perangkat IoT (HTTP POST dari Perangkat via IP).
     */
    public function store(Request $request): JsonResponse
    {
        // Validasi input dari sensor
        $validator = Validator::make($request->all(), [
            'device_id'   => 'required|string|max:50',
            'latitude'    => 'required|numeric|between:-90,90',
            'longitude'   => 'required|numeric|between:-180,180',
            'altitude'    => 'nullable|numeric',
            'speed'       => 'nullable|numeric|min:0',
            'satellites'  => 'nullable|integer|min:0',
            'recorded_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi data GPS gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Simpan data GPS ke database MySQL
        $gpsData = GpsData::create([
            'device_id'   => $request->input('device_id'),
            'latitude'    => $request->input('latitude'),
            'longitude'   => $request->input('longitude'),
            'altitude'    => $request->input('altitude'),
            'speed'       => $request->input('speed'),
            'satellites'  => $request->input('satellites'),
            'recorded_at' => $request->input('recorded_at', now()),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data GPS berhasil disimpan ke database',
            'data'    => $gpsData
        ], 201);
    }

    /**
     * Mengambil lokasi GPS terkini/terbaru (Untuk Front-End Real-Time Tracking).
     */
    public function latest(Request $request): JsonResponse
    {
        $query = GpsData::query();

        if ($request->has('device_id') && !empty($request->device_id)) {
            $query->where('device_id', $request->device_id);
        }

        $latestData = $query->orderBy('created_at', 'desc')->first();

        if (!$latestData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Belum ada data GPS yang tersimpan',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data GPS terkini berhasil diambil',
            'data' => $latestData
        ], 200);
    }

    /**
     * Menampilkan detail data GPS berdasarkan ID.
     */
    public function show(int $id): JsonResponse
    {
        $gpsData = GpsData::find($id);

        if (!$gpsData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data GPS tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail data GPS berhasil diambil',
            'data' => $gpsData
        ], 200);
    }

    /**
     * Menghapus log data GPS berdasarkan ID.
     */
    public function destroy(int $id): JsonResponse
    {
        $gpsData = GpsData::find($id);

        if (!$gpsData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data GPS tidak ditemukan',
            ], 404);
        }

        $gpsData->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data GPS berhasil dihapus'
        ], 200);
    }
}
