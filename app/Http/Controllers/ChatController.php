<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// ========================================================================
// CHAT CONTROLLER - PESAN ANTAR USER (MESSAGING SYSTEM)
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Controller ini menangani fitur chat/pesan antar user
// - User bisa kirim pesan ke user lain (buyer ↔ seller, buyer ↔ buyer, dll)
// - Fitur standar e-commerce untuk komunikasi
//
// FITUR UTAMA:
// 1. Send Message - Kirim pesan ke user lain
// 2. View Messages - Lihat riwayat chat dengan user tertentu
// 3. Get Contacts - Daftar kontak yang pernah diajak chat
// 4. Read Status - Tandai pesan sudah dibaca
//
// USE CASE DI E-COMMERCE:
// - Buyer tanya produk ke seller
// - Buyer konfirmasi pengiriman
// - Seller informasikan status order
// - User komunikasi umum
//
// CATATAN:
// - Chat ini BASIC (text only, no real-time)
// - Tidak ada WebSocket (harus refresh manual)
// - Tidak ada image/file sharing
// - Tidak ada push notification
// - Untuk production: perlu upgrade ke real-time messaging
// ========================================================================

class ChatController extends Controller
{
    /**
     * Menampilkan riwayat percakapan dengan pengguna tertentu
     * 
     * ==========================================================================
     * FITUR: VIEW MESSAGES - LIHAT RIWAYAT CHAT
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini tampilkan riwayat chat antara 2 user
     * - Load semua pesan (sender & receiver) antara user yang login & user target
     * - Auto mark pesan yang belum dibaca sebagai sudah dibaca
     * 
     * FLOW:
     * 1. Ambil user yang login (authenticated user)
     * 2. Query semua pesan antara user A & user B
     * 3. Order by created_at asc (dari yang paling lama)
     * 4. Mark pesan yang belum dibaca sebagai sudah dibaca
     * 5. Return JSON dengan messages & user info
     * 
     * QUERY LOGIC:
     * WHERE (sender_id = user_A AND receiver_id = user_B)
     *    OR (sender_id = user_B AND receiver_id = user_A)
     * ORDER BY created_at ASC
     * 
     * @param int $userId ID user yang diajak chat
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessages($userId)
    {
        // ========================================
        // STEP 1: AMBIL AUTHENTICATED USER
        // ========================================
        $authenticatedUser = Auth::user();

        // ========================================
        // STEP 2: QUERY PESAN ANTARA 2 USER
        // ========================================
        // Query semua pesan antara user yang login & user target
        // Logic: Pesan dari A ke B ATAU dari B ke A
        $messages = ChatMessage::where(function($query) use ($authenticatedUser, $userId) {
                $query->where('sender_id', $authenticatedUser->id)  // Pesan dari user yang login
                      ->where('receiver_id', $userId);               // Ke user target
            })
            ->orWhere(function($query) use ($authenticatedUser, $userId) {
                $query->where('sender_id', $userId)                   // Pesan dari user target
                      ->where('receiver_id', $authenticatedUser->id); // Ke user yang login
            })
            ->orderBy('created_at', 'asc')  // Urutkan dari yang paling lama (chat history)
            ->get();

        // ========================================
        // STEP 3: MARK PESAN SEBAGAI SUDAH DIBACA
        // ========================================
        // Tandai pesan yang belum dibaca dari user lain sebagai sudah dibaca
        // Update read_status = true dan read_at = now()
        ChatMessage::where('sender_id', $userId)
                   ->where('receiver_id', $authenticatedUser->id)
                   ->where('read_status', false)
                   ->update([
                       'read_status' => true,
                       'read_at' => now()
                   ]);

        // ========================================
        // STEP 4: AMBIL INFO USER TARGET
        // ========================================
        $otherUser = User::find($userId);

        // ========================================
        // STEP 5: RETURN JSON RESPONSE
        // ========================================
        return response()->json([
            'other_user' => $otherUser,  // Info user yang diajak chat
            'messages' => $messages      // Riwayat pesan
        ]);
    }

    /**
     * Mengirim pesan baru
     * 
     * ==========================================================================
     * FITUR: SEND MESSAGE - KIRIM PESAN BARU
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini handle kirim pesan baru ke user lain
     * - Validasi: receiver_id valid, bukan diri sendiri
     * - Pesan max 1000 karakter
     * - message_type = 'text' (bisa dikembangkan ke image/file)
     * 
     * VALIDASI:
     * - receiver_id: Required, harus ada di tabel users
     * - message: Required, string, max 1000 karakter
     * - Tidak boleh kirim pesan ke diri sendiri
     * 
     * FLOW:
     * 1. Validasi input form
     * 2. Cek apakah receiver_id valid
     * 3. Cek apakah tidak kirim ke diri sendiri
     * 4. Buat ChatMessage record
     * 5. Return JSON dengan pesan yang dikirim
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        // ========================================
        // STEP 1: VALIDASI INPUT
        // ========================================
        $request->validate([
            'receiver_id' => 'required|exists:users,id',  // Receiver harus valid
            'message' => 'required|string|max:1000'  // Max 1000 karakter
        ]);

        // ========================================
        // STEP 2: AMBIL AUTHENTICATED USER
        // ========================================
        $authenticatedUser = Auth::user();

        // ========================================
        // STEP 3: CEK TIDAK KIRIM KE DIRI SENDIRI
        // ========================================
        // Cek apakah receiver_id valid dan bukan diri sendiri
        if ($request->receiver_id == $authenticatedUser->id) {
            return response()->json([
                'message' => 'Tidak bisa mengirim pesan ke diri sendiri'
            ], 400);
        }

        // ========================================
        // STEP 4: BUAT PESAN BARU
        // ========================================
        // Create ChatMessage record
        $message = ChatMessage::create([
            'sender_id' => $authenticatedUser->id,  // User yang kirim
            'receiver_id' => $request->receiver_id,  // User yang terima
            'message' => $request->message,  // Isi pesan
            'message_type' => 'text'  // Tipe pesan (text, image, file - akan dikembangkan)
        ]);

        // ========================================
        // STEP 5: RETURN JSON RESPONSE
        // ========================================
        // Load relasi sender & receiver untuk info lengkap
        return response()->json([
            'message' => 'Pesan berhasil dikirim',
            'chat_message' => $message->load(['sender', 'receiver'])
        ]);
    }

    /**
     * Menampilkan daftar kontak yang pernah diajak chat
     * 
     * ==========================================================================
     * FITUR: GET CONTACTS - DAFTAR KONTAK CHAT
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini tampilkan daftar user yang pernah diajak chat
     * - Query semua unique contact IDs (sender atau receiver)
     * - Load info user (name, email) untuk tampilan
     * 
     * QUERY LOGIC:
     * 1. Ambil semua pesan dari/ke user yang login
     * 2. Extract contact_id (jika saya sender → contact = receiver, dan sebaliknya)
     * 3. DISTINCT untuk dapat unique contacts
     * 4. Load user info untuk setiap contact
     * 
     * USE CASE:
     * - Tampilkan daftar chat di sidebar
     * - Quick access ke chat history
     * - Lihat siapa yang pernah komunikasi
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContacts()
    {
        // ========================================
        // STEP 1: AMBIL AUTHENTICATED USER
        // ========================================
        $authenticatedUser = Auth::user();

        // ========================================
        // STEP 2: QUERY UNIQUE CONTACT IDS
        // ========================================
        // Ambil ID pengguna yang pernah mengirim atau menerima pesan
        // CASE logic: Jika saya sender → contact = receiver, jika saya receiver → contact = sender
        $contactIds = ChatMessage::where(function($query) use ($authenticatedUser) {
                $query->where('sender_id', $authenticatedUser->id)  // Saya sender
                      ->orWhere('receiver_id', $authenticatedUser->id);  // Atau saya receiver
            })
            ->selectRaw('CASE
                            WHEN sender_id = ? THEN receiver_id
                            ELSE sender_id
                         END as contact_id', [$authenticatedUser->id])  // Parameterized query untuk security
            ->distinct()  // Hanya unique contact IDs
            ->pluck('contact_id');  // Get array of contact IDs

        // ========================================
        // STEP 3: LOAD USER INFO UNTUK CONTACTS
        // ========================================
        // Load info user (id, name, email) untuk setiap contact
        $contacts = User::whereIn('id', $contactIds)
                       ->select('id', 'name', 'email')  // Hanya field yang diperlukan
                       ->get();

        // ========================================
        // STEP 4: RETURN JSON RESPONSE
        // ========================================
        return response()->json([
            'contacts' => $contacts
        ]);
    }

    /**
     * Menandai semua pesan dari pengguna tertentu sebagai sudah dibaca
     * 
     * ==========================================================================
     * FITUR: MARK AS READ - TANDAI PESAN SUDAH DIBACA
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini mark semua pesan dari user tertentu sebagai sudah dibaca
     * - Dipanggil saat user buka chat dengan user tertentu
     * - Update read_status = true untuk semua pesan yang belum dibaca
     * 
     * FLOW:
     * 1. Ambil authenticated user
     * 2. Query semua pesan dari user target yang belum dibaca
     * 3. Update read_status = true, read_at = now()
     * 4. Return success message
     *
     * @param int $userId ID user yang pesannya akan di-mark sebagai read
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($userId)
    {
        // ========================================
        // STEP 1: AMBIL AUTHENTICATED USER
        // ========================================
        $authenticatedUser = Auth::user();

        // ========================================
        // STEP 2: UPDATE READ STATUS
        // ========================================
        // Tandai semua pesan dari user tertentu sebagai sudah dibaca
        ChatMessage::where('sender_id', $userId)  // Pesan dari user target
                   ->where('receiver_id', $authenticatedUser->id)  // Ke user yang login
                   ->where('read_status', false)  // Yang belum dibaca saja
                   ->update([
                       'read_status' => true,  // Mark sebagai sudah dibaca
                       'read_at' => now()  // Timestamp saat dibaca
                   ]);

        // ========================================
        // STEP 3: RETURN SUCCESS MESSAGE
        // ========================================
        return response()->json([
            'message' => 'Semua pesan telah ditandai sebagai sudah dibaca'
        ]);
    }
}
