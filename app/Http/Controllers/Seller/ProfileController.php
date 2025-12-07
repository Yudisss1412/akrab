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

        // Jika alamat berubah, coba geocode dan simpan koordinat
        if ($request->input('address') !== $user->address) {
            $address = $request->input('address');
            $coordinates = $this->getCoordinatesFromAddress($address);

            if ($coordinates) {
                $updateData['lat'] = $coordinates['lat'];
                $updateData['lng'] = $coordinates['lng'];
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

    /**
     * Get coordinates from address using OpenStreetMap Nominatim API
     */
    private function getCoordinatesFromAddress($address)
    {
        if (empty($address)) {
            return null;
        }

        $encodedAddress = urlencode($address);
        $url = "https://nominatim.openstreetmap.org/search?format=json&q={$encodedAddress}&limit=1";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: EcommerceAkrab/1.0 (contact@ecommerceakrab.com)'
        ]);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            \Log::error('Failed to get coordinates from Nominatim API', [
                'address' => $address,
                'http_code' => $httpCode
            ]);
            return null;
        }

        $data = json_decode($response, true);

        if (empty($data) || !isset($data[0]['lat']) || !isset($data[0]['lon'])) {
            \Log::warning('No coordinates found for address', ['address' => $address]);
            return null;
        }

        return [
            'lat' => (float) $data[0]['lat'],
            'lng' => (float) $data[0]['lon']
        ];
    }

    /**
     * Geocode address to coordinates using OpenStreetMap Nominatim API
     */
    public function geocodeAddress(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255',
        ]);

        $address = $request->input('address');

        // Encode the address for URL
        $encodedAddress = urlencode($address);

        // Call OpenStreetMap Nominatim API
        $url = "https://nominatim.openstreetmap.org/search?format=json&q={$encodedAddress}&limit=1";

        // Set up cURL options
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: EcommerceAkrab/1.0 (contact@ecommerceakrab.com)' // Nominatim requires a proper user agent
        ]);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungi layanan geocoding'
            ], 500);
        }

        $data = json_decode($response, true);

        if (empty($data) || !isset($data[0]['lat']) || !isset($data[0]['lon'])) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat tidak ditemukan'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'lat' => (float) $data[0]['lat'],
                'lng' => (float) $data[0]['lon'],
                'display_name' => $data[0]['display_name']
            ]
        ]);
    }
}
