# Vehicle Rental Dashboard System

## Overview

The application now features separate, role-based dashboards:

1. **User Dashboard** - For renters to manage their bookings
2. **Owner Dashboard** - For vehicle owners to manage their fleet and earnings

## Dashboard Routing

### Automatic Role-Based Redirection

When users visit `/dashboard`, they are automatically redirected based on their role:

- **Owners** → `/owner/dashboard` (Owner Dashboard)
- **Regular Users** → `/dashboard` (User Dashboard)

## User Dashboard Features

**Located at:** `/dashboard`  
**Controller:** `DashboardController`  
**View:** `resources/js/Pages/Dashboard.vue`

### Key Features:
- **Active Rental Alert** - Shows currently rented vehicle with time remaining
- **Recent Bookings** - Last 5 bookings with status and quick actions
- **Booking Statistics** - Total bookings, completed trips, total spent, overcharges
- **Quick Actions** - Browse vehicles, manage bookings, update profile
- **Featured Vehicles** - Recommended vehicles for next rental

### Data Displayed:
- Current rental status and time remaining
- Return dates and overdue alerts
- Overcharge notifications and extension options
- Booking history and payment status
- Contact information for vehicle owners

## Owner Dashboard Features

**Located at:** `/owner/dashboard`  
**Controller:** `OwnerDashboardController`  
**View:** `resources/js/Pages/Owner/Dashboard.vue`

### Key Sections:

#### 1. Vehicle Statistics
- Total vehicles owned
- Available vs. unavailable vehicles  
- Currently booked vehicles
- Pending booking requests

#### 2. Earnings Overview
- Total lifetime earnings
- This month's earnings
- Pending payments
- Overcharge earnings

#### 3. Active Rentals Management
- Real-time view of vehicles currently rented out
- Time remaining until return
- Overdue alerts and potential overcharges
- Direct contact with renters
- Quick access to booking details

#### 4. Recent Bookings
- Last 10 bookings across all vehicles
- Booking status and payment information
- Renter contact details

#### 5. Vehicle Performance Analytics
- Top-performing vehicles by earnings
- Booking counts and completion rates
- Individual vehicle statistics

#### 6. Quick Actions
- Add new vehicle
- Manage all bookings
- Vehicle management
- Calendar view

#### 7. Popular Vehicle Insights
- Most frequently booked vehicle
- Booking statistics

#### 8. Calendar Preview
- Upcoming bookings (next 30 days)
- Booking timeline visualization

## Technical Implementation

### Controllers

**DashboardController** (User Dashboard)
```php
- Checks user role and redirects owners
- Fetches user's recent bookings and statistics
- Calculates active rental status
- Provides booking management data
```

**OwnerDashboardController** (Owner Dashboard)
```php
- Vehicle statistics and performance metrics
- Earnings calculations and analytics
- Active rental monitoring
- Calendar event generation
- Popular vehicle identification
```

### Routes

```php
// Main dashboard (auto-redirects owners)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Owner dashboard
Route::middleware(['auth', 'role:owner, admin'])->prefix('owner')->group(function () {
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])
        ->name('owner.dashboard');
    
    // Other owner routes...
});
```

### Key Features for Owners

1. **Real-time Monitoring**
   - Live tracking of rental periods
   - Overdue notifications
   - Overcharge alerts

2. **Financial Insights**
   - Revenue tracking
   - Payment status monitoring
   - Performance analytics

3. **Fleet Management**
   - Vehicle availability status
   - Performance comparisons
   - Popular vehicle identification

4. **Booking Management**
   - Pending request notifications
   - Active rental oversight
   - Historical booking data

5. **Direct Communication**
   - Renter contact information
   - Quick call/message options

### Key Features for Users

1. **Booking Overview**
   - Current rental status
   - Upcoming returns
   - Booking history

2. **Overcharge Management**
   - Late return warnings
   - Extension options
   - Payment tracking

3. **Quick Actions**
   - Vehicle browsing
   - Booking management
   - Profile updates

## Database Requirements

The dashboard system uses existing models and relationships:

- `User` (with role relationship)
- `Vehicle` (with owner relationship)
- `Booking` (with calculated end times)
- `Payment` (payment status)
- `Role` (user role identification)

## Future Enhancements

Potential improvements could include:

1. **Real-time Updates**
   - WebSocket integration for live updates
   - Push notifications

2. **Advanced Analytics**
   - Revenue forecasting
   - Seasonal trends
   - Market insights

3. **Mobile Optimization**
   - Responsive design improvements
   - Mobile app integration

4. **GPS Integration**
   - Real-time vehicle tracking
   - Location-based services

This dashboard system provides owners with comprehensive fleet management tools while giving users clear visibility into their rental activities and obligations.
