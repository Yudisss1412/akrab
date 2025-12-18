<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        // Check if there are any real tickets in the database
        $hasRealTickets = Ticket::exists();

        if (!$hasRealTickets) {
            // No real tickets exist, show filtered dummy data
            return $this->showFilteredDummyTickets($request);
        }

        $query = Ticket::with('user', 'assignee');

        // Filter berdasarkan kategori jika ada
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter berdasarkan status jika ada
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan priority jika ada
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter berdasarkan pencarian jika ada
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'LIKE', "%{$search}%")
                  ->orWhere('message', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.support_tickets', compact('tickets'));
    }

    public function show($id)
    {
        // Add debug logging to help identify the issue
        \Log::info('Accessing ticket with ID: ' . $id . ' by user ID: ' . (auth()->id() ?? 'null'));

        try {
            // Check if user is authenticated before attempting to find the ticket
            // This could save a database query if the user isn't logged in
            if (!auth()->check()) {
                abort(403, 'Anda harus login terlebih dahulu');
            }

            // Check if there are real tickets in the database
            $hasRealTickets = Ticket::exists();

            $ticket = null;

            if ($hasRealTickets) {
                // Try to find the actual ticket
                $ticket = Ticket::with('user', 'assignee')->findOrFail($id);
            } else {
                // No real tickets exist, try to create a dummy ticket if the ID is within range
                // Use the same logic as in showFilteredDummyTickets to generate dummy data
                $users = \App\Models\User::limit(10)->get();
                if ($users->isEmpty()) {
                    $users = collect([
                        (object) ['id' => 1, 'name' => 'Ahmad Santoso', 'email' => 'ahmad@example.com'],
                        (object) ['id' => 2, 'name' => 'Budi Prasetyo', 'email' => 'budi@example.com'],
                        (object) ['id' => 3, 'name' => 'Siti Rahayu', 'email' => 'siti@example.com'],
                        (object) ['id' => 4, 'name' => 'Joko Widodo', 'email' => 'joko@example.com'],
                        (object) ['id' => 5, 'name' => 'Lina Marlina', 'email' => 'lina@example.com'],
                        (object) ['id' => 6, 'name' => 'Rina Kusuma', 'email' => 'rina@example.com'],
                        (object) ['id' => 7, 'name' => 'Agus Setiawan', 'email' => 'agus@example.com'],
                        (object) ['id' => 8, 'name' => 'Dewi Anggraini', 'email' => 'dewi@example.com'],
                        (object) ['id' => 9, 'name' => 'Fajar Pamungkas', 'email' => 'fajar@example.com'],
                        (object) ['id' => 10, 'name' => 'Tina Nurhayati', 'email' => 'tina@example.com'],
                    ]);
                }

                // Check if this ID would exist in dummy data (1-15)
                if ($id >= 1 && $id <= 15) {
                    $categories = ['technical', 'billing', 'account', 'product', 'other'];
                    $priorities = ['low', 'medium', 'high', 'urgent'];
                    $statuses = ['open', 'in_progress', 'resolved', 'closed'];

                    $subjects = [
                        'Kesulitan saat login ke akun',
                        'Pembayaran tidak terproses',
                        'Produk tidak sesuai deskripsi',
                        'Masalah teknis di website',
                        'Pertanyaan tentang kebijakan retur',
                        'Kesalahan harga produk',
                        'Kesulitan saat checkout',
                        'Akun saya diblokir',
                        'Pengiriman terlambat',
                        'Tidak bisa mengunggah foto profil'
                    ];

                    $messages = [
                        'Saya mengalami kesulitan saat mencoba login ke akun saya. Setiap kali saya memasukkan password, sistem mengata니다 password salah padahal saya yakin benar.',
                        'Pembayaran saya tidak terproses dengan status pending lebih dari 24 jam. Mohon bantuannya untuk mempercepat proses verifikasi.',
                        'Produk yang saya terima tidak sesuai dengan deskripsi di website. Warna dan ukuran berbeda dari yang saya pesan.',
                        'Website sering loading lama dan kadang tidak merespon. Ini mengganggu pengalaman belanja saya.',
                        'Saya ingin bertanya tentang kebijakan retur barang jika produk yang diterima cacat atau rusak.',
                        'Saya menemukan kesalahan harga di beberapa produk. Harga tertulis lebih murah dari seharusnya, apakah ini promo atau kesalahan sistem?',
                        'Saya mengalami kesulitan saat proses checkout. Tombol "proses pembayaran" tidak merespon saat saya klik.',
                        'Akun saya tiba-tiba diblokir tanpa pemberitahuan sebelumnya. Mohon penjelasan dan bantuan untuk membukanya kembali.',
                        'Pengiriman saya terlambat beberapa hari dari estimasi yang diberikan. Mohon informasi terkini mengenai status pengiriman.',
                        'Saya tidak bisa mengunggah foto profil di halaman pengaturan akun. Setiap kali mencoba, muncul pesan error.'
                    ];

                    // Create dummy ticket object
                    $ticket = new \stdClass();
                    $ticket->id = $id;
                    $ticket->subject = $subjects[($id - 1) % count($subjects)];
                    $ticket->message = $messages[($id - 1) % count($messages)];
                    $ticket->category = $categories[($id - 1) % count($categories)];
                    $ticket->priority = $priorities[array_rand($priorities)];
                    $ticket->status = $statuses[array_rand($statuses)];
                    $ticket->user_id = $users->get(($id - 1) % $users->count())->id;
                    $ticket->user = $users->get(($id - 1) % $users->count());
                    $ticket->assignee = null;
                    $ticket->assignee_id = null;
                    $ticket->created_at = now()->subDays(rand(0, 30))->subHours(rand(0, 24))->subMinutes(rand(0, 60));
                    $ticket->updated_at = now();
                    $ticket->resolved_at = $ticket->status === 'resolved' ? $ticket->created_at->addHours(rand(1, 48)) : null;
                    $ticket->resolution_notes = $ticket->status === 'resolved' ? 'Masalah telah diselesaikan sesuai dengan permintaan pelanggan.' : null;
                }
            }

            // If ticket still null, it means it wasn't found in real data or valid dummy range
            if (!$ticket) {
                \Log::error('Ticket not found - neither real nor in dummy range: ' . $id);
                abort(404, 'Tiket tidak ditemukan');
            }

            // Check if user is admin or the owner of the ticket
            $user = auth()->user();

            // Ensure user object is available
            if (!$user) {
                abort(403, 'Anda harus login terlebih dahulu');
            }

            // Check if user has admin/staff/support role by checking the role relationship
            $isAdmin = false;
            if ($user->role && in_array($user->role->name, ['admin', 'staff', 'support'])) {
                $isAdmin = true;
            }

            if ($isAdmin) {
                // Admin/staff view
                return view('admin.ticket_detail', compact('ticket'));
            } elseif (auth()->id() === $ticket->user_id) {
                // Customer view - user accessing their own ticket
                return view('customer.tickets.detail', compact('ticket'));
            } else {
                // Unauthorized access
                abort(403, 'Anda tidak memiliki akses ke tiket ini');
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error accessing ticket ID ' . $id . ': ' . $e->getMessage());

            // Jika ticket tidak ditemukan
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                abort(404, 'Tiket tidak ditemukan');
            } else {
                // Log error atau tampilkan error lainnya
                \Log::error('Error accessing ticket: ' . $e->getMessage());
                abort(500, 'Terjadi kesalahan saat mengakses tiket');
            }
        }
    }

    public function create()
    {
        return view('customer.tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'category' => 'required|in:technical,billing,account,product,other',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'message' => $request->message,
            'category' => $request->category,
            'priority' => $request->priority,
            'status' => 'open',
        ]);

        return redirect()->route('customer.tickets')
            ->with('success', 'Tiket bantuan berhasil dibuat!');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'nullable|in:open,in_progress,resolved,closed',
            'priority' => 'nullable|in:low,medium,high',
            'assignee_id' => 'nullable|exists:users,id',
            'resolution_notes' => 'nullable|string',
        ]);

        $ticket = Ticket::findOrFail($id);

        // Prepare update data
        $updateData = [];

        if ($request->has('status') && !empty($request->status)) {
            $updateData['status'] = $request->status;
            $updateData['resolved_at'] = $request->status === 'resolved' ? now() : null;
        }

        if ($request->has('priority') && !empty($request->priority)) {
            $updateData['priority'] = $request->priority;
        }

        if ($request->has('assignee_id')) {
            $updateData['assignee_id'] = $request->assignee_id;
        }

        if ($request->has('resolution_notes')) {
            $updateData['resolution_notes'] = $request->resolution_notes;
        }

        // Update ticket with validated data
        $ticket->update($updateData);

        return response()->json(['success' => true, 'message' => 'Tiket berhasil diperbarui']);
    }

    public function getTicketsByUser()
    {
        $tickets = Ticket::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.tickets.index', compact('tickets'));
    }

    public function apiGetTickets(Request $request)
    {
        // Check if there are any real tickets in the database
        $hasRealTickets = Ticket::exists();

        if (!$hasRealTickets) {
            // No real tickets exist, return filtered dummy data
            return response()->json(['tickets' => $this->getFilteredDummyTicketsForAPI($request)]);
        }

        $tickets = Ticket::with('user', 'assignee')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'subject' => $ticket->subject,
                    'message' => \Str::limit($ticket->message, 100),
                    'category' => $ticket->category,
                    'priority' => $ticket->priority,
                    'status' => $ticket->status,
                    'user_name' => $ticket->user->name ?? 'N/A',
                    'assignee_name' => $ticket->assignee->name ?? 'Unassigned',
                    'created_at' => $ticket->created_at->format('d M Y H:i'),
                    'resolved_at' => $ticket->resolved_at ? $ticket->resolved_at->format('d M Y H:i') : null,
                ];
            });

        return response()->json(['tickets' => $tickets]);
    }

    public function getTicketMessages($id)
    {
        // Add debug logging
        \Log::info('API getTicketMessages called for ticket ID: ' . $id);

        // Check if there are real tickets in the database
        $hasRealTickets = Ticket::exists();

        if ($hasRealTickets) {
            // Try to find the actual ticket
            $ticket = Ticket::with('user')->findOrFail($id);
        } else {
            // No real tickets exist, try to create a dummy ticket if the ID is within range
            $users = \App\Models\User::limit(10)->get();
            if ($users->isEmpty()) {
                $users = collect([
                    (object) ['id' => 1, 'name' => 'Ahmad Santoso', 'email' => 'ahmad@example.com'],
                    (object) ['id' => 2, 'name' => 'Budi Prasetyo', 'email' => 'budi@example.com'],
                    (object) ['id' => 3, 'name' => 'Siti Rahayu', 'email' => 'siti@example.com'],
                    (object) ['id' => 4, 'name' => 'Joko Widodo', 'email' => 'joko@example.com'],
                    (object) ['id' => 5, 'name' => 'Lina Marlina', 'email' => 'lina@example.com'],
                    (object) ['id' => 6, 'name' => 'Rina Kusuma', 'email' => 'rina@example.com'],
                    (object) ['id' => 7, 'name' => 'Agus Setiawan', 'email' => 'agus@example.com'],
                    (object) ['id' => 8, 'name' => 'Dewi Anggraini', 'email' => 'dewi@example.com'],
                    (object) ['id' => 9, 'name' => 'Fajar Pamungkas', 'email' => 'fajar@example.com'],
                    (object) ['id' => 10, 'name' => 'Tina Nurhayati', 'email' => 'tina@example.com'],
                ]);
            }

            // Check if this ID would exist in dummy data (1-15)
            if ($id >= 1 && $id <= 15) {
                $categories = ['technical', 'billing', 'account', 'product', 'other'];
                $priorities = ['low', 'medium', 'high', 'urgent'];
                $statuses = ['open', 'in_progress', 'resolved', 'closed'];

                $subjects = [
                    'Kesulitan saat login ke akun',
                    'Pembayaran tidak terproses',
                    'Produk tidak sesuai deskripsi',
                    'Masalah teknis di website',
                    'Pertanyaan tentang kebijakan retur',
                    'Kesalahan harga produk',
                    'Kesulitan saat checkout',
                    'Akun saya diblokir',
                    'Pengiriman terlambat',
                    'Tidak bisa mengunggah foto profil'
                ];

                $messages_arr = [
                    'Saya mengalami kesulitan saat mencoba login ke akun saya. Setiap kali saya memasukkan password, sistem mengatanda password salah padahal saya yakin benar.',
                    'Pembayaran saya tidak terproses dengan status pending lebih dari 24 jam. Mohon bantuannya untuk mempercepat proses verifikasi.',
                    'Produk yang saya terima tidak sesuai dengan deskripsi di website. Warna dan ukuran berbeda dari yang saya pesan.',
                    'Website sering loading lama dan kadang tidak merespon. Ini mengganggu pengalaman belanja saya.',
                    'Saya ingin bertanya tentang kebijakan retur barang jika produk yang diterima cacat atau rusak.',
                    'Saya menemukan kesalahan harga di beberapa produk. Harga tertulis lebih murah dari seharusnya, apakah ini promo atau kesalahan sistem?',
                    'Saya mengalami kesulitan saat proses checkout. Tombol "proses pembayaran" tidak merespon saat saya klik.',
                    'Akun saya tiba-tiba diblokir tanpa pemberitahuan sebelumnya. Mohon penjelasan dan bantuan untuk membukanya kembali.',
                    'Pengiriman saya terlambat beberapa hari dari estimasi yang diberikan. Mohon informasi terkini mengenai status pengiriman.',
                    'Saya tidak bisa mengunggah foto profil di halaman pengaturan akun. Setiap kali mencoba, muncul pesan error.'
                ];

                // Create dummy ticket object
                $ticket = new \stdClass();
                $ticket->id = $id;
                $ticket->subject = $subjects[($id - 1) % count($subjects)];
                $ticket->message = $messages_arr[($id - 1) % count($messages_arr)];
                $ticket->category = $categories[($id - 1) % count($categories)];
                $ticket->priority = $priorities[array_rand($priorities)];
                $ticket->status = $statuses[array_rand($statuses)];
                $ticket->user_id = $users->get(($id - 1) % $users->count())->id;
                $ticket->user = $users->get(($id - 1) % $users->count());
                $ticket->assignee = null;
                $ticket->assignee_id = null;
                $ticket->created_at = now()->subDays(rand(0, 30))->subHours(rand(0, 24))->subMinutes(rand(0, 60));
                $ticket->updated_at = now();
                $ticket->resolved_at = $ticket->status === 'resolved' ? $ticket->created_at->addHours(rand(1, 48)) : null;
                $ticket->resolution_notes = $ticket->status === 'resolved' ? 'Masalah telah diselesaikan sesuai dengan permintaan pelanggan.' : null;
            } else {
                // If ID is not in dummy range, return 404
                \Log::error('Ticket not found in dummy range: ' . $id);
                abort(404, 'Tiket tidak ditemukan');
            }
        }

        // Format tiket awal sebagai message pertama
        $messages = [
            [
                'id' => 'ticket-'.$ticket->id,
                'message' => $ticket->message,
                'sender_id' => $ticket->user_id,
                'sender_name' => $ticket->user->name ?? 'N/A',
                'created_at' => $ticket->created_at->format('d M Y, H:i'),
                'is_ticket_message' => true
            ]
        ];

        // Tambahkan balasan tiket
        $replies = \App\Models\TicketReply::where('ticket_id', $id)
            ->with(['user', 'user.role'])
            ->orderBy('created_at', 'asc')
            ->get();

        \Log::info('Found ' . $replies->count() . ' replies for ticket ID: ' . $id);

        foreach ($replies as $reply) {
            $messages[] = [
                'id' => 'reply-'.$reply->id,
                'message' => $reply->message,
                'sender_id' => $reply->user_id,
                'sender_name' => $reply->user->name ?? 'N/A',
                'sender_role' => $reply->user->role->name ?? 'user', // untuk menentukan tampilan (admin/customer)
                'created_at' => $reply->created_at->format('d M Y, H:i'),
                'is_reply' => true,
                'is_internal_note' => $reply->is_internal_note
            ];
        }

        // Tambahkan catatan penyelesaian jika ada
        if($ticket->resolution_notes) {
            $messages[] = [
                'id' => 'resolution-'.$ticket->id,
                'message' => $ticket->resolution_notes,
                'sender_id' => $ticket->assignee_id ?? null,
                'sender_name' => $ticket->assignee->name ?? 'System',
                'created_at' => $ticket->resolved_at ? $ticket->resolved_at->format('d M Y, H:i') : $ticket->updated_at->format('d M Y, H:i'),
                'is_resolution_note' => true
            ];
        }

        \Log::info('Returning ' . count($messages) . ' messages for ticket ID: ' . $id);

        return response()->json(['messages' => $messages]);
    }

    public function addTicketReply(Request $request, $id)
    {
        // Add debug logging to track API calls
        \Log::info('API addTicketReply called with ID: ' . $id . ' by user: ' . (Auth::id() ?? 'null'));
        \Log::info('Request data: ', $request->all());

        // Verify user is authenticated
        if (!Auth::check()) {
            \Log::warning('Unauthenticated user tried to add ticket reply');
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $request->validate([
            'message' => 'required|string|min:1',
            'is_internal_note' => 'boolean'
        ]);

        // Check if there are real tickets in the database
        $hasRealTickets = Ticket::exists();

        if ($hasRealTickets) {
            // Try to find the actual ticket
            $ticket = Ticket::findOrFail($id);
        } else {
            // No real tickets exist, try to create a dummy ticket if the ID is within range
            $users = \App\Models\User::limit(10)->get();
            if ($users->isEmpty()) {
                $users = collect([
                    (object) ['id' => 1, 'name' => 'Ahmad Santoso', 'email' => 'ahmad@example.com'],
                    (object) ['id' => 2, 'name' => 'Budi Prasetyo', 'email' => 'budi@example.com'],
                    (object) ['id' => 3, 'name' => 'Siti Rahayu', 'email' => 'siti@example.com'],
                    (object) ['id' => 4, 'name' => 'Joko Widodo', 'email' => 'joko@example.com'],
                    (object) ['id' => 5, 'name' => 'Lina Marlina', 'email' => 'lina@example.com'],
                    (object) ['id' => 6, 'name' => 'Rina Kusuma', 'email' => 'rina@example.com'],
                    (object) ['id' => 7, 'name' => 'Agus Setiawan', 'email' => 'agus@example.com'],
                    (object) ['id' => 8, 'name' => 'Dewi Anggraini', 'email' => 'dewi@example.com'],
                    (object) ['id' => 9, 'name' => 'Fajar Pamungkas', 'email' => 'fajar@example.com'],
                    (object) ['id' => 10, 'name' => 'Tina Nurhayati', 'email' => 'tina@example.com'],
                ]);
            }

            // Check if this ID would exist in dummy data (1-15)
            if ($id >= 1 && $id <= 15) {
                $categories = ['technical', 'billing', 'account', 'product', 'other'];
                $priorities = ['low', 'medium', 'high', 'urgent'];
                $statuses = ['open', 'in_progress', 'resolved', 'closed'];

                $subjects = [
                    'Kesulitan saat login ke akun',
                    'Pembayaran tidak terproses',
                    'Produk tidak sesuai deskripsi',
                    'Masalah teknis di website',
                    'Pertanyaan tentang kebijakan retur',
                    'Kesalahan harga produk',
                    'Kesulitan saat checkout',
                    'Akun saya diblokir',
                    'Pengiriman terlambat',
                    'Tidak bisa mengunggah foto profil'
                ];

                $messages_arr = [
                    'Saya mengalami kesulitan saat mencoba login ke akun saya. Setiap kali saya memasukkan password, sistem mengatanda password salah padahal saya yakin benar.',
                    'Pembayaran saya tidak terproses dengan status pending lebih dari 24 jam. Mohon bantuannya untuk mempercepat proses verifikasi.',
                    'Produk yang saya terima tidak sesuai dengan deskripsi di website. Warna dan ukuran berbeda dari yang saya pesan.',
                    'Website sering loading lama dan kadang tidak merespon. Ini mengganggu pengalaman belanja saya.',
                    'Saya ingin bertanya tentang kebijakan retur barang jika produk yang diterima cacat atau rusak.',
                    'Saya menemukan kesalahan harga di beberapa produk. Harga tertulis lebih murah dari seharusnya, apakah ini promo atau kesalahan sistem?',
                    'Saya mengalami kesulitan saat proses checkout. Tombol "proses pembayaran" tidak merespon saat saya klik.',
                    'Akun saya tiba-tiba diblokir tanpa pemberitahuan sebelumnya. Mohon penjelasan dan bantuan untuk membukanya kembali.',
                    'Pengiriman saya terlambat beberapa hari dari estimasi yang diberikan. Mohon informasi terkini mengenai status pengiriman.',
                    'Saya tidak bisa mengunggah foto profil di halaman pengaturan akun. Setiap kali mencoba, muncul pesan error.'
                ];

                // Create dummy ticket object
                $ticket = new \stdClass();
                $ticket->id = $id;
                $ticket->subject = $subjects[($id - 1) % count($subjects)];
                $ticket->message = $messages_arr[($id - 1) % count($messages_arr)];
                $ticket->category = $categories[($id - 1) % count($categories)];
                $ticket->priority = $priorities[array_rand($priorities)];
                $ticket->status = $statuses[array_rand($statuses)];
                $ticket->user_id = $users->get(($id - 1) % $users->count())->id;
                $ticket->user = $users->get(($id - 1) % $users->count());
                $ticket->assignee = null;
                $ticket->assignee_id = null;
                $ticket->created_at = now()->subDays(rand(0, 30))->subHours(rand(0, 24))->subMinutes(rand(0, 60));
                $ticket->updated_at = now();
                $ticket->resolved_at = $ticket->status === 'resolved' ? $ticket->created_at->addHours(rand(1, 48)) : null;
                $ticket->resolution_notes = $ticket->status === 'resolved' ? 'Masalah telah diselesaikan sesuai dengan permintaan pelanggan.' : null;
            } else {
                // If ID is not in dummy range, return 404
                \Log::error('Ticket not found in dummy range: ' . $id);
                abort(404, 'Tiket tidak ditemukan');
            }
        }

        // Check if user has permission to reply to this ticket
        $user = Auth::user();
        $isAdmin = $user->role && in_array($user->role->name, ['admin', 'staff', 'support']);

        if (!$isAdmin && $ticket->user_id != $user->id) {
            \Log::warning('Unauthorized user tried to reply to ticket ' . $id . ', user: ' . $user->id);
            return response()->json(['error' => 'Unauthorized to reply to this ticket'], 403);
        }

        // Create new ticket reply
        $reply = \App\Models\TicketReply::create([
            'ticket_id' => $id, // Use the ID parameter directly
            'user_id' => Auth::id(),
            'message' => $request->message,
            'is_internal_note' => $request->boolean('is_internal_note', false)
        ]);

        \Log::info('Ticket reply created successfully: ' . $reply->id);

        // Update ticket status to 'in_progress' when admin replies
        if (!$reply->is_internal_note) {
            if ($hasRealTickets) {
                $ticket->update(['status' => 'in_progress']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Balasan tiket berhasil ditambahkan',
            'reply' => [
                'id' => 'reply-'.$reply->id,
                'message' => $reply->message,
                'sender_id' => $reply->user_id,
                'sender_name' => $reply->user->name ?? 'N/A',
                'sender_role' => $reply->user->role->name ?? 'user',
                'created_at' => $reply->created_at->format('d M Y, H:i'),
                'is_reply' => true,
                'is_internal_note' => $reply->is_internal_note
            ]
        ]);
    }

    public function deleteTicketReply(Request $request, $ticketId, $replyId)
    {
        // Add debug logging
        \Log::info('API deleteTicketReply called for ticket ID: ' . $ticketId . ' and reply ID: ' . $replyId . ' by user: ' . (Auth::id() ?? 'null'));

        // Verify user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $user = Auth::user();
        $isAdmin = $user->role && in_array($user->role->name, ['admin', 'staff', 'support']);

        // Find the ticket reply
        $reply = \App\Models\TicketReply::findOrFail($replyId);

        // Check if user is the creator of the reply or an admin
        if ($reply->user_id !== $user->id && !$isAdmin) {
            return response()->json(['error' => 'Unauthorized to delete this reply'], 403);
        }

        // Verify that the reply belongs to the correct ticket
        if ($reply->ticket_id != $ticketId) {
            return response()->json(['error' => 'Reply does not belong to this ticket'], 403);
        }

        // Delete the reply
        $reply->delete();

        return response()->json([
            'success' => true,
            'message' => 'Balasan tiket berhasil dihapus'
        ]);
    }

    /**
     * Show filtered dummy tickets when there are no real tickets in the database
     */
    private function showFilteredDummyTickets($request)
    {
        // Create dummy tickets collection
        $dummyTickets = collect();

        // Get real users from the database to use as ticket creators
        $users = \App\Models\User::limit(10)->get();
        if ($users->isEmpty()) {
            // If no users exist, create default users
            $users = collect([
                (object) ['id' => 1, 'name' => 'Ahmad Santoso'],
                (object) ['id' => 2, 'name' => 'Budi Prasetyo'],
                (object) ['id' => 3, 'name' => 'Siti Rahayu'],
                (object) ['id' => 4, 'name' => 'Joko Widodo'],
                (object) ['id' => 5, 'name' => 'Lina Marlina'],
                (object) ['id' => 6, 'name' => 'Rina Kusuma'],
                (object) ['id' => 7, 'name' => 'Agus Setiawan'],
                (object) ['id' => 8, 'name' => 'Dewi Anggraini'],
                (object) ['id' => 9, 'name' => 'Fajar Pamungkas'],
                (object) ['id' => 10, 'name' => 'Tina Nurhayati'],
            ]);
        }

        // Get staff users for assignees
        $staffUsers = \App\Models\User::whereHas('role', function($query) {
            $query->whereIn('name', ['admin', 'staff', 'support']);
        })->get();

        if ($staffUsers->isEmpty()) {
            // If no staff users exist, create a default staff user
            $staffUsers = collect([
                (object) ['id' => 999, 'name' => 'Staff Support'],
            ]);
        }

        // Categories and priorities
        $categories = ['technical', 'billing', 'account', 'product', 'other'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $statuses = ['open', 'in_progress', 'resolved', 'closed'];

        // Subjects and messages
        $subjects = [
            'Kesulitan saat login ke akun',
            'Pembayaran tidak terproses',
            'Produk tidak sesuai deskripsi',
            'Masalah teknis di website',
            'Pertanyaan tentang kebijakan retur',
            'Kesalahan harga produk',
            'Kesulitan saat checkout',
            'Akun saya diblokir',
            'Pengiriman terlambat',
            'Tidak bisa mengunggah foto profil'
        ];

        $messages = [
            'Saya mengalami kesulitan saat mencoba login ke akun saya. Setiap kali saya memasukkan password, sistem mengata니다 password salah padahal saya yakin benar.',
            'Pembayaran saya tidak terproses dengan status pending lebih dari 24 jam. Mohon bantuannya untuk mempercepat proses verifikasi.',
            'Produk yang saya terima tidak sesuai dengan deskripsi di website. Warna dan ukuran berbeda dari yang saya pesan.',
            'Website sering loading lama dan kadang tidak merespon. Ini mengganggu pengalaman belanja saya.',
            'Saya ingin bertanya tentang kebijakan retur barang jika produk yang diterima cacat atau rusak.',
            'Saya menemukan kesalahan harga di beberapa produk. Harga tertulis lebih murah dari seharusnya, apakah ini promo atau kesalahan sistem?',
            'Saya mengalami kesulitan saat proses checkout. Tombol "proses pembayaran" tidak merespon saat saya klik.',
            'Akun saya tiba-tiba diblokir tanpa pemberitahuan sebelumnya. Mohon penjelasan dan bantuan untuk membukanya kembali.',
            'Pengiriman saya terlambat beberapa hari dari estimasi yang diberikan. Mohon informasi terkini mengenai status pengiriman.',
            'Saya tidak bisa mengunggah foto profil di halaman pengaturan akun. Setiap kali mencoba, muncul pesan error.'
        ];

        // Determine filters
        $filteredCategory = $request->get('category');
        $filteredStatus = $request->get('status');
        $filteredPriority = $request->get('priority');
        $search = $request->get('search');

        for ($i = 0; $i < 15; $i++) {
            $ticket = new \stdClass();
            $ticket->id = $i + 1;
            $ticket->subject = $subjects[$i % count($subjects)];
            $ticket->message = $messages[$i % count($messages)];

            // Apply filters to ticket properties
            $ticket->category = $filteredCategory ?? $categories[$i % count($categories)];
            $ticket->priority = $filteredPriority ?? $priorities[array_rand($priorities)];
            $ticket->status = $filteredStatus ?? $statuses[array_rand($statuses)];

            // Select a user from the database to be the ticket creator
            $ticket->user = $users->get($i % $users->count());
            $ticket->user_id = $ticket->user->id;

            // Select staff for assignee if ticket is not open
            $ticket->assignee = $ticket->status === 'open' ? null : $staffUsers->first();
            $ticket->assignee_id = $ticket->assignee ? $ticket->assignee->id : null;

            $ticket->created_at = now()->subDays(rand(0, 30))->subHours(rand(0, 24))->subMinutes(rand(0, 60));
            $ticket->resolved_at = $ticket->status === 'resolved' ? $ticket->created_at->addHours(rand(1, 48)) : null;
            $ticket->resolution_notes = $ticket->status === 'resolved' ? 'Masalah telah diselesaikan sesuai dengan permintaan pelanggan.' : null;

            // Apply search filter
            $includeTicket = true;
            if ($search) {
                $searchLower = strtolower($search);
                if (!str_contains(strtolower($ticket->subject), $searchLower) &&
                    !str_contains(strtolower($ticket->message), $searchLower) &&
                    !str_contains(strtolower($ticket->user->name ?? ''), $searchLower)) {
                    $includeTicket = false;
                }
            }

            // Apply category filter if specified
            if ($filteredCategory && $ticket->category !== $filteredCategory) {
                $includeTicket = false;
            }

            // Apply status filter if specified
            if ($filteredStatus && $ticket->status !== $filteredStatus) {
                $includeTicket = false;
            }

            // Apply priority filter if specified
            if ($filteredPriority && $ticket->priority !== $filteredPriority) {
                $includeTicket = false;
            }

            if ($includeTicket) {
                $dummyTickets->push($ticket);
            }
        }

        // Create a paginator for dummy data
        $currentPage = $request->get('page', 1);
        $perPage = 10;
        $offset = ($currentPage - 1) * $perPage;
        $items = $dummyTickets->slice($offset, $perPage)->values();

        $tickets = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $dummyTickets->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );

        return view('admin.support_tickets', compact('tickets'));
    }

    /**
     * Get filtered dummy tickets for API when there are no real tickets
     */
    private function getFilteredDummyTicketsForAPI($request)
    {
        $dummyTickets = collect();

        // Get real users from the database to use as ticket creators
        $users = \App\Models\User::limit(10)->get();
        if ($users->isEmpty()) {
            // If no users exist, create default users
            $users = collect([
                (object) ['id' => 1, 'name' => 'Ahmad Santoso'],
                (object) ['id' => 2, 'name' => 'Budi Prasetyo'],
                (object) ['id' => 3, 'name' => 'Siti Rahayu'],
                (object) ['id' => 4, 'name' => 'Joko Widodo'],
                (object) ['id' => 5, 'name' => 'Lina Marlina'],
                (object) ['id' => 6, 'name' => 'Rina Kusuma'],
                (object) ['id' => 7, 'name' => 'Agus Setiawan'],
                (object) ['id' => 8, 'name' => 'Dewi Anggraini'],
                (object) ['id' => 9, 'name' => 'Fajar Pamungkas'],
                (object) ['id' => 10, 'name' => 'Tina Nurhayati'],
            ]);
        }

        // Get staff users for assignees
        $staffUsers = \App\Models\User::whereHas('role', function($query) {
            $query->whereIn('name', ['admin', 'staff', 'support']);
        })->get();

        if ($staffUsers->isEmpty()) {
            // If no staff users exist, create a default staff user
            $staffUsers = collect([
                (object) ['id' => 999, 'name' => 'Staff Support'],
            ]);
        }

        // Categories and priorities
        $categories = ['technical', 'billing', 'account', 'product', 'other'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $statuses = ['open', 'in_progress', 'resolved', 'closed'];

        // Subjects and messages
        $subjects = [
            'Kesulitan saat login ke akun',
            'Pembayaran tidak terproses',
            'Produk tidak sesuai deskripsi',
            'Masalah teknis di website',
            'Pertanyaan tentang kebijakan retur',
            'Kesalahan harga produk',
            'Kesulitan saat checkout',
            'Akun saya diblokir',
            'Pengiriman terlambat',
            'Tidak bisa mengunggah foto profil'
        ];

        $messages = [
            'Saya mengalami kesulitan saat mencoba login ke akun saya. Setiap kali saya memasukkan password, sistem mengata니다 password salah padahal saya yakin benar.',
            'Pembayaran saya tidak terproses dengan status pending lebih dari 24 jam. Mohon bantuannya untuk mempercepat proses verifikasi.',
            'Produk yang saya terima tidak sesuai dengan deskripsi di website. Warna dan ukuran berbeda dari yang saya pesan.',
            'Website sering loading lama dan kadang tidak merespon. Ini mengganggu pengalaman belanja saya.',
            'Saya ingin bertanya tentang kebijakan retur barang jika produk yang diterima cacat atau rusak.',
            'Saya menemukan kesalahan harga di beberapa produk. Harga tertulis lebih murah dari seharusnya, apakah ini promo atau kesalahan sistem?',
            'Saya mengalami kesulitan saat proses checkout. Tombol "proses pembayaran" tidak merespon saat saya klik.',
            'Akun saya tiba-tiba diblokir tanpa pemberitahuan sebelumnya. Mohon penjelasan dan bantuan untuk membukanya kembali.',
            'Pengiriman saya terlambat beberapa hari dari estimasi yang diberikan. Mohon informasi terkini mengenai status pengiriman.',
            'Saya tidak bisa mengunggah foto profil di halaman pengaturan akun. Setiap kali mencoba, muncul pesan error.'
        ];

        // Determine filters
        $filteredCategory = $request->get('category');
        $filteredStatus = $request->get('status');
        $filteredPriority = $request->get('priority');
        $search = $request->get('search');

        for ($i = 0; $i < 15; $i++) {
            $ticketId = $i + 1;
            $subject = $subjects[$i % count($subjects)];
            $message = $messages[$i % count($messages)];

            // Apply filters to ticket properties
            $category = $filteredCategory ?? $categories[$i % count($categories)];
            $priority = $filteredPriority ?? $priorities[array_rand($priorities)];
            $status = $filteredStatus ?? $statuses[array_rand($statuses)];

            // Select a user from the database to be the ticket creator
            $user = $users->get($i % $users->count());
            $user_name = $user->name;

            // Select staff for assignee if ticket is not open
            $assignee_name = $status === 'open' ? 'Unassigned' : ($staffUsers->first() ? $staffUsers->first()->name : 'Staff Support');

            $created_at = now()->subDays(rand(0, 30))->subHours(rand(0, 24))->subMinutes(rand(0, 60));
            $resolved_at = $status === 'resolved' ? $created_at->addHours(rand(1, 48))->format('d M Y H:i') : null;

            $ticketData = [
                'id' => $ticketId,
                'subject' => $subject,
                'message' => \Str::limit($message, 100),
                'category' => $category,
                'priority' => $priority,
                'status' => $status,
                'user_name' => $user_name,
                'assignee_name' => $assignee_name,
                'created_at' => $created_at->format('d M Y H:i'),
                'resolved_at' => $resolved_at,
            ];

            // Apply search filter
            $includeTicket = true;
            if ($search) {
                $searchLower = strtolower($search);
                if (!str_contains(strtolower($ticketData['subject']), $searchLower) &&
                    !str_contains(strtolower($ticketData['message']), $searchLower) &&
                    !str_contains(strtolower($ticketData['user_name']), $searchLower)) {
                    $includeTicket = false;
                }
            }

            // Apply category filter if specified
            if ($filteredCategory && $ticketData['category'] !== $filteredCategory) {
                $includeTicket = false;
            }

            // Apply status filter if specified
            if ($filteredStatus && $ticketData['status'] !== $filteredStatus) {
                $includeTicket = false;
            }

            // Apply priority filter if specified
            if ($filteredPriority && $ticketData['priority'] !== $filteredPriority) {
                $includeTicket = false;
            }

            if ($includeTicket) {
                $dummyTickets->push($ticketData);
            }
        }

        return $dummyTickets->toArray();
    }

    public function getUserProfile($userId)
    {
        \Log::info('getUserProfile called with userId: ' . $userId);

        // Find the user with related data
        $user = \App\Models\User::with('orders', 'tickets')->find($userId);

        if (!$user) {
            \Log::warning('User not found with ID: ' . $userId);
            return response()->json(['error' => 'User not found'], 404);
        }

        \Log::info('User found: ' . $user->name . ' (' . $user->id . ')');

        // Calculate additional information
        $totalOrders = $user->orders()->count();
        $totalTickets = $user->tickets()->count();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '-',
                'address' => $user->address ?? '-',
                'created_at' => $user->created_at->format('d M Y'),
                'last_login' => '-', // No last_login field in the users table
                'status' => $user->status ?? 'Aktif',
                'total_orders' => $totalOrders,
                'total_tickets' => $totalTickets,
            ]
        ]);
    }

    public function getTicketStatus($id)
    {
        // Check if there are real tickets in the database
        $hasRealTickets = Ticket::exists();

        if ($hasRealTickets) {
            $ticket = Ticket::find($id);
        } else {
            // No real tickets exist, try to create a dummy ticket if the ID is within range
            $users = \App\Models\User::limit(10)->get();
            if ($users->isEmpty()) {
                $users = collect([
                    (object) ['id' => 1, 'name' => 'Ahmad Santoso', 'email' => 'ahmad@example.com'],
                    (object) ['id' => 2, 'name' => 'Budi Prasetyo', 'email' => 'budi@example.com'],
                    (object) ['id' => 3, 'name' => 'Siti Rahayu', 'email' => 'siti@example.com'],
                    (object) ['id' => 4, 'name' => 'Joko Widodo', 'email' => 'joko@example.com'],
                    (object) ['id' => 5, 'name' => 'Lina Marlina', 'email' => 'lina@example.com'],
                    (object) ['id' => 6, 'name' => 'Rina Kusuma', 'email' => 'rina@example.com'],
                    (object) ['id' => 7, 'name' => 'Agus Setiawan', 'email' => 'agus@example.com'],
                    (object) ['id' => 8, 'name' => 'Dewi Anggraini', 'email' => 'dewi@example.com'],
                    (object) ['id' => 9, 'name' => 'Fajar Pamungkas', 'email' => 'fajar@example.com'],
                    (object) ['id' => 10, 'name' => 'Tina Nurhayati', 'email' => 'tina@example.com'],
                ]);
            }

            // Check if this ID would exist in dummy data (1-15)
            if ($id >= 1 && $id <= 15) {
                $categories = ['technical', 'billing', 'account', 'product', 'other'];
                $priorities = ['low', 'medium', 'high', 'urgent'];
                $statuses = ['open', 'in_progress', 'resolved', 'closed'];

                $subjects = [
                    'Kesulitan saat login ke akun',
                    'Pembayaran tidak terproses',
                    'Produk tidak sesuai deskripsi',
                    'Masalah teknis di website',
                    'Pertanyaan tentang kebijakan retur',
                    'Kesalahan harga produk',
                    'Kesulitan saat checkout',
                    'Akun saya diblokir',
                    'Pengiriman terlambat',
                    'Tidak bisa mengunggah foto profil'
                ];

                $messages_arr = [
                    'Saya mengalami kesulitan saat mencoba login ke akun saya. Setiap kali saya memasukkan password, sistem mengatanda password salah padahal saya yakin benar.',
                    'Pembayaran saya tidak terproses dengan status pending lebih dari 24 jam. Mohon bantuannya untuk mempercepat proses verifikasi.',
                    'Produk yang saya terima tidak sesuai dengan deskripsi di website. Warna dan ukuran berbeda dari yang saya pesan.',
                    'Website sering loading lama dan kadang tidak merespon. Ini mengganggu pengalaman belanja saya.',
                    'Saya ingin bertanya tentang kebijakan retur barang jika produk yang diterima cacat atau rusak.',
                    'Saya menemukan kesalahan harga di beberapa produk. Harga tertulis lebih murah dari seharusnya, apakah ini promo atau kesalahan sistem?',
                    'Saya mengalami kesulitan saat proses checkout. Tombol "proses pembayaran" tidak merespon saat saya klik.',
                    'Akun saya tiba-tiba diblokir tanpa pemberitahuan sebelumnya. Mohon penjelasan dan bantuan untuk membukanya kembali.',
                    'Pengiriman saya terlambat beberapa hari dari estimasi yang diberikan. Mohon informasi terkini mengenai status pengiriman.',
                    'Saya tidak bisa mengunggah foto profil di halaman pengaturan akun. Setiap kali mencoba, muncul pesan error.'
                ];

                // Create dummy ticket object with required data
                $ticket = new \stdClass();
                $ticket->id = $id;
                $ticket->subject = $subjects[($id - 1) % count($subjects)];
                $ticket->message = $messages_arr[($id - 1) % count($messages_arr)];
                $ticket->category = $categories[($id - 1) % count($categories)];
                $ticket->priority = $priorities[array_rand($priorities)];
                $ticket->status = $statuses[array_rand($statuses)];
                $ticket->user_id = $users->get(($id - 1) % $users->count())->id;
                $ticket->user = $users->get(($id - 1) % $users->count());
                $ticket->assignee = null;
                $ticket->assignee_id = null;
                $ticket->created_at = now()->subDays(rand(0, 30))->subHours(rand(0, 24))->subMinutes(rand(0, 60));
                $ticket->updated_at = now();
                $ticket->resolved_at = $ticket->status === 'resolved' ? $ticket->created_at->addHours(rand(1, 48)) : null;
                $ticket->resolution_notes = $ticket->status === 'resolved' ? 'Masalah telah diselesaikan sesuai dengan permintaan pelanggan.' : null;
            } else {
                // If ID is not in dummy range, return 404
                return response()->json(['error' => 'Ticket not found'], 404);
            }
        }

        if (!$ticket) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }

        return response()->json([
            'success' => true,
            'status' => $ticket->status,
            'priority' => $ticket->priority,
            'updated_at' => $ticket->updated_at->toISOString()
        ]);
    }
}