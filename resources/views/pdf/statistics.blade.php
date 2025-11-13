<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $ownerName }} Business Analytics - {{ $periodLabel }}</title>
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
        <h1>{{ $ownerName }} Business Analytics</h1>
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
                <div class="icon">MY VEHICLES</div>
                <div class="value">{{ number_format($ownerStats['total_vehicles']) }}</div>
                <div class="sub-value">{{ number_format($ownerStats['active_vehicles']) }} available</div>
            </div>
            <div class="stat-card">
                <div class="icon">REVENUE</div>
                <div class="value" style="color: #059669;">&#8369;{{ number_format($ownerStats['total_earnings'], 0) }}</div>
                <div class="sub-value">{{ number_format($ownerStats['completed_bookings']) }} completed</div>
            </div>
            <div class="stat-card">
                <div class="icon">BOOKINGS</div>
                <div class="value">{{ number_format($ownerStats['total_bookings']) }}</div>
                <div class="sub-value" style="color: #d97706;">{{ number_format($ownerStats['pending_bookings']) }} pending</div>
            </div>
            <div class="stat-card">
                <div class="icon">RATING</div>
                <div class="value">{{ $ownerStats['avg_vehicle_rating'] ? number_format($ownerStats['avg_vehicle_rating'], 1) : 'N/A' }}</div>
                <div class="sub-value">average score</div>
            </div>
        </div>
    </div>

    <!-- Metrics Bar -->
    <div class="metrics-grid">
        <div class="metrics-row">
            <div class="metric-box green">
                <div class="value">{{ $completionRate }}%</div>
                <div class="label">Completion Rate</div>
            </div>
            <div class="metric-box blue">
                <div class="value">{{ $vehicleAvailability }}%</div>
                <div class="label">Vehicle Availability</div>
            </div>
            <div class="metric-box purple">
                <div class="value">&#8369;{{ number_format($averageBookingValue, 0) }}</div>
                <div class="label">Avg. Booking Value</div>
            </div>
        </div>
    </div>

    <!-- Monthly Earnings - Full Width -->
    <div class="section">
        <h2 class="section-title">Monthly Revenue Performance</h2>
        @if(count($monthlyEarnings) > 0)
            @php
                $maxEarnings = max(array_column($monthlyEarnings->toArray(), 'earnings'));
                $maxBookings = max(array_column($monthlyEarnings->toArray(), 'bookings'));
                    $earningsSteps = [];
    for ($i = 15; $i >= 0; $i--) {
        $earningsSteps[] = round($maxEarnings * ($i / 15));
    }
            @endphp
            
            <!-- Chart Container -->
            <div style="position: relative; height: 180px; margin-bottom: 15px; padding: 20px 15px; background-color: #2a2a2a; border-radius: 8px; border: 1px solid #404040;">
                <!-- Y-axis labels (Revenue) -->
                <div style="position: absolute; left: 0; top: 20px; bottom: 30px; width: 60px; display: flex; flex-direction: column; justify-content: space-between;">
@foreach ($earningsSteps as $step)
    <div style="font-size: 8px; color: #10b981; text-align: right; padding-right: 8px;">
        &#8369;{{ number_format($step, 0) }}
    </div>
@endforeach
                </div>
                
                <!-- Grid lines -->
                <div style="position: absolute; left: 65px; right: 15px; top: 20px; bottom: 30px;">
                    <div style="position: absolute; width: 100%; height: 1px; background-color: #404040; top: 0%;"></div>
                    <div style="position: absolute; width: 100%; height: 1px; background-color: #404040; top: 10%;"></div>
                    <div style="position: absolute; width: 100%; height: 1px; background-color: #404040; top: 20%;"></div>
                    <div style="position: absolute; width: 100%; height: 1px; background-color: #404040; top: 30%;"></div>
                    <div style="position: absolute; width: 100%; height: 1px; background-color: #404040; top: 40%;"></div>
                    <div style="position: absolute; width: 100%; height: 1px; background-color: #404040; top: 50%;"></div>
                    <div style="position: absolute; width: 100%; height: 1px; background-color: #404040; top: 60%;"></div>
                    <div style="position: absolute; width: 100%; height: 1px; background-color: #404040; top: 70%;"></div>
                    <div style="position: absolute; width: 100%; height: 1px; background-color: #404040; top: 80%;"></div>
                    <div style="position: absolute; width: 100%; height: 1px; background-color: #404040; top: 90%;"></div>
                </div>
                
                <!-- Line Chart -->
                <div style="position: absolute; left: 65px; right: 15px; top: 20px; bottom: 30px;">
                    @php
                        $chartWidth = 100 / count($monthlyEarnings);
                        $prevX = null;
                        $prevY = null;
                    @endphp
                    
                    @foreach($monthlyEarnings as $index => $month)
                        @php
                            $x = ($index * $chartWidth) + ($chartWidth / 2);
                            $y = 100 - (($month['earnings'] / max($maxEarnings, 1)) * 100);
                            
                            // Line to previous point
                            if ($prevX !== null) {
                                $lineWidth = abs($x - $prevX);
                                $lineHeight = abs($y - $prevY);
                                $angle = atan2($y - $prevY, $x - $prevX) * (180 / pi());
                                $length = sqrt(pow($x - $prevX, 2) + pow($y - $prevY, 2));
                            }
                        @endphp
                        
                        <!-- Line segment -->
                        @if($prevX !== null)
                            <div style="position: absolute; left: {{ $prevX }}%; top: {{ $prevY }}%; width: {{ $length }}%; height: 3px; background: linear-gradient(90deg, #10b981, #34d399); transform-origin: left center; transform: rotate({{ $angle }}deg);"></div>
                        @endif
                        
                        <!-- Data point -->
                        <div style="position: absolute; left: {{ $x }}%; top: {{ $y }}%; width: 8px; height: 8px; background-color: #10b981; border: 2px solid #ffffff; border-radius: 50%; transform: translate(-50%, -50%); box-shadow: 0 0 8px rgba(16, 185, 129, 0.6);"></div>
                        
                        <!-- Value label -->
                        <div style="position: absolute; left: {{ $x }}%; top: {{ max(0, $y - 10) }}%; transform: translate(-50%, -100%); font-size: 8px; color: #10b981; font-weight: bold; white-space: nowrap;">&#8369;{{ number_format($month['earnings'], 0) }}</div>
                        
                        @php
                            $prevX = $x;
                            $prevY = $y;
                        @endphp
                    @endforeach
                </div>
                
                <!-- X-axis labels (Months) -->
                <div style="position: absolute; left: 65px; right: 15px; bottom: 5px; height: 20px; display: flex; justify-content: space-around;">
                    @foreach($monthlyEarnings as $month)
                        <div style="font-size: 8px; color: #999999; text-align: center; flex: 1;">{{ $month['month'] }}</div>
                    @endforeach
                </div>
            </div>
            
            <!-- Summary Stats Table -->
            <table style="margin-top: 15px;">
                <thead>
                    <tr>
                        <th>Period</th>
                        <th style="text-align: center;">Bookings</th>
                        <th style="text-align: right;">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyEarnings as $month)
                        <tr>
                            <td>{{ $month['month'] }}</td>
                            <td style="text-align: center;">{{ number_format($month['bookings']) }}</td>
                            <td style="text-align: right; font-weight: 600;">&#8369;{{ number_format($month['earnings'], 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>TOTAL</td>
                        <td style="text-align: center;">{{ number_format($monthlyEarnings->sum('bookings')) }}</td>
                        <td style="text-align: right;">&#8369;{{ number_format($monthlyEarnings->sum('earnings'), 0) }}</td>
                    </tr>
                </tfoot>
            </table>
        @else
            <p style="text-align: center; color: #9ca3af; padding: 15px 0;">No revenue data available</p>
        @endif
    </div>

    <!-- Vehicle Performance & Booking Status - Side by Side -->
    <div class="two-column">
        <!-- Top Performing Vehicles -->
        <div class="column">
            <div class="section">
                <h2 class="section-title">Vehicle Performance</h2>
                @if(count($vehiclePerformance) > 0)
                    @foreach($vehiclePerformance->take(5) as $index => $vehicle)
                        <div class="list-item">
                            <div class="left">
                                <span class="badge yellow">{{ $index + 1 }}</span>
                                <span>
                                    <strong>{{ $vehicle->name }}</strong><br>
                                    <span class="secondary-text">{{ $vehicle->plate_number }}</span>
                                </span>
                            </div>
                            <div class="right">
                                &#8369;{{ number_format($vehicle->total_earnings, 0) }}
                                <span class="count-label">{{ number_format($vehicle->booking_count) }} rentals</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p style="text-align: center; color: #9ca3af; padding: 10px 0;">No vehicle data</p>
                @endif
            </div>
        </div>



                <!-- Recent Customer Feedback -->
        <div class="column">
            <div class="section">
                <h2 class="section-title">Recent Customer Feedback</h2>
                @if(count($recentFeedback) > 0)
                    @foreach($recentFeedback->take(5) as $feedback)
                        <div class="list-item">
                            <div class="left">
                                <span class="badge pink">{{ strtoupper(substr($feedback->customer_name, 0, 1)) }}</span>
                                <span>
                                    <strong>{{ $feedback->customer_name }}</strong><br>
                                    <span class="secondary-text">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $feedback->rating)
                                                ★
                                            @else
                                                ☆
                                            @endif
                                        @endfor
                                        - {{ $feedback->plate_number }}
                                    </span>
                                </span>
                            </div>
                        </div>
                        @if($feedback->comment)
                            <div style="padding: 5px 12px 10px 48px; color: #b3b3b3; font-size: 9px; line-height: 1.4;">
                                "{{ Str::limit($feedback->comment, 80) }}"
                            </div>
                        @endif
                    @endforeach
                @else
                    <p style="text-align: center; color: #9ca3af; padding: 10px 0;">No feedback data</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Peak Hours & Recent Feedback - Side by Side -->
    <div class="two-column">
        <!-- Peak Booking Hours -->
        <div class="column">
            <div class="section">
                <h2 class="section-title">Peak Booking Hours</h2>
                @if(count($peakHours) > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>Hour</th>
                                <th style="text-align: right;">Bookings</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peakHours->take(8) as $hour)
                                <tr>
                                    <td>{{ $hour->hour }}</td>
                                    <td style="text-align: right; font-weight: 600;">{{ number_format($hour->count) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="text-align: center; color: #9ca3af; padding: 10px 0;">No peak hour data</p>
                @endif
            </div>
        </div>

                <!-- Booking Status Distribution -->
        <div class="column">
            <div class="section">
                <h2 class="section-title">Booking Status</h2>
                @if(count($bookingStatusStats) > 0)
                    @foreach($bookingStatusStats as $status => $count)
                        <div class="list-item">
                            <div class="left">
                                <span class="badge" style="
                                    @if($status === 'pending') background-color: #f59e0b; border-color: #fbbf24;
                                    @elseif($status === 'confirmed') background-color: #3b82f6; border-color: #60a5fa;
                                    @elseif($status === 'completed') background-color: #10b981; border-color: #34d399;
                                    @else background-color: #ef4444; border-color: #f87171;
                                    @endif
                                ">●</span>
                                <span>
                                    <strong style="text-transform: capitalize;">{{ str_replace('_', ' ', $status) }}</strong>
                                </span>
                            </div>
                            <div class="right">
                                {{ number_format($count) }}
                                <span class="count-label">bookings</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p style="text-align: center; color: #9ca3af; padding: 10px 0;">No booking data</p>
                @endif
            </div>
        </div>


    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>GoMoto Car Rental Platform</strong> - Owner Business Analytics Report</p>
        <p>&copy; {{ date('Y') }} GoMoto. Confidential Business Intelligence - All rights reserved.</p>
    </div>
</div>

</body>
</html>
