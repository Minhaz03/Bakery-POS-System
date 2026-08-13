<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice - {{ $sale->invoice_no }}</title>
    <!-- Google Fonts -->
    
    
    
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background-color: #f1f5f9;
            color: #0f172a;
            line-height: 1.5;
            padding: 40px 20px;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 40px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 24px;
            margin-bottom: 32px;
        }

        .company-info h1 {
            font-size: 24px;
            font-weight: 800;
            color: #6366f1;
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        }

        .company-info p {
            color: #64748b;
            font-size: 13px;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h2 {
            font-size: 28px;
            font-weight: 800;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .invoice-title p {
            font-size: 14px;
            color: #64748b;
            margin-top: 4px;
        }

        .invoice-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 32px;
        }

        .meta-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
        }

        .meta-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 8px;
        }

        .meta-content {
            font-size: 14px;
            font-weight: 500;
        }

        .meta-content strong {
            display: block;
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 32px;
        }

        th {
            background: #f8fafc;
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 14px 16px;
            font-size: 14px;
            border-bottom: 1px solid #f1f5f9;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .item-name {
            font-weight: 600;
            color: #1e293b;
        }

        .totals {
            width: 320px;
            margin-left: auto;
            border-top: 2px solid #e2e8f0;
            padding-top: 16px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
            color: #475569;
        }

        .total-row.grand-total {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px dashed #cbd5e1;
        }

        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 13px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 24px;
        }

        .print-btn {
            display: block;
            width: 200px;
            margin: 0 auto 30px auto;
            padding: 12px 20px;
            background: #6366f1;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
            transition: transform 0.15s, background 0.15s;
        }

        .print-btn:hover {
            background: #4f46e5;
            transform: translateY(-1px);
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .invoice-container {
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
            .print-btn {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="print-btn">
        🖨️ Print Invoice
    </button>

    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>POS System</h1>
                <p>Bakery Edition</p>
                <p>123 Bakery Street, Food City, 12345</p>
                <p>Phone: +1 234 567 890</p>
            </div>
            <div class="invoice-title">
                <h2>INVOICE</h2>
                <p># {{ $sale->invoice_no }}</p>
            </div>
        </div>

        <!-- Meta Information -->
        <div class="invoice-meta">
            <div class="meta-box">
                <div class="meta-title">Billed To</div>
                <div class="meta-content">
                    @if($sale->customer)
                        <strong>{{ $sale->customer->name }}</strong>
                        @if($sale->customer->phone)
                            <div>{{ $sale->customer->phone }}</div>
                        @endif
                        @if($sale->customer->address)
                            <div>{{ $sale->customer->address }}</div>
                        @endif
                    @else
                        <strong>Walk-in Customer</strong>
                    @endif
                </div>
            </div>
            
            <div class="meta-box">
                <div class="meta-title">Invoice Details</div>
                <div class="meta-content" style="display:grid;grid-template-columns:auto 1fr;gap:4px 12px;">
                    <span style="color:#64748b">Date:</span>
                    <span>{{ $sale->sale_date->format('F d, Y') }}</span>
                    
                    <span style="color:#64748b">Status:</span>
                    <span style="text-transform:capitalize;font-weight:700;color:{{ $sale->status === 'completed' ? '#16a34a' : '#dc2626' }}">
                        {{ $sale->status }}
                    </span>
                    
                    <span style="color:#64748b">Payment:</span>
                    <span style="text-transform:capitalize;">{{ str_replace('_', ' ', $sale->payment_method) }}</span>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sale->items as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item->product?->name ?? 'Deleted Product' }}</div>
                        <div style="font-size:12px;color:#64748b;margin-top:2px;">SKU: {{ $item->product?->sku ?? 'N/A' }}</div>
                    </td>
                    <td class="text-center">{{ floatval($item->quantity) }}</td>
                    <td class="text-right">৳ {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right" style="font-weight:600;color:#0f172a;">৳ {{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center" style="color:#94a3b8;padding:24px;">No items found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <div class="total-row">
                <span>Subtotal</span>
                <span>৳ {{ number_format($sale->subtotal, 2) }}</span>
            </div>
            @if($sale->discount_amount > 0)
            <div class="total-row">
                <span>Discount</span>
                <span style="color:#ef4444;">- ৳ {{ number_format($sale->discount_amount, 2) }}</span>
            </div>
            @endif
            @if($sale->tax_amount > 0)
            <div class="total-row">
                <span>Tax / VAT (5%)</span>
                <span>৳ {{ number_format($sale->tax_amount, 2) }}</span>
            </div>
            @endif
            <div class="total-row grand-total">
                <span>Total Due</span>
                <span style="color:#6366f1;">৳ {{ number_format($sale->grand_total, 2) }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
            <p style="margin-top:4px;">If you have any questions about this invoice, please contact us.</p>
        </div>
    </div>

    <!-- Auto trigger print on load if requested, optional -->
    <script>
        window.onload = function() {
            // Uncomment to auto-print
            // window.print();
        }
    </script>
</body>
</html>
