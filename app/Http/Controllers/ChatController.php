<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Menampilkan riwayat percakapan dengan pengguna tertentu
     */
    public function getMessages($userId)
    {
        $authenticatedUser = Auth::user();
        
        $messages = ChatMessage::where(function($query) use ($authenticatedUser, $userId) {
                $query->where('sender_id', $authenticatedUser->id)
                      ->where('receiver_id', $userId);
            })
            ->orWhere(function($query) use ($authenticatedUser, $userId) {
                $query->where('sender_id', $userId)
                      ->where('receiver_id', $authenticatedUser->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Tandai pesan yang belum dibaca dari user lain sebagai sudah dibaca
        ChatMessage::where('sender_id', $userId)
                   ->where('receiver_id', $authenticatedUser->id)
                   ->where('read_status', false)
                   ->update([
                       'read_status' => true,
                       'read_at' => now()
                   ]);

        $otherUser = User::find($userId);

        return response()->json([
            'other_user' => $otherUser,
            'messages' => $messages
        ]);
    }

    /**
     * Mengirim pesan baru
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000'
        ]);

        $authenticatedUser = Auth::user();

        // Cek apakah receiver_id valid dan bukan diri sendiri
        if ($request->receiver_id == $authenticatedUser->id) {
            return response()->json([
                'message' => 'Tidak bisa mengirim pesan ke diri sendiri'
            ], 400);
        }

        $message = ChatMessage::create([
            'sender_id' => $authenticatedUser->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'message_type' => 'text'
        ]);

        return response()->json([
            'message' => 'Pesan berhasil dikirim',
            'chat_message' => $message->load(['sender', 'receiver'])
        ]);
    }

    /**
     * Menampilkan daftar kontak yang pernah diajak chat
     */
    public function getContacts()
    {
        $authenticatedUser = Auth::user();

        // Ambil ID pengguna yang pernah mengirim atau menerima pesan
        $contactIds = ChatMessage::where(function($query) use ($authenticatedUser) {
                $query->where('sender_id', $authenticatedUser->id)
                      ->orWhere('receiver_id', $authenticatedUser->id);
            })
            ->selectRaw('CASE 
                            WHEN sender_id = ? THEN receiver_id 
                            ELSE sender_id 
                         END as contact_id', [$authenticatedUser->id])
            ->distinct()
            ->pluck('contact_id');

        $contacts = User::whereIn('id', $contactIds)
                       ->select('id', 'name', 'email')
                       ->get();

        return response()->json([
            'contacts' => $contacts
        ]);
    }

    /**
     * Menandai semua pesan dari pengguna tertentu sebagai sudah dibaca
     */
    public function markAsRead($userId)
    {
        $authenticatedUser = Auth::user();

        ChatMessage::where('sender_id', $userId)
                   ->where('receiver_id', $authenticatedUser->id)
                   ->where('read_status', false)
                   ->update([
                       'read_status' => true,
                       'read_at' => now()
                   ]);

        return response()->json([
            'message' => 'Semua pesan telah ditandai sebagai sudah dibaca'
        ]);
    }
}
