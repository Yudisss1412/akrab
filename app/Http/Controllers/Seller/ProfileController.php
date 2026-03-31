<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the seller's profile page.
     */
    public function show()
    {
        $user = Auth::user();

        // Ambil data seller terkait dengan profile_image
        $seller = Seller::where('user_id', $user->id)->first();

        if (!$seller) {
            abort(403, 'Akses ditolak. Seller record tidak ditemukan.');
        }

        return view('penjual.profil_penjual', compact('user', 'seller'));
    }

    /**
     * Show the form for editing the seller's profile.
     */
    public function edit()
    {
        $user = Auth::user()->load('seller'); // Load the seller relationship
        return view('penjual.edit_profil_penjual', compact('user'));
    }

    /**
     * Update the seller's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Log request data to debug
        \Log::info('Profile update request received for user ID: ' . $user->id);
        \Log::info('Request content type:', [$request->header('Content-Type')]);
        \Log::info('Request all data:', $request->all());
        \Log::info('Request raw input:', $request->all()); // This will work for both form data and JSON
        \Log::info('Request keys:', array_keys($request->all()));

        // Log individual fields
        \Log::info('Shop name from request:', ['value' => $request->input('shopName')]);
        \Log::info('Email from request:', ['value' => $request->input('email')]);
        \Log::info('Phone from request:', ['value' => $request->input('phone')]);

        // Validasi input
        // Validasi input termasuk file avatar
        $validationRules = [
            'shopName' => 'required|string|max:50',
            'ownerName' => 'required|string|max:50',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id)
            ],
            'phone' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^(\+62|62|0)[2-9]\d{7,11}$/', $value)) {
                        $fail('Nomor telepon harus menggunakan format Indonesia (contoh: +6281234567890).');
                    }
                },
            ],
            'address' => 'required|string|min:10|max:120',
            'shopDescription' => 'required|string|min:10|max:200',
            'bankName' => 'required|string|max:50',
            'accountNumber' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^[0-9]{8,20}$/', $value)) {
                        $fail('Nomor rekening harus antara 8 hingga 20 digit dan hanya angka.');
                    }
                },
            ],
            'accountHolder' => 'required|string|max:50',
        ];

        // Tambahkan validasi untuk avatar jika ada file yang diunggah
        if ($request->hasFile('avatar')) {
            $validationRules['avatar'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }

        $validator = Validator::make($request->all(), $validationRules, [
            'phone.regex' => 'Nomor telepon harus menggunakan format Indonesia (contoh: +6281234567890)',
            'avatar.image' => 'File harus berupa gambar',
            'avatar.mimes' => 'Gambar harus berupa file dengan format: jpeg, png, jpg, gif, svg',
            'avatar.max' => 'Ukuran gambar tidak boleh lebih dari 2MB',
        ]);

        if ($validator->fails()) {
            \Log::info('Validation failed', $validator->errors()->toArray());

            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Update data profil
        $updateData = [
            'name' => $request->input('shopName'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'shop_description' => $request->input('shopDescription'),
            'bank_name' => $request->input('bankName'),
            'bank_account_number' => $request->input('accountNumber'),
            'bank_account_name' => $request->input('accountHolder'),
        ];

        // Handle coordinates - prioritize coordinates sent from form if available
        $lat = $request->input('lat');
        $lng = $request->input('lng');

        if ($lat && $lng) {
            // Use coordinates sent from the form (from the map)
            $updateData['lat'] = $lat;
            $updateData['lng'] = $lng;
        } else {
            // If no coordinates sent from form, try geocoding the address
            if ($request->input('address') !== $user->address) {
                $address = $request->input('address');
                $coordinates = $this->getCoordinatesFromAddress($address);

                if ($coordinates) {
                    $updateData['lat'] = $coordinates['lat'];
                    $updateData['lng'] = $coordinates['lng'];
                }
            }
        }

        // Update seller data untuk profile image
        $seller = Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            return response()->json([
                'success' => false,
                'message' => 'Data penjual tidak ditemukan'
            ], 404);
        }

        // Jika ada file avatar yang diupload
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');

            // Validasi file
            $request->validate([
                'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            // Hapus foto lama jika ada
            if ($seller->profile_image) {
                $oldImagePath = storage_path('app/public/' . $seller->profile_image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Simpan foto baru
            $fileName = 'profile_image_' . $user->id . '_' . time() . '.' . $avatar->getClientOriginalExtension();
            $path = $avatar->storeAs('seller_profiles', $fileName, 'public');
            $seller->profile_image = $path;
            $seller->save();
        }

        \Log::info('Profile update request - data to be updated', [
            'user_id' => $user->id,
            'user_current_data' => $user->toArray(),
            'update_data' => $updateData
        ]);

        $user->update($updateData);

        // Refresh user data from database to confirm update
        $updatedUserData = $user->fresh();
        \Log::info('Profile updated successfully', [
            'user_id' => $updatedUserData->id,
            'updated_fields' => [
                'name' => $updatedUserData->name,
                'email' => $updatedUserData->email,
                'phone' => $updatedUserData->phone,
                'address' => $updatedUserData->address,
                'shop_description' => $updatedUserData->shop_description,
                'bank_name' => $updatedUserData->bank_name,
                'bank_account_number' => $updatedUserData->bank_account_number,
                'bank_account_name' => $updatedUserData->bank_account_name,
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => [
                'shop_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'shop_description' => $user->shop_description,
                'bank_name' => $user->bank_name,
                'bank_account_number' => $user->bank_account_number,
                'bank_account_name' => $user->bank_account_name,
                'profile_image' => $seller->profile_image ? asset('storage/' . $seller->profile_image) : null,
            ]
        ]);
    }

    // ========================================================================
    // FITUR GEOCODING - KONVERSI ALAMAT TEKS MENJADI KOORDINAT PETA
    // ========================================================================
    // UNTUK SIDANG SKRIPSI:
    // - Fitur ini mengubah alamat lengkap (contoh: "Jl. Sudirman No. 10, Jakarta")
    //   menjadi koordinat latitude & longitude yang bisa ditampilkan di Google Maps
    // - Menggunakan OpenStreetMap Nominatim API (GRATIS, tidak perlu API Key)
    // - Alternatif dari Google Geocoding API yang berbayar
    //
    // ALUR KERJA:
    // 1. Seller mengisi alamat di form profile
    // 2. Sistem otomatis memanggil API Nominatim
    // 3. API mengembalikan koordinat lat/lng
    // 4. Koordinat disimpan ke database untuk fitur "Petunjuk Arah" di halaman toko
    //
    // MENGAPA MENGGUNAKAN NOMINATIM?
    // - Gratis tanpa limitasi ketat untuk skala kecil-menengah
    // - Tidak memerlukan credit card atau API key
    // - Akurasi cukup baik untuk alamat di Indonesia
    // - Cocok untuk skripsi dengan budget terbatas
    // ========================================================================

    /**
     * Mendapatkan koordinat dari alamat menggunakan OpenStreetMap Nominatim API
     *
     * Method private ini digunakan untuk mengkonversi alamat teks menjadi
     * koordinat latitude dan longitude (geocoding).
     *
     * Proses:
     * 1. Encode alamat untuk URL (spasi jadi %20, karakter khusus di-encode)
     * 2. Call API Nominatim OpenStreetMap dengan HTTP GET
     * 3. Parse response JSON yang berisi hasil geocoding
     * 4. Return lat dan lng dalam format float
     *
     * @param string $address Alamat lengkap yang akan dikonversi
     * @return array|null Array dengan 'lat' dan 'lng' atau null jika gagal
     * 
     * @example
     * Input: "Jl. Sudirman, Jakarta Pusat"
     * Output: ['lat' => -6.195172, 'lng' => 106.820673]
     */
    private function getCoordinatesFromAddress($address)
    {
        // ========================================
        // STEP 1: VALIDASI AWAL
        // ========================================
        // Jika alamat kosong, return null
        if (empty($address)) {
            return null;
        }

        // ========================================
        // STEP 2: ENCODE ALAMAT UNTUK URL
        // ========================================
        // Encode alamat agar aman untuk URL
        // Contoh: "Jl. Sudirman No. 10" → "Jl.+Sudirman+No.+10"
        $encodedAddress = urlencode($address);

        // ========================================
        // STEP 3: PANGGIL API NOMINATIM
        // ========================================
        // URL API Nominatim OpenStreetMap untuk geocoding
        // Parameter:
        // - format=json: Response dalam format JSON
        // - q: Query pencarian (alamat yang sudah di-encode)
        // - limit=1: Hanya ambil 1 hasil paling relevan (hemat bandwidth)
        $url = "https://nominatim.openstreetmap.org/search?format=json&q={$encodedAddress}&limit=1";

        // ========================================
        // STEP 4: SETUP cURL UNTUK HTTP REQUEST
        // ========================================
        // Initialize cURL untuk melakukan HTTP GET request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);                    // Set URL target
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);         // Return response sebagai string (bukan output langsung)
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            // User-Agent WAJIB diisi - Nominatim menolak request tanpa User-Agent yang valid
            // Ini adalah requirement dari Nominatim API usage policy
            'User-Agent: EcommerceAkrab/1.0 (contact@ecommerceakrab.com)'
        ]);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);         // Follow redirect jika ada
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);                  // Timeout 30 detik untuk menghindari hanging

        // ========================================
        // STEP 5: EKSEKUSI REQUEST & HANDLE RESPONSE
        // ========================================
        // Execute request ke API Nominatim
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  // Get HTTP status code (200 = success)
        curl_close($ch);  // Tutup cURL session untuk free memory

        // ========================================
        // STEP 6: VALIDASI HTTP RESPONSE
        // ========================================
        // Cek jika request gagal atau HTTP code bukan 200 (OK)
        if ($response === false || $httpCode !== 200) {
            \Log::error('Failed to get coordinates from Nominatim API', [
                'address' => $address,
                'http_code' => $httpCode
            ]);
            return null;  // Return null jika API call gagal
        }

        // ========================================
        // STEP 7: DECODE & PARSE JSON RESPONSE
        // ========================================
        // Decode JSON response menjadi PHP array
        $data = json_decode($response, true);

        // ========================================
        // STEP 8: VALIDASI DATA HASIL
        // ========================================
        // Validasi response - harus ada data dan harus ada lat/lng
        // Nominatim mengembalikan array, bahkan jika tidak ada hasil (array kosong)
        if (empty($data) || !isset($data[0]['lat']) || !isset($data[0]['lon'])) {
            \Log::warning('No coordinates found for address', ['address' => $address]);
            return null;  // Return null jika alamat tidak ditemukan
        }

        // ========================================
        // STEP 9: RETURN KOORDINAT
        // ========================================
        // Return koordinat sebagai float (tipe data numerik desimal)
        // CATATAN: Nominatim menggunakan 'lon' (longitude) bukan 'lng'
        return [
            'lat' => (float) $data[0]['lat'],   // Latitude: -90 sampai +90
            'lng' => (float) $data[0]['lon']    // Longitude: -180 sampai +180
        ];
    }

    /**
     * Geocode address to coordinates - Endpoint AJAX untuk geocoding
     * 
     * Endpoint publik yang dapat dipanggil via AJAX untuk mengkonversi
     * alamat menjadi koordinat. Digunakan saat seller update profil
     * dan ingin mendapatkan koordinat dari alamat yang diinput.
     * 
     * Request:
     * - address (required): Alamat lengkap yang akan dikonversi
     * 
     * Response:
     * - success: boolean
     * - data.lat: Latitude koordinat
     * - data.lng: Longitude koordinat
     * - data.display_name: Nama lengkap alamat dari Nominatim
     * 
     * @param Request $request Objek request HTTP
     * @return \Illuminate\Http\JsonResponse JSON response dengan koordinat
     */
    public function geocodeAddress(Request $request)
    {
        // Validasi input address
        $request->validate([
            'address' => 'required|string|max:255',
        ]);

        $address = $request->input('address');

        // Encode alamat untuk URL
        $encodedAddress = urlencode($address);

        // URL API Nominatim OpenStreetMap
        $url = "https://nominatim.openstreetmap.org/search?format=json&q={$encodedAddress}&limit=1";

        // Setup cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: EcommerceAkrab/1.0 (contact@ecommerceakrab.com)'
        ]);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        // Execute request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Handle error
        if ($response === false || $httpCode !== 200) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungi layanan geocoding'
            ], 500);
        }

        // Decode response
        $data = json_decode($response, true);

        // Jika tidak ada hasil
        if (empty($data) || !isset($data[0]['lat']) || !isset($data[0]['lon'])) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat tidak ditemukan'
            ]);
        }

        // Return koordinat yang berhasil ditemukan
        return response()->json([
            'success' => true,
            'data' => [
                'lat' => (float) $data[0]['lat'],
                'lng' => (float) $data[0]['lon'],
                'display_name' => $data[0]['display_name']  // Nama lengkap alamat
            ]
        ]);
    }

    /**
     * Get seller coordinates for directions - Endpoint untuk Get Directions
     * 
     * Endpoint ini digunakan untuk mendapatkan koordinat seller yang akan
     * ditampilkan di peta untuk fitur Get Directions. Customer memanggil
     * endpoint ini saat ingin melihat arah ke toko seller.
     * 
     * Prioritas pengambilan koordinat:
     * 1. Dari tabel sellers (field lat, lng) - jika seller sudah update koordinat
     * 2. Dari tabel users (field lat, lng) - fallback jika sellers tidak ada
     * 
     * Validasi koordinat:
     * - Tidak boleh null, empty string, 0, atau 'null' (string)
     * - Harus koordinat valid dalam range Indonesia
     * 
     * @param int $sellerId ID penjual yang koordinatnya diminta
     * @return \Illuminate\Http\JsonResponse JSON dengan lat, lng, name, address
     */
    public function getSellerCoordinates($sellerId)
    {
        // Cari seller dengan relasi user
        $seller = \App\Models\Seller::with('user')->find($sellerId);

        // Jika seller tidak ditemukan
        if (!$seller) {
            return response()->json(['error' => 'Seller not found'], 404);
        }

        // Inisialisasi variabel koordinat
        $lat = null;
        $lng = null;

        // PRIORITAS 1: Cek koordinat di tabel sellers terlebih dahulu
        // Validasi: tidak boleh null, empty, 0, atau string 'null'
        if ($seller->lat && $seller->lng && $seller->lat != 'null' && $seller->lng != 'null' &&
            $seller->lat != 0 && $seller->lng != 0 &&
            !is_null($seller->lat) && !is_null($seller->lng) &&
            $seller->lat != '' && $seller->lng != '') {
            $lat = (float)$seller->lat;
            $lng = (float)$seller->lng;
        }

        // PRIORITAS 2: Jika tidak ditemukan di seller, cek di tabel users
        if ((!$lat || !$lng) && $seller->user) {
            if ($seller->user->lat && $seller->user->lng &&
                $seller->user->lat != 'null' && $seller->user->lng != 'null' &&
                $seller->user->lat != 0 && $seller->user->lng != 0 &&
                !is_null($seller->user->lat) && !is_null($seller->user->lng) &&
                $seller->user->lat != '' && $seller->user->lng != '') {
                $lat = (float)$seller->user->lat;
                $lng = (float)$seller->user->lng;
            }
        }

        // Logging untuk debugging jika koordinat tidak ditemukan
        if ($lat === null || $lng === null) {
            \Log::info('Seller coordinates not found', [
                'seller_id' => $sellerId,
                'seller_lat' => $seller->lat,
                'seller_lng' => $seller->lng,
                'user_lat' => $seller->user ? $seller->user->lat : null,
                'user_lng' => $seller->user ? $seller->user->lng : null,
            ]);
        }

        // Return response dengan koordinat (atau null jika tidak ada)
        return response()->json([
            'lat' => $lat,
            'lng' => $lng,
            'name' => $seller->store_name ?? $seller->name,  // Nama toko untuk ditampilkan di map
            'address' => $seller->address ?? $seller->user->address ?? 'Alamat tidak tersedia'  // Alamat untuk ditampilkan
        ]);
    }
}
