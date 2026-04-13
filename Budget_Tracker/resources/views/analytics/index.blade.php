@extends('layouts.app')

@section('content')
@push('styles')
   @vite(['resources/css/app.css', 'resources/css/dashboard.css', 'resources/js/analytics.js'])
@endpush
<main class="main-content">
    <div class="topbar">
        <div>
            <h1 class="topbar-title">Financial Analytics</h1>
            <p class="topbar-subtitle">Deep dive into your spending habits</p>
        </div>
        <form action="{{ route('analytics') }}" method="GET">
            <input type="month" name="month" class="month-selector" 
                   value="{{ $selectedMonth->format('Y-m') }}" onchange="this.form.submit()">
        </form>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Monthly Burn Rate</div>
            <div class="stat-value">{{ number_format($kpis['spentPercentage'], 1) }}%</div>
            <div class="stat-budget-bar-wrap">
                <div class="stat-budget-track">
                    <div class="stat-budget-fill" style="width: {{ $kpis['spentPercentage'] }}%; background: #FBCF97;"></div>
                </div>
            </div>
        </div>
        <div class="stat-card accent-green">
            <div class="stat-label" style="color: rgba(255,255,255,0.7)">Total Spent Ever</div>
            <div class="stat-value">{{ number_format($kpis['totalSpentAllTime'], 2) }}DH</div>
            <div class="stat-bg-decoration"></div>
        </div>
        </div>

    <div class="mid-grid">
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">9-Month Spending Trend</h3>
            </div>
            <canvas id="analyticsTrendChart"></canvas>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Category Distribution</h3>
            </div>
            <canvas id="categoryDonutChart"></canvas>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    window.chartData = @json($chartData);
    window.categoryData = @json($categorySpending);
</script>

@endsection