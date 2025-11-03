<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>New Contact Request - CleanShoes</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #0d0d0d, #2e2e2e);
            color: #ffffff;
            text-align: center;
            padding: 30px 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 0.5px;
        }

        .content {
            padding: 30px;
        }

        .content h2 {
            color: #111;
            font-size: 20px;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 8px;
        }

        .content p {
            margin: 10px 0;
            font-size: 16px;
        }

        .content strong {
            color: #000;
        }

        .footer {
            background-color: #fafafa;
            text-align: center;
            font-size: 14px;
            color: #777;
            padding: 20px;
            border-top: 1px solid #eee;
        }

        .footer a {
            color: #000;
            text-decoration: none;
            font-weight: bold;
        }

        @media (max-width: 600px) {
            .content {
                padding: 20px;
            }

            .header h1 {
                font-size: 22px;
            }

            .content p {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>

<div class="email-container">
    <div class="header">
        <h1>IN CleanShoes</h1>
        <p style="margin-top: 8px; font-size: 14px; color: #ccc;">Premium Shoe Cleaning Service</p>
    </div>

    <div class="content">
        <h2>🧼 New Contact Request</h2>

        <p><strong>Name:</strong> {{ $data['name'] }}</p>

        @if(!empty($data['email']))
            <p><strong>Email:</strong> {{ $data['email'] }}</p>
        @endif

        <p><strong>Phone:</strong> {{ $data['phone'] }}</p>

        @if(!empty($data['comment']))
            <p><strong>Comment:</strong><br>{{ $data['comment'] }}</p>
        @endif
    </div>
</div>

</body>
</html>
