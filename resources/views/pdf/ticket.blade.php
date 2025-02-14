<!DOCTYPE html>
<html>
<head>
    <title>Event Ticket</title>
</head>
<body>
    <h1>{{ $ticket->event->name }} Ticket</h1>
    <p>Quantity: {{ $ticket->quantity }}</p>
    <p>Status: {{ $ticket->status }}</p>
    <p>Purchase Date: {{ $ticket->created_at }}</p>
</body>
</html>
