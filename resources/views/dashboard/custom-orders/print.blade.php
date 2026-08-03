<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Custom Order - {{ $order->order_number }}</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Courier Prime', monospace;
        }
        
        body {
            background-color: #f1f5f9;
            color: #000;
            line-height: 1.4;
            padding: 20px;
            font-size: 13px;
        }

        .receipt-container {
            max-width: 300px;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px 15px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: 700; }
        
        .border-dashed {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        
        .header h1 {
            font-size: 18px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .header p {
            font-size: 12px;
        }

        .meta-data {
            margin-top: 15px;
            font-size: 12px;
        }
        
        .meta-data div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .specs {
            margin: 15px 0;
            font-size: 12px;
        }
        .specs-title {
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .specs-content {
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .totals {
            margin-top: 15px;
        }
        .totals div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }
        .totals .grand-total {
            font-size: 15px;
            font-weight: 700;
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px dashed #000;
        }

        .footer {
            margin-top: 20px;
            font-size: 11px;
            text-align: center;
        }

        .print-btn {
            display: block;
            width: 300px;
            margin: 0 auto 20px auto;
            padding: 12px 20px;
            background: #000;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            font-family: sans-serif;
        }
        .print-btn:hover { background: #333; }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .receipt-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
                width: 100%;
                max-width: 100%;
            }
            .print-btn { display: none !important; }
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="print-btn">
        🖨️ Print Receipt
    </button>

    <div class="receipt-container">
        <!-- Header -->
        <div class="header text-center">
            <h1>POS Bakery</h1>
            <p>123 Bakery Street, Food City</p>
            <p>Tel: +1 234 567 890</p>
        </div>

        <div class="border-dashed"></div>

        <div class="text-center text-bold" style="font-size:14px; margin: 10px 0;">
            CUSTOM ORDER
        </div>

        <div class="meta-data">
            <div><span>Order #:</span> <span class="text-bold">{{ $order->order_number }}</span></div>
            <div><span>Date:</span> <span>{{ $order->created_at->format('d/m/Y h:i A') }}</span></div>
            <div><span>Customer:</span> <span class="text-bold">{{ $order->customer_name }}</span></div>
            @if($order->customer && $order->customer->phone)
            <div><span>Phone:</span> <span>{{ $order->customer->phone }}</span></div>
            @endif
            <div><span>Delivery:</span> <span class="text-bold" style="font-size:13px;">{{ $order->delivery_date->format('d/m/Y') }}</span></div>
        </div>

        <div class="border-dashed"></div>

        <div class="specs">
            <div class="specs-title">Order Specifications</div>
            <div class="specs-content">{{ $order->details }}</div>
        </div>

        <div class="border-dashed"></div>

        <div class="totals">
            <div>
                <span>Total Cost:</span>
                <span>৳ {{ number_format($order->total_price, 2) }}</span>
            </div>
            <div>
                <span>Advance Paid:</span>
                <span>৳ {{ number_format($order->advance_payment, 2) }}</span>
            </div>
            <div class="grand-total">
                <span>Balance Due:</span>
                @php $balance = $order->total_price - $order->advance_payment; @endphp
                <span>৳ {{ number_format(max(0, $balance), 2) }}</span>
            </div>
        </div>

        <div class="border-dashed" style="margin-top:15px;"></div>

        <div class="footer">
            <p>Please present this slip when collecting your order.</p>
            <p style="margin-top:5px;font-weight:bold;">Thank You!</p>
        </div>
    </div>

</body>
</html>
