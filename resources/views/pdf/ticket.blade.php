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
        <p><strong>Ticket No:</strong> {{ $payment->transaction_id }}</p>
        <p><strong>User Name:</strong> {{ $userData->name }}</p>
        <p><strong>Email:</strong> {{ $userData->email }}</p>
        <p><strong>Event:</strong> {{ $payment->event->name ?? 'N/A' }}</p>
        <p><strong>Price:</strong> ${{ $payment->total_amount }}</p>
    </div>
</body>

</html>