<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gong Memorial Record - {{ $gong->departed_name }}</title>
    <style>
        @page {
            margin: 20mm;
            size: A4;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo {
            width: 100px;
            height: 100px;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            border-radius: 50%;
        }

        .logo-fallback {
            width: 100px;
            height: 100px;
            border: 4px solid #1e3a8a;
            border-radius: 50%;
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }

        .logo-fallback .bpa-text {
            font-size: 18px;
            font-weight: bold;
            color: #dc2626;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .logo-fallback .org-text {
            font-size: 6px;
            font-weight: bold;
            color: #1e3a8a;
            text-align: center;
            line-height: 1.1;
        }

        .organization-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin: 10px 0;
        }

        .document-title {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin: 15px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .print-info {
            text-align: right;
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 20px;
        }

        .content {
            margin: 30px 0;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .info-row {
            display: table-row;
            border-bottom: 1px solid #f3f4f6;
        }

        .info-label {
            display: table-cell;
            font-weight: bold;
            color: #374151;
            padding: 8px 15px 8px 0;
            width: 30%;
            vertical-align: top;
        }

        .info-value {
            display: table-cell;
            padding: 8px 0;
            color: #1f2937;
            vertical-align: top;
        }

        .memorial-content {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
            font-style: italic;
            line-height: 1.8;
        }

        .payment-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .payment-paid {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .payment-unpaid {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .footer {
            position: fixed;
            bottom: 20mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }

        .signature-section {
            margin-top: 50px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 45%;
            text-align: center;
            border-top: 1px solid #374151;
            padding-top: 10px;
            margin: 0 20px;
        }

        .amount {
            font-size: 14px;
            font-weight: bold;
            color: #059669;
        }

        .broadcast-info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }

        .broadcast-title {
            font-weight: bold;
            color: #1d4ed8;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    @php
        $path = public_path('images/bpa-logo.png');
        $logo = base64_encode(file_get_contents($path));
    @endphp
    <div class="header">
        <div class="logo">
            <img src="data:image/png;base64,{{ $logo }}" alt="BPA Logo" style="width:100px; height:100px;">
            {{-- <img src="{{ asset('images/bpa-logo.png') }}" alt="BPA Logo"> --}}
            {{-- <div class="logo-fallback">
                <img src="{{ asset('images/bpa-logo.png') }}" alt="BPA Logo"> --}}
                {{-- <div class="bpa-text">BPA</div>
                <div class="org-text">BROADCASTING &<br>PUBLICATIONS<br>AUTHORITY</div> --}}
            {{-- </div> --}}
        </div>
        <div class="organization-name">Broadcasting & Publications Authority</div>
        <div class="document-title">Memorial Record</div>
    </div>

    <div class="print-info">
        <strong>Print Date:</strong> {{ now()->format('F d, Y \a\t g:i A') }}<br>
        <strong>Record ID:</strong> #{{ str_pad($gong->id, 6, '0', STR_PAD_LEFT) }}<br>
        @if(isset($printedBy))
            <strong>Printed By:</strong> {{ $printedBy }}
        @endif
    </div>

    <div class="content">
        <!-- Customer Information -->
        <div class="section">
            <div class="section-title">Customer Information</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Customer Name:</div>
                    <div class="info-value">{{ $gong->customer->fullname }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $gong->customer->email }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Phone:</div>
                    <div class="info-value">{{ $gong->customer->phone }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Address:</div>
                    <div class="info-value">{{ $gong->customer->address }}</div>
                </div>
            </div>
        </div>

        <!-- Memorial Information -->
        <div class="section">
            <div class="section-title">Memorial Information</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Departed Name:</div>
                    <div class="info-value"><strong>{{ $gong->departed_name }}</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Death Date:</div>
                    <div class="info-value">{{ $gong->death_date ? $gong->death_date->format('F d, Y') : 'Not specified' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Published Date:</div>
                    <div class="info-value">{{ $gong->published_date ? $gong->published_date->format('F d, Y') : 'Not specified' }}</div>
                </div>
                @if($gong->song_title)
                <div class="info-row">
                    <div class="info-label">Memorial Song:</div>
                    <div class="info-value"><em>"{{ $gong->song_title }}"</em></div>
                </div>
                @endif
            </div>
        </div>

        <!-- Broadcasting Information -->
        <div class="section">
            <div class="section-title">Broadcasting Details</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Broadcasting Band:</div>
                    <div class="info-value">
                        @if(is_array($gong->band))
                            {{ implode(', ', $gong->band) }}
                        @else
                            {{ $gong->band ?? 'Not specified' }}
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Broadcast Days:</div>
                    <div class="info-value">
                        @if(is_array($gong->broadcast_days))
                            {{ implode(', ', $gong->broadcast_days) }}
                        @else
                            {{ $gong->broadcast_days ?? 'Not specified' }}
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Broadcast Period:</div>
                    <div class="info-value">
                        {{ $gong->broadcast_start_date ? $gong->broadcast_start_date->format('M d, Y') : 'Not specified' }}
                        @if($gong->broadcast_end_date)
                            - {{ $gong->broadcast_end_date->format('M d, Y') }}
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Daily Frequencies:</div>
                    <div class="info-value">
                        Morning: {{ $gong->morning_frequency ?? 0 }},
                        Lunch: {{ $gong->lunch_frequency ?? 0 }},
                        Evening: {{ $gong->evening_frequency ?? 0 }}
                    </div>
                </div>
                @if($gong->broadcast_notes)
                <div class="info-row">
                    <div class="info-label">Broadcast Notes:</div>
                    <div class="info-value">{{ $gong->broadcast_notes }}</div>
                </div>
                @endif
            </div>

            @if($gong->contents)
            <div class="memorial-content" style="margin-top: 20px;">
                <strong>Memorial Message:</strong><br>
                {!! $gong->clean_contents !!}
            </div>
            @endif
        </div>

        <!-- Payment Information -->
        <div class="section">
            <div class="section-title">Payment Information</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Amount:</div>
                    <div class="info-value">
                        <span class="amount">${{ number_format($gong->amount, 2) }}</span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Payment Status:</div>
                    <div class="info-value">
                        <span class="payment-status {{ $gong->is_paid ? 'payment-paid' : 'payment-unpaid' }}">
                            {{ $gong->is_paid ? 'PAID' : 'UNPAID' }}
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Record Created:</div>
                    <div class="info-value">{{ $gong->created_at->format('F d, Y \a\t g:i A') }}</div>
                </div>
                @if($gong->updated_at != $gong->created_at)
                <div class="info-row">
                    <div class="info-label">Last Updated:</div>
                    <div class="info-value">{{ $gong->updated_at->format('F d, Y \a\t g:i A') }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div>Prepared By</div>
                @if(isset($printedBy))
                    <div style="margin-top: 20px; font-weight: bold; color: #2563eb;">{{ $printedBy }}</div>
                    <div style="margin-top: 10px; font-size: 10px;">BPA Staff</div>
                @else
                    <div style="margin-top: 30px; font-size: 10px;">BPA Staff Signature</div>
                @endif
            </div>
            <div style="display: table-cell; width: 10%;"></div>
            <div class="signature-box">
                <div>Customer Acknowledgment</div>
                <div style="margin-top: 30px; font-size: 10px;">Customer Signature</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div>Broadcasting & Publications Authority - Gong Memorial Services</div>
        <div>This document was generated electronically and is valid without signature</div>
    </div>
</body>
</html>
