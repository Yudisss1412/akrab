<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('user', 'assignee')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.support_tickets', compact('tickets'));
    }

    public function show($id)
    {
        $ticket = Ticket::with('user', 'assignee')->findOrFail($id);
        
        return view('admin.ticket_detail', compact('ticket'));
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

        return redirect()->route('support.tickets.detail', $ticket->id)
            ->with('success', 'Tiket bantuan berhasil dibuat!');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'assignee_id' => 'nullable|exists:users,id',
            'resolution_notes' => 'nullable|string',
        ]);

        $ticket = Ticket::findOrFail($id);
        
        // Update status and additional fields
        $ticket->update([
            'status' => $request->status,
            'assignee_id' => $request->assignee_id,
            'resolution_notes' => $request->resolution_notes,
            'resolved_at' => $request->status === 'resolved' ? now() : null,
        ]);

        return response()->json(['success' => true, 'message' => 'Status tiket berhasil diperbarui']);
    }

    public function getTicketsByUser()
    {
        $tickets = Ticket::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.tickets.index', compact('tickets'));
    }

    public function apiGetTickets()
    {
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
        // Untuk sekarang, karena belum ada model pesan tiket, kita hanya kembalikan tiket asli
        // di masa depan bisa ditambah dengan model pesan terpisah untuk komunikasi tiket
        $ticket = Ticket::with('user')->findOrFail($id);
        
        // Format data sebagai message untuk konsistensi
        $messages = [
            [
                'id' => $ticket->id,
                'message' => $ticket->message,
                'sender_id' => $ticket->user_id,
                'sender_name' => $ticket->user->name ?? 'N/A',
                'created_at' => $ticket->created_at->format('d M Y, H:i'),
                'is_ticket_message' => true
            ]
        ];
        
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
        
        return response()->json(['messages' => $messages]);
    }
}
