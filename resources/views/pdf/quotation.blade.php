<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Quotation {{ $quotation->quotation_number }}</title>
    <style>
        body, body * {
            font-family: sans-serif;
        }
        @page { margin: 100px 30px 250px 30px; }
        /* header { position: fixed; top: -100px; left: 0px; right: 0px; height: 50px; }
        footer { position: fixed; bottom: 10px; left: 0px; right: 0px; height: 50px; } */
    </style>
</head>
<body style="width: 100%;margin: 0 auto;">
    <header>
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td style="width: 60%;text-align: left;padding: 5px 0;">
                        @if($organisation->logo)
                            <img src="{{ public_path('storage/' . $organisation->logo) }}" style="width:200px;height:auto;">
                        @else
                            <div style="font-size:24px; font-weight:bold;">{{ $organisation->name }}</div>
                        @endif
                    </td>
                    <td style="width: 40%;text-align: right;padding: 5px 0;font-size:9px;line-height:1.6;color:#555;">
                        <div>#101, 1st Floor, 1st Cross, 1st Main Road, J.P. Nagar 2nd Phase, Mysuru - 570008</div>
                        <div>Tel: +918219209001</div>
                        <div>Email: dummy@example.com</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </header>

    <main>
        <table style="display: block;width: 100%; padding-bottom: 10px;">
            <tbody style="width: 100%;display: table;border-collapse: collapse;">
                <tr>
                    <td colspan="3" style="background-color: #d9d9d9;text-align: center;padding: 2px 8px;font-weight: 600;font-size: 16px;">QUOTATION</td>
                </tr>

                <tr><td colspan="3" style="height:10px;"></td></tr>

                <tr>
                    <td style="font-size: 10px;font-weight: 600;padding: 2px 0;width: 15%;text-align:right;">Client Name:</td>
                    <td style="font-size: 10px;padding: 2px 0 0 5px;width: 56%;">{{ $customer->name }}</td>
                    <td style="font-size: 10px;padding: 2px 0;width: 29%;"><b style="font-weight: 600;width: 70px;display: inline-block;">Reference No:</b> <span style="font-weight: 400;">{{ $quotation->quotation_number }}</span></td>
                </tr>
                <tr>
                    <td style="font-size: 10px;font-weight: 600;padding: 2px 0;width: 15%;text-align:right;">Client Address:</td>
                    <td style="font-size: 10px;padding: 2px 0 0 5px;width: 56%;">{{ $customer->address ?? '' }}</td>
                    <td style="font-size: 10px;padding: 2px 0;width: 29%;"><b style="font-weight: 600;width: 70px;display: inline-block;">Date:</b><span style="font-weight: 400;">{{ $quotation->created_at->format('d M Y') }}</span></td>
                </tr>
                <tr>
                    <td style="font-size: 10px;font-weight: 600;padding: 2px 0;width: 15%;text-align:right;">Contact Number:</td>
                    <td style="font-size: 10px;padding: 2px 0 0 5px;width: 56%;">{{ $customer->phone ?? '' }}</td>
                    <td style="font-size: 10px;padding: 2px 0;width: 29%;">
                        <b style="font-weight: 600;width: 70px;display: inline-block;">Validity:</b>
                        <span style="font-weight: 400;" id="page-no-field">30 Days</span>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 10px;font-weight: 600;padding: 2px 0;width: 15%;text-align:right;">Email Address:</td>
                    <td style="font-size: 10px;padding: 2px 0 0 5px;width: 56%;">{{ $customer->email ?? '' }}</td>
                    <td style="font-size: 10px;padding: 2px 0;width: 29%;"></td>
                </tr>
            </tbody>
        </table>

        <table style="display: block;width: 100%;padding-bottom: 10px;">
            <tbody style="width: 100%;display: table;border-collapse: collapse;">
                <tr>
                    <td style="font-size:10px;font-weight: 600;background-color: #d9d9d9;padding: 2px;width: 11%;border:1px solid #000;text-align: center;">No.</td>
                    <td style="font-size:10px;font-weight: 600;background-color: #d9d9d9;padding: 2px;border-top:1px solid #000;border-bottom:1px solid #000;width: 77%;">Description</td>
                    <td style="font-size:10px;font-weight: 600;background-color: #d9d9d9;padding: 2px;width: 12%;border-top:1px solid #000;border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;text-align: center;">AMOUNT (S$)</td>
                </tr>
                
                @forelse($quotation->items as $index => $item)
                <tr>
                    <td style="font-size:10px;padding: 2px 2px 2px 2px;width: 11%;border-left:1px solid #000;text-align: center;">{{ $index + 1 }}</td>
                    <td style="font-size:10px;padding: 2px 2px 2px 2px;width: 77%;border-left:1px solid #000;">{{ $item->description }}</td>
                    <td style="font-size:10px;padding: 2px 2px 2px 0px;width: 12%;border-left:1px solid #000;border-right:1px solid #000;text-align: right;">
                        {{ number_format($item->amount, 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td style="font-size:10px;padding: 2px 2px 2px 2px;width: 11%;border-left:1px solid #000;text-align: center;"></td>
                    <td style="font-size:10px;padding: 2px 2px 2px 2px;width: 77%;border-left:1px solid #000;text-align:center;color:#aaa;">No items found.</td>
                    <td style="font-size:10px;padding: 2px 2px 2px 0px;width: 12%;border-right:1px solid #000;text-align: center;"></td>
                </tr>
                @endforelse
                @php
                    $itemCount = $quotation->items->count();
                    $maxItems = 12; // Adjust this number based on how many rows fit on a page
                    $spacerRows = max(0, $maxItems - $itemCount);
                @endphp
                @for ($i = 0; $i < $spacerRows; $i++)
                    <tr>
                        <td style="font-size:10px;padding: 2px 2px 2px 2px;width: 11%;border-left:1px solid #000;">&nbsp;</td>
                        <td style="font-size:10px;padding: 2px 2px 2px 2px;width: 77%;border-left:1px solid #000;">&nbsp;</td>
                        <td style="font-size:10px;padding: 2px 2px 2px 0px;width: 12%;border-left:1px solid #000;border-right:1px solid #000;">&nbsp;</td>
                    </tr>
                @endfor

                <tr>
                    <td style="padding: 12px 2px 2px 0px;border-top:1px solid #000;border-left:1px solid #000;"></td>
                    <td style="padding: 12px 2px 2px 0px;border-top:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;"></td>
                    <td style="padding: 12px 2px 2px 0px;border-top:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;"></td>
                </tr>

                <tr>
                    <td style="font-size:10px;padding: 2px 2px 2px 0px;width: 11%;border-left:1px solid #000;text-align: center;"></td>
                    <td style="font-size:10px;padding: 2px;width: 77%;border-left:1px solid #000; text-align:right;"><b>Sub Total :</b></td>
                    <td style="font-size:10px;padding: 2px 2px 2px 0px;width: 12%;border-left:1px solid #000;border-right:1px solid #000;text-align: right;"><b>{{ number_format($quotation->sub_total, 2) }}</b></td>
                </tr>

                @if($quotation->discount_amount > 0)
                <tr>
                    <td style="font-size:10px;padding: 2px 2px 2px 0px;width: 11%;border-left:1px solid #000;text-align: center;"></td>
                    <td style="font-size:10px;padding: 2px;width: 77%;border-left:1px solid #000; text-align:right;">
                        <b>Discount :</b>
                    </td>
                    <td style="font-size:10px;padding: 2px 2px 2px 0px;width: 12%;border-left:1px solid #000;border-right:1px solid #000;text-align: right;"><b>-{{ number_format($quotation->discount_amount, 2) }}</b></td>
                </tr>
                @endif

                <tr>
                    <td style="font-size:10px;padding: 2px 2px 30px 2px;width: 11%;border-left:1px solid #000;border-bottom:1px solid #000;text-align: center;"></td>
                    <td style="font-size:10px;padding: 2px 2px 30px 2px;width: 77%;border-left:1px solid #000;border-bottom:1px solid #000; text-align:right;"><b>Total Amount :</b></td>
                    <td style="font-size:10px;padding: 2px 2px 30px 0px;width: 12%;border-left:1px solid #000;border-right:1px solid #000;border-bottom:1px solid #000;text-align: right;">
                        <b style="padding:1px;"> {{ number_format($quotation->total_amount, 2) }}</b>
                    </td>
                </tr>
            </tbody>
        </table>

        @if($quotation->terms || $organisation->terms_and_conditions)
        <table style="display: block;width: 100%;padding-bottom: 10px;">
            <tbody style="width: 100%;display: table;border-collapse: collapse;">
                <tr>
                    <td style="background-color: #d9d9d9;text-align: center;padding: 2px 8px;font-weight: 600;font-size: 16px;">Terms & Conditions</td>
                </tr>
                <tr>
                    <td>
                        <ul style="font-size: 10px;">
                            <li>This quotation is valid for 30 days from the quotation date unless otherwise stated.</li>
                            <li>Acceptance of this quotation constitutes agreement to all terms and conditions contained herein.</li>
                            <li>Any additional work requested outside the scope of this quotation may be subject to additional charges.</li>
                            <li>Prices quoted are based on the information available at the time of preparation and may be revised if project requirements change.</li>
                            <li>A deposit may be required before work commences. Remaining payments shall be made according to the agreed payment schedule.</li>
                            <li>Materials and products are subject to availability.</li>
                            <li>Project timelines may be affected by site conditions, weather, or circumstances beyond our control.</li>
                            <li>This quotation does not include work not specifically listed within the quotation items.</li>
                            <li>Any variations to the agreed specification may result in additional costs.</li>
                            <li>Ownership of supplied goods and materials remains with the company until full payment has been received.</li>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>
        @endif
    </main>
</body>
</html>