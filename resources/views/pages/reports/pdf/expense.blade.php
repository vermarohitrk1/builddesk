<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Expense Report</title>
    <style>
        body { font-family: sans-serif; font-size: 13px; color: #333; }
        .header { text-align: center; margin-bottom: 25px; }
        .header h2 { margin: 0 0 5px 0; color: #b91c1c; }
        .header p { margin: 0; color: #666; font-size: 11px; }
        .summary { margin-bottom: 20px; border: 1px solid #ddd; padding: 10px; background: #f9fafb; display: table; width: 100%; box-sizing: border-box; }
        .summary-col { display: table-cell; width: 50%; }
        .summary-col strong { font-size: 15px; color: #b91c1c; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; color: #475569; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f8fafc; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Expense Report</h2>
        <p>Period: {{ $date_from ? \Carbon\Carbon::parse($date_from)->format('d M Y') : 'Start' }} to {{ $date_to ? \Carbon\Carbon::parse($date_to)->format('d M Y') : 'Today' }}</p>
        <p>Generated on: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <div class="summary">
        <div class="summary-col">
            <span>Transaction Count:</span> <br>
            <strong>{{ count($reports) }}</strong>
        </div>
        <div class="summary-col text-right">
            <span>Total Expenses:</span> <br>
            <strong>₹ {{ number_format($totalExpense, 2) }}</strong>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Expense Date</th>
                <th>Category</th>
                <th>Supplier</th>
                <th>Payment Method</th>
                <th>Description</th>
                <th>Created By</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $row)
                <tr>
                    <td>{{ $row['expense_date'] }}</td>
                    <td><strong>{{ $row['category'] }}</strong></td>
                    <td>{{ $row['supplier'] }}</td>
                    <td>{{ $row['payment_method'] }}</td>
                    <td>{{ $row['description'] }}</td>
                    <td>{{ $row['created_by'] }}</td>
                    <td class="text-right">₹ {{ number_format($row['amount'], 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6" class="text-right">Total Expenses:</td>
                <td class="text-right">₹ {{ number_format($totalExpense, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
