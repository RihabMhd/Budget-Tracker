<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: sans-serif;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .stats-grid {
            width: 100%;
            margin-bottom: 20px;
        }

        .stat-card {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: #6366f1;
            color: white;
            padding: 10px;
            text-align: left;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .total {
            font-weight: bold;
            color: #ef4444;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Monthly Financial Report</h1>
        {{-- Change $user->name to $userName --}}
        <p>{{ $month }} - Prepared for {{ $userName ?? 'Guest User' }}</p>
    </div>

    <table class="stats-grid">
        <tr>
            <td class="stat-card">
                <strong>Total Spent</strong><br>
                {{ number_format($kpis['monthlyExpenses'], 2) }} DH
            </td>
            <td class="stat-card">
                <strong>Remaining</strong><br>
                {{ number_format($kpis['remaining'], 2) }} DH
            </td>
        </tr>
    </table>

    <h3>Top Categories</h3>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $categoryData = is_array($categories) ? $categories : $categories->toArray();
            @endphp

            @foreach ($categoryData as $cat)
                <tr>
                    <td>{{ $cat['name'] ?? 'Uncategorized' }}</td>
                    <td>
                        {{ number_format($cat['amount'] ?? 0, 2) }} DH
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
