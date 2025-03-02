<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Event Ticket</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background-color: #000000;
            padding: 20px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            font-size: 24px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .email-content {
            padding: 30px;
            color: #333333;
        }
        .ticket-info {
            margin-bottom: 20px;
            border: 1px solid #e0e0e0;
            padding: 15px;
            background-color: #f9f9f9;
        }
        .ticket-info p {
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
        }
        .ticket-info strong {
            font-weight: 600;
        }
        .email-footer {
            background-color: #f4f4f4;
            padding: 15px;
            text-align: center;
            font-size: 14px;
            color: #666666;
        }
        .event-image {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .qr-code {
            text-align: center;
            margin-top: 20px;
        }
        .qr-code img {
            width: 150px;
            height: 150px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Your Event Ticket</h1>
        </div>
        <div class="email-content">
            <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" alt="Event" class="event-image">

            <p>Dear {{ $userData->name }},</p>
            <p>Thank you for your purchase. Here are your ticket details:</p>

            <div class="ticket-info">
                <p><strong>Ticket No:</strong> {{ $payment->transaction_id }}</p>
                <p><strong>Name:</strong> {{ $userData->name }}</p>
                <p><strong>Email:</strong> {{ $userData->email }}</p>
                <p><strong>Event:</strong> {{ $payment->event->name ?? 'N/A' }}</p>
                <p><strong>Price:</strong> NPR {{ $payment->total_amount }}</p>
            </div>

            <p>Please present the QR code below at the event entrance:</p>

            <div class="qr-code">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $payment->transaction_id }}" alt="QR Code">
            </div>

            <p>We look forward to seeing you at the event!</p>
        </div>
        <div class="email-footer">
            &copy; 2023 Color Mode Nepal. All rights reserved.
        </div>
    </div>
</body>
</html>
