<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket for {{ $userData->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .ticket {
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }

        h2 {
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .ticket-info {
            text-align: left;
            margin-bottom: 30px;
        }

        .ticket-info p {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            font-size: 14px;
        }

        .ticket-info strong {
            font-weight: 600;
        }

        .barcode {
            border-top: 2px solid #000;
            padding-top: 20px;
        }

        .barcode img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <h2>Ticket Confirmation</h2>
        <div class="ticket-info">
            <p><strong>Ticket No:</strong> <span>{{ $payment->transaction_id }}</span></p>
            <p><strong>User Name:</strong> <span>{{ $userData->name }}</span></p>
            <p><strong>Email:</strong> <span>{{ $userData->email }}</span></p>
            <p><strong>Event:</strong> <span>{{ $payment->event->name ?? 'N/A' }}</span></p>
            <p><strong>Price:</strong> <span>${{ $payment->total_amount }}</span></p>
        </div>
        <div class="barcode">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $payment->transaction_id }}" alt="QR Code">
        </div>
    </div>
</body>
</html>
