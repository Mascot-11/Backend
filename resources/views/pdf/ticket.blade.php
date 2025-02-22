<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }

        .ticket {
            border: 2px dashed #000;
            padding: 20px;
            width: 50%;
            margin: auto;
        }
    </style>
</head>

<body>
    <div class="ticket">
        <h2>Ticket Confirmation</h2>
        <!-- {{-- <p><strong>Ticket ID:</strong> {{ $ticket->id }}</p>
        <p><strong>User Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Event:</strong> {{ $ticket->event->name ?? 'N/A' }}</p>
        <p><strong>Seat Number:</strong> {{ $ticket->seat_number }}</p>
        <p><strong>Price:</strong> ${{ number_format($ticket->amount / 100, 2) }}</p> --}} -->
    </div>
</body>

</html>
