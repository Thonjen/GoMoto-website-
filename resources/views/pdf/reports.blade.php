<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $adminName }} Statistics - {{ $periodLabel }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #ffffff;
            background-color: #1a1a1a;
            padding: 20px;
            font-size: 12px;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
            background-color: #2d2d2d;
            padding: 30px;
            border-radius: 12px;
            border: 2px solid #404040;
            box-shadow: 0 20px 60px rgba(0,0,0,0.8);
        }
        .header {
            background: linear-gradient(135deg, #1f1f1f 0%, #2a2a2a 100%);
            padding: 25px;
            margin: -30px -30px 30px -30px;
            border-radius: 12px 12px 0 0;
            border-bottom: 4px solid #10b981;
            position: relative;
        }
        .header::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #10b981 0%, #3b82f6 50%, #a855f7 100%);
        }
        .header h1 {
            font-size: 22px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 8px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.8);
            letter-spacing: 0.5px;
        }
        .header .period {
            font-size: 12px;
            color: #10b981;
            margin-bottom: 8px;
            font-weight: 700;
        }
        .header .meta {
            font-size: 9px;
            color: #999999;
            line-height: 1.6;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-spacing: 10px;
        }
        .stats-row {
            display: table-row;
        }
        .stat-card {
            display: table-cell;
            width: 25%;
            padding: 12px;
            background-color: #3a3a3a;
            border: 2px solid #505050;
            border-radius: 10px;
            vertical-align: top;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
            position: relative;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #10b981, #3b82f6, #a855f7, #ec4899);
            border-radius: 8px 8px 0 0;
        }
        .stat-card .icon {
            font-size: 8px;
            color: #999999;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        .stat-card .value {
            font-size: 18px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 6px;
            line-height: 1;
        }
        .stat-card .sub-value {
            font-size: 9px;
            color: #b3b3b3;
            font-weight: 600;
        }
        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-spacing: 10px;
        }
        .metrics-row {
            display: table-row;
        }
        .metric-box {
            display: table-cell;
            width: 33.33%;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.6);
            border: 2px solid;
        }
        .metric-box.green {
            background-color: #1e4d3b;
            color: #ffffff;
            border-color: #10b981;
        }
        .metric-box.blue {
            background-color: #1e3a5f;
            color: #ffffff;
            border-color: #3b82f6;
        }
        .metric-box.purple {
            background-color: #3d2561;
            color: #ffffff;
            border-color: #a855f7;
        }
        .metric-box .value {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 6px;
        }
        .metric-box .label {
            font-size: 8px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #e5e5e5;
        }
        .section {
            margin-bottom: 20px;
            background-color: #3a3a3a;
            border: 2px solid #505050;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.6);
            position: relative;
            overflow: hidden;
        }
        .section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #10b981, #3b82f6, #a855f7);
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 18px;
            margin-left: 10px;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding-bottom: 10px;
            border-bottom: 2px solid #505050;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        table thead {
            background: linear-gradient(135deg, #2a2a2a 0%, #1f1f1f 100%);
            color: white;
            border-bottom: 3px solid #10b981;
        }
        table th {
            padding: 10px 8px;
            text-align: left;
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
        }
        table td {
            padding: 10px 8px;
            border-bottom: 1px solid #404040;
            color: #e5e5e5;
        }
        table tbody tr:nth-child(even) {
            background-color: #383838;
        }
        table tbody tr:nth-child(odd) {
            background-color: #333333;
        }
        table tfoot {
            background: linear-gradient(135deg, #2a2a2a 0%, #1f1f1f 100%);
            font-weight: bold;
            color: #ffffff;
            border-top: 3px solid #10b981;
        }
        table tfoot td {
            padding: 11px 8px;
            font-size: 11px;
        }
        .list-item {
            padding: 10px 12px;
            margin-bottom: 6px;
            border-bottom: 1px solid #404040;
            display: table;
            width: 100%;
            background-color: #333333;
            border-radius: 6px;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }
        .list-item:nth-child(odd) {
            background-color: #383838;
        }
        .list-item .left {
            display: table-cell;
            width: 70%;
            vertical-align: middle;
            color: #e5e5e5;
            line-height: 1.5;
        }
        .list-item .left strong {
            vertical-align: middle;
            font-size: 12px;
            color: #ffffff;
        }
        .list-item .left .secondary-text {
            font-size: 9px;
            color: #999999;
            font-weight: normal;
        }
        .list-item .right {
            display: table-cell;
            width: 30%;
            text-align: right;
            font-weight: bold;
            color: #ffffff;
            font-size: 14px;
            vertical-align: middle;
            padding-left: 12px;
        }
        .list-item .right .count-label {
            display: block;
            font-size: 7px;
            color: #999999;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }
        .badge {
            display: inline-block;
            width: 24px;
            height: 24px;
            background-color: #505050;
            color: white;
            font-size: 10px;
            font-weight: bold;
            margin-right: 10px;
            border-radius: 50%;
            border: 2px solid #707070;
            text-align: center;
            line-height: 20px;
            vertical-align: middle;
            box-shadow: 0 2px 8px rgba(0,0,0,0.4);
        }

        .badge.yellow {
            background-color: #f59e0b;
            border-color: #fbbf24;
            box-shadow: 0 2px 8px rgba(251,191,36,0.3);
        }
        .badge.pink {
            background-color: #ec4899;
            border-color: #f472b6;
            box-shadow: 0 2px 8px rgba(236,72,153,0.3);
        }
        .category-grid {
            display: table;
            width: 100%;
        }
        .category-row {
            display: table-row;
        }
        .category-item {
            display: table-cell;
            width: 33.33%;
            padding: 12px;
            border: 2px solid #505050;
            border-radius: 8px;
            vertical-align: top;
            background-color: #333333;
            box-shadow: 0 2px 12px rgba(0,0,0,0.5);
            position: relative;
        }
        .category-item::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #10b981, #3b82f6);
            border-radius: 0 0 6px 6px;
        }
        .category-header {
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 10px;
            font-size: 12px;
            line-height: 1.3;
        }
        .category-detail {
            font-size: 10px;
            color: #b3b3b3;
            margin-top: 8px;
            line-height: 1.6;
        }
        .category-detail div {
            padding: 2px 0;
        }
        .revenue-value {
            color: #10b981;
            font-weight: 700;
            font-size: 10px;
        }
        .footer {
            text-align: center;
            font-size: 9px;
            color: #b3b3b3;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #505050;
        }
        .footer strong {
            color: #ffffff;
            font-size: 11px;
        }
        .two-column {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-spacing: 10px;
        }
        .column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .column:first-child {
            padding-right: 5px;
        }
        .column:last-child {
            padding-left: 5px;
        }
    </style>
</head>

<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <h1>{{ $adminName }} Statistics</h1>
        <p class="period">{{ $periodLabel }}</p>
        <p class="meta">
            Generated: {{ now()->format('F d, Y - h:i A') }}<br>
            Analysis Period: {{ $startDate }} to {{ $endDate }}
        </p>
    </div>

    <!-- Summary Cards -->
    <div class="stats-grid">
        <div class="stats-row">
            <div class="stat-card">
                <div class="icon">VEHICLES</div>
                <div class="value">{{ number_format($platformStats['total_vehicles']) }}</div>
                <div class="sub-value">{{ number_format($platformStats['active_vehicles']) }} available</div>
            </div>
            <div class="stat-card">
                <div class="icon">REVENUE</div>
                <div class="value" style="color: #059669;">₱{{ number_format($platformStats['total_revenue'], 0) }}</div>
                <div class="sub-value">{{ number_format($platformStats['completed_bookings']) }} completed</div>
            </div>
            <div class="stat-card">
                <div class="icon">COMMUNITY</div>
                <div class="value">{{ number_format($platformStats['total_users']) }}</div>
                <div class="sub-value">{{ number_format($platformStats['total_owners']) }} owners</div>
            </div>
            <div class="stat-card">
                <div class="icon">BOOKINGS</div>
                <div class="value">{{ number_format($platformStats['total_bookings']) }}</div>
                <div class="sub-value" style="color: #d97706;">{{ number_format($platformStats['pending_bookings']) }} pending</div>
            </div>
        </div>
    </div>

    <!-- Metrics Bar -->
    <div class="metrics-grid">
        <div class="metrics-row">
            <div class="metric-box green">
                <div class="value">{{ $vehicleAvailability }}%</div>
                <div class="label">Vehicle Availability</div>
            </div>
            <div class="metric-box blue">
                <div class="value">{{ $completionRate }}%</div>
                <div class="label">Completion Rate</div>
            </div>
            <div class="metric-box purple">
                <div class="value">₱{{ number_format($averageBookingValue, 0) }}</div>
                <div class="label">Avg. Booking Value</div>
            </div>
        </div>
    </div>



    <!-- Revenue Breakdown - Full Width -->
    <div class="section">
        <h2 class="section-title">Revenue Breakdown</h2>
        @if(count($revenueData) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th style="text-align: right;">Daily Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($revenueData->take(8) as $revenue)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($revenue->date)->format('M d, Y') }}</td>
                            <td style="text-align: right; font-weight: 600;">₱{{ number_format($revenue->total, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>TOTAL REVENUE</td>
                        <td style="text-align: right;">₱{{ number_format($revenueData->sum('total'), 0) }}</td>
                    </tr>
                </tfoot>
            </table>
        @else
            <p style="text-align: center; color: #9ca3af; padding: 15px 0;">No revenue data available</p>
        @endif
    </div>

        <!-- Top Lists - Side by Side -->
    <div class="two-column">
        <!-- Top Vehicles -->
        <div class="column">
            <div class="section">
                <h2 class="section-title">Most Popular Vehicles</h2>
                @if(count($vehicleUtilization) > 0)
                    @foreach($vehicleUtilization->take(5) as $index => $vehicle)
                        <div class="list-item">
                            <div class="left">
                                <span class="badge yellow">{{ $index + 1 }}</span>
                                <span>
                                    <strong>{{ $vehicle->make->name ?? '' }} {{ $vehicle->model->name ?? '' }}</strong><br>
                                    <span class="secondary-text">{{ $vehicle->license_plate }}</span>
                                </span>
                            </div>
                            <div class="right">
                                {{ $vehicle->bookings_count }}
                                <span class="count-label">Bookings</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p style="text-align: center; color: #9ca3af; padding: 10px 0;">No vehicle data</p>
                @endif
            </div>
        </div>

        <!-- Top Users -->
        <div class="column">
            <div class="section">
                <h2 class="section-title">Top Users by Bookings</h2>
                @if(count($topUsers) > 0)
                    @foreach($topUsers->take(5) as $index => $user)
                        <div class="list-item">
                            <div class="left">
                                <span class="badge pink">{{ $index + 1 }}</span>
                                <span>
                                    <strong>{{ $user->first_name }} {{ $user->last_name }}</strong><br>
                                    <span class="secondary-text">{{ Str::limit($user->email, 25) }}</span>
                                </span>
                            </div>
                            <div class="right">
                                {{ $user->bookings_count }}
                                <span class="count-label">Bookings</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p style="text-align: center; color: #9ca3af; padding: 10px 0;">No user data</p>
                @endif
            </div>
        </div>
    </div>
    <!-- Vehicle Categories -->
    <div class="section">
        <h2 class="section-title">Vehicle Categories Performance</h2>
        @if(count($vehicleCategories) > 0)
            <div class="category-grid">
                @foreach($vehicleCategories->take(6) as $index => $category)
                    @if(($index) % 3 == 0)
                        <div class="category-row">
                    @endif
                            <div class="category-item">
                                <div class="category-header">
                                    <span class="badge" style="background-color: #6366f1;">{{ $index + 1 }}</span>
                                    {{ Str::limit($category['name'], 18) }}
                                    <span style="float: right; font-weight: bold;">{{ $category['booking_count'] }}</span>
                                </div>
                                <div class="category-detail">
                                    <div>Vehicles: <strong>{{ $category['vehicle_count'] }}</strong></div>
                                    <div>Revenue: <span class="revenue-value">₱{{ number_format($category['total_revenue'], 0) }}</span></div>
                                </div>
                            </div>
                    @if(($index + 1) % 3 == 0 || $loop->last)
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <p style="text-align: center; color: #9ca3af; padding: 15px 0;">No category data available</p>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>GoMoto Car Rental Platform</strong> - Professional Analytics Report</p>
        <p>&copy; {{ date('Y') }} GoMoto. Confidential Business Intelligence - All rights reserved.</p>
    </div>
</div>

</body>
</html>