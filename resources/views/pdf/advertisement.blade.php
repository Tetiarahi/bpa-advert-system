<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advertisement Record - {{ $advertisement->title }}</title>
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

        .ad-content {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
            line-height: 1.8;
            font-size: 14px;
            color: #1f2937;
        }

        .ad-content p {
            margin: 10px 0;
        }

        .ad-content ul, .ad-content ol {
            margin: 10px 0;
            padding-left: 20px;
        }

        .ad-content strong {
            color: #111827;
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

        .category-badge {
            background: #f3f4f6;
            color: #374151;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
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
            {{-- <img src="{{ asset('images/logo.png') }}" alt="BPA Logo"> --}}
            {{-- <div class="logo-fallback">
                <div class="bpa-text">BPA</div>
                <div class="org-text">BROADCASTING &<br>PUBLICATIONS<br>AUTHORITY</div>
            </div> --}}
        </div>
        <div class="organization-name">Broadcasting & Publications Authority</div>
        <div class="document-title">Advertisement Record</div>
    </div>

    <div class="print-info">
        <strong>Print Date:</strong> {{ now()->format('F d, Y \a\t g:i A') }}<br>
        <strong>Record ID:</strong> #{{ str_pad($advertisement->id, 6, '0', STR_PAD_LEFT) }}<br>
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
                    <div class="info-value">{{ $advertisement->customer->fullname }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $advertisement->customer->email }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Phone:</div>
                    <div class="info-value">{{ $advertisement->customer->phone }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Address:</div>
                    <div class="info-value">{{ $advertisement->customer->address }}</div>
                </div>
            </div>
        </div>

        <!-- Advertisement Information -->
        <div class="section">
            <div class="section-title">Advertisement Details</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Title:</div>
                    <div class="info-value"><strong>{{ $advertisement->title }}</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Category:</div>
                    <div class="info-value">
                        <span class="category-badge">{{ $advertisement->adsCategory->name }}</span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Broadcast Period:</div>
                    <div class="info-value">
                        {{ $advertisement->broadcast_start_date ? $advertisement->broadcast_start_date->format('F d, Y') : 'Not specified' }}
                        @if($advertisement->broadcast_end_date)
                            - {{ $advertisement->broadcast_end_date->format('F d, Y') }}
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Morning Broadcasts (6:00 AM - 8:00 AM):</div>
                    <div class="info-value">{{ $advertisement->morning_frequency ?? 0 }} times</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Lunch Broadcasts (12:00 PM - 2:00 PM):</div>
                    <div class="info-value">{{ $advertisement->lunch_frequency ?? 0 }} times</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Evening Broadcasts (5:00 PM - 9:30 PM):</div>
                    <div class="info-value">{{ $advertisement->evening_frequency ?? 0 }} times</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Total Daily Broadcasts:</div>
                    <div class="info-value">
                        {{ ($advertisement->morning_frequency ?? 0) + ($advertisement->lunch_frequency ?? 0) + ($advertisement->evening_frequency ?? 0) }} times per day
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Broadcast Days:</div>
                    <div class="info-value">
                        @if(is_array($advertisement->broadcast_days))
                            {{ implode(', ', $advertisement->broadcast_days) }}
                        @else
                            {{ $advertisement->broadcast_days ?? 'Not specified' }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Advertisement Content -->
        @if($advertisement->content)
        <div class="section">
            <div class="section-title">Advertisement Content</div>
            <div class="ad-content">
                {!! $advertisement->clean_content !!}
            </div>
        </div>
        @endif

        <!-- Broadcasting Information -->
        <div class="section">
            <div class="section-title">Broadcasting Details</div>
            <div class="broadcast-info">
                <div class="broadcast-title">Broadcasting Band</div>
                <div style="font-size: 14px; font-weight: bold;">
                    @if(is_array($advertisement->band))
                        {{ implode(', ', $advertisement->band) }}
                    @else
                        {{ $advertisement->band }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="section">
            <div class="section-title">Financial Information</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Amount:</div>
                    <div class="info-value">
                        <span class="amount">${{ number_format($advertisement->amount, 2) }}</span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Record Created:</div>
                    <div class="info-value">{{ $advertisement->created_at->format('F d, Y \a\t g:i A') }}</div>
                </div>
                @if($advertisement->updated_at != $advertisement->created_at)
                <div class="info-row">
                    <div class="info-label">Last Updated:</div>
                    <div class="info-value">{{ $advertisement->updated_at->format('F d, Y \a\t g:i A') }}</div>
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
        <div>Broadcasting & Publications Authority - Advertisement Services</div>
        <div>This document was generated electronically and is valid without signature</div>
    </div>
</body>
</html>
