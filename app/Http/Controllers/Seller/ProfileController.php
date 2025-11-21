<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the seller's profile.
     */
    public function edit()
    {
        $user = Auth::user();
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
        $validator = Validator::make($request->all(), [
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
        ], [
            'phone.regex' => 'Nomor telepon harus menggunakan format Indonesia (contoh: +6281234567890)',
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
            ]
        ]);
    }
}
