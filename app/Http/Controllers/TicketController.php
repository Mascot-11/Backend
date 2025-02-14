<?php
namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketMail;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    public function purchase(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:esewa'
        ]);

        $event = Event::findOrFail($request->event_id);
        $totalAmount = $event->price * $request->quantity;
        $user = auth()->user();

        // Check availability
        if ($event->available_tickets < $request->quantity) {
            return response()->json(['message' => 'Not enough tickets available'], 400);
        }

        if ($request->payment_method == 'esewa') {
            return $this->initiateEsewaPayment($totalAmount, $event);
        }
    }

    private function initiateEsewaPayment($amount, $event)
    {
        // Generate unique transaction ID
        $transactionId = "event_ticket_" . time();

        // Prepare payment parameters
        $params = [
            'amt' => $amount,
            'txAmt' => 0,
            'psc' => 0,
            'pdc' => 0,
            'scd' => env('ESEWA_MERCHANT_CODE'),
            'pid' => $transactionId,
            'su' => env('ESEWA_SUCCESS_URL'),
            'fu' => env('ESEWA_FAILURE_URL')
        ];

        // Return redirect URL for frontend
        return response()->json([
            'message' => 'Redirect to eSewa for payment',
            'esewa_url' => 'https://uat.esewa.com.np/epay/main?' . http_build_query($params)
        ]);
    }

    public function verifyEsewaPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'refId' => 'required|string',
            'pid' => 'required|string',
            'event_id' => 'required|exists:events,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            // Send verification request to eSewa API
            $response = Http::asForm()->post('https://uat.esewa.com.np/epay/transrec', [
                'amt' => $request->amount,
                'scd' => env('ESEWA_MERCHANT_CODE'),
                'rid' => $request->refId,
                'pid' => $request->pid
            ]);

            if ($response->body() == "Success") {
                return $this->processTicket($request->event_id, auth()->user(), $request->quantity, "esewa");
            } else {
                Log::error("eSewa payment verification failed for transaction: {$request->pid}");
                return response()->json(['message' => 'eSewa payment verification failed'], 400);
            }
        } catch (\Exception $e) {
            Log::error("Error verifying eSewa payment: " . $e->getMessage());
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

    public function getTicketDetails($ticketId)
    {
        $ticket = Ticket::with('event')->findOrFail($ticketId);
        return response()->json(['ticket' => $ticket]);
    }

    private function processTicket($eventId, $user, $quantity, $paymentMethod)
    {
        // Create ticket record in the database
        $ticket = Ticket::create([
            'user_id' => $user->id,
            'event_id' => $eventId,
            'quantity' => $quantity,
            'status' => 'paid',
        ]);

        // Generate PDF for the ticket
        $pdf = Pdf::loadView('pdf.ticket', ['ticket' => $ticket]);
        $pdfPath = storage_path("app/public/tickets/ticket_{$ticket->id}.pdf");
        $pdf->save($pdfPath);

        // Send email with ticket attached
        Mail::to($user->email)->send(new TicketMail($ticket));

        return response()->json(['message' => 'Payment successful', 'ticket' => $ticket]);
    }
}
