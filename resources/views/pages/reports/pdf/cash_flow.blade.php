<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Statement</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 25px; }
        .header h2 { margin: 0 0 5px 0; color: #1e3a8a; }
        .header p { margin: 0; color: #666; font-size: 11px; }
        .summary { margin-bottom: 20px; border: 1px solid #ddd; padding: 10px; background: #f9fafb; display: table; width: 100%; box-sizing: border-box; }
        .summary-col { display: table-cell; width: 33.3%; }
        .summary-col strong { font-size: 14px; }
        .text-success { color: #15803d; }
        .text-danger { color: #b91c1c; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; color: #475569; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f8fafc; }
        .badge { display: inline-block; padding: 2px 5px; font-size: 10px; font-weight: bold; border-radius: 3px; color: #fff; }
        .badge-success { background-color: #15803d; }
        .badge-danger { background-color: #b91c1c; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Financial Statement (Cash Flow)</h2>
        <p>Period: {{ $date_from ? \Carbon\Carbon::parse($date_from)->format('d M Y') : 'Start' }} to {{ $date_to ? \Carbon\Carbon::parse($date_to)->format('d M Y') : 'Today' }}</p>
        <p>Generated on: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <div class="summary">
        <div class="summary-col">
            <span>Total In (Revenue):</span> <br>
            <strong class="text-success">₹ {{ number_format($totalIn, 2) }}</strong>
        </div>
        <div class="summary-col" style="text-align: center;">
            <span>Total Out (Expenses):</span> <br>
            <strong class="text-danger">₹ {{ number_format($totalOut, 2) }}</strong>
        </div>
        <div class="summary-col text-right">
            <span>Net Cash Flow:</span> <br>
            <strong class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">₹ {{ number_format($netProfit, 2) }}</strong>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 80px;">Date</th>
                <th style="width: 60px;">Type</th>
                <th style="width: 100px;">Reference</th>
                <th>Description</th>
                <th class="text-right" style="width: 90px;">Money In (+)</th>
                <th class="text-right" style="width: 90px;">Money Out (-)</th>
                <th class="text-right" style="width: 90px;">Running Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $row)
                <tr>
                    <td>{{ $row['date_formatted'] }}</td>
                    <td>
                        <span class="badge {{ $row['type'] == 'Revenue' ? 'badge-success' : 'badge-danger' }}">
                            {{ $row['type'] }}
                        </span>
                    </td>
                    <td><strong>{{ $row['reference'] }}</strong></td>
                    <td style="color: #666; font-size: 11px;">{{ $row['description'] }}</td>
                    <td class="text-right text-success">
                        {{ $row['money_in'] > 0 ? '₹ ' . number_format($row['money_in'], 2) : '-' }}
                    </td>
                    <td class="text-right text-danger">
                        {{ $row['money_out'] > 0 ? '₹ ' . number_format($row['money_out'], 2) : '-' }}
                    </td>
                    <td class="text-right"><strong>₹ {{ number_format($row['balance'], 2) }}</strong></td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="text-right">Totals:</td>
                <td class="text-right text-success">₹ {{ number_format($totalIn, 2) }}</td>
                <td class="text-right text-danger">₹ {{ number_format($totalOut, 2) }}</td>
                <td class="text-right">₹ {{ number_format($netProfit, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
