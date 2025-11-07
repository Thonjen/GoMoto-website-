# SMS Notifications Implementation Guide

## Overview
This document describes the SMS notification system implemented for the GoMoto booking platform. The system sends automated SMS messages to renters at key points in the booking lifecycle.

## SMS Scenarios

### Scenario 1: Booking Confirmation
**Trigger**: When the owner accepts/confirms a booking request by clicking either:
- "Confirm Payment & Booking" button (for GCash payments)
- "Confirm Booking" button (for COD payments)

**Location**: 
- Controller: `BookingController::confirm()` and `BookingController::confirmPayment()`
- Service Method: `SmsService::sendBookingConfirmation()`

**Message Content**:
```
Hi {FirstName}! Your booking has been CONFIRMED by the owner.

Vehicle: {Make} {Model} ({Type})
License Plate: {LicensePlate}
Pickup: {PickupDateTime}
Return: {ExpectedReturnDateTime}
Location: {PickupLocation}
Amount: ₱{TotalAmount}

Please arrive on time. Contact owner: {OwnerPhone}
Thank you for using GoMoto!
```

**Implementation**:
- Loads required relationships (user, vehicle, make, model, type, owner, pricingTier)
- Formats dates and amounts for readability
- Includes owner contact information for coordination
- Wrapped in try-catch to prevent booking failure if SMS fails

---

### Scenario 2: Return Reminder (10 Minutes Before Due)
**Trigger**: Automated task that runs every minute via Laravel scheduler

**Location**:
- Command: `App\Console\Commands\SendReturnReminders`
- Service Method: `SmsService::sendReturnReminder()`
- Scheduled in: `routes/console.php`

**Message Content**:
```
REMINDER: {FirstName}, your {Make} {Model} rental is due in 10 MINUTES at {ReturnTime}.

Please return the vehicle on time to avoid overcharge fees. If you need more time, request an extension through the app.

Late returns may incur additional charges as per the rental agreement.
GoMoto
```

**Implementation**:
- Runs every minute to ensure timely delivery
- Checks all active (confirmed) bookings without return_time
- Calculates expected return time based on pricing tier
- Sends SMS when 10-11 minutes remain before due time
- Logs all reminder attempts for auditing

**Setup Required**:
```bash
# Make sure Laravel scheduler is running
# Add to cron (Linux/Mac):
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1

# Or run manually for testing:
php artisan bookings:send-return-reminders
```

---

### Scenario 3: Booking Completion
**Trigger**: When the owner clicks "Mark as Completed" button

**Location**:
- Controller: `BookingController::complete()`
- Service Method: `SmsService::sendBookingCompletion()`

**Message Content (No Overcharges)**:
```
Hi {FirstName}! Your {Make} {Model} rental has been successfully COMPLETED.

Return Time: {ReturnDateTime}
Initial Amount: ₱{TotalAmount}

No additional charges applied. Thank you for returning on time!

Thank you for using GoMoto. We hope to serve you again!
```

**Message Content (With Overcharges)**:
```
Hi {FirstName}! Your {Make} {Model} rental has been successfully COMPLETED.

Return Time: {ReturnDateTime}
Initial Amount: ₱{TotalAmount}
Overcharge Applied: ₱{OverchargeAmount}
Total: ₱{GrandTotal}

Please settle the overcharge payment with the owner.

Thank you for using GoMoto. We hope to serve you again!
```

**Implementation**:
- Calculates overcharges automatically
- Includes overcharge details in message if applicable
- Marks vehicle as available again
- Wrapped in try-catch to prevent completion failure if SMS fails

---

## Technical Architecture

### SmsService Structure
```php
namespace App\Services;

class SmsService
{
    // Core SMS sending method
    public function send(string $to, string $message): bool
    
    // Scenario 1: Booking confirmation
    public function sendBookingConfirmation($booking): bool
    
    // Scenario 2: 10-minute return reminder
    public function sendReturnReminder($booking): bool
    
    // Scenario 3: Booking completion
    public function sendBookingCompletion($booking, $overchargeAmount = 0): bool
    
    // Additional method for KYC verification
    public function sendVerificationToUser(User $user): bool
}
```

### Configuration
SMS service configuration is stored in `config/sms.php`:
```php
return [
    'base_url' => env('SMS_BASE_URL'),
    'token' => env('SMS_TOKEN'),
];
```

Required `.env` entries:
```env
SMS_BASE_URL=https://your-sms-gateway.com/api
SMS_TOKEN=your-api-token-here
```

---

## Error Handling

All SMS operations include comprehensive error handling:

1. **Try-Catch Blocks**: All controller methods wrap SMS calls in try-catch
2. **Logging**: Failed SMS attempts are logged with booking_id and error details
3. **Non-Blocking**: SMS failures don't prevent booking operations from completing
4. **Phone Validation**: Empty phone numbers are caught before API calls

### Log Examples
```php
// Success log
Log::info('SmsService: SMS sent successfully', [
    'to' => $user->phone,
    'status' => $response->status()
]);

// Failure log
Log::error('Failed to send booking confirmation SMS', [
    'booking_id' => $booking->id,
    'error' => $e->getMessage()
]);
```

---

## Testing

### Manual Testing

1. **Test Scenario 1** (Booking Confirmation):
```bash
# Create a test booking and confirm it through the UI
# Check logs:
tail -f storage/logs/laravel.log | grep "SmsService"
```

2. **Test Scenario 2** (Return Reminder):
```bash
# Run the command manually:
php artisan bookings:send-return-reminders

# Check output and logs
```

3. **Test Scenario 3** (Completion):
```bash
# Mark a booking as complete through the UI
# Check logs for SMS confirmation
```

### Debugging

Enable verbose logging by checking `config/logging.php`:
```php
'default' => env('LOG_CHANNEL', 'stack'),
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single'],
        'ignore_exceptions' => false,
    ],
    // ...
],
```

---

## Database Requirements

### Required Fields

**bookings table**:
- `user_id` - Foreign key to users
- `vehicle_id` - Foreign key to vehicles
- `pricing_tier_id` - Foreign key to pricing tiers
- `pickup_datetime` - When rental starts
- `return_time` - When vehicle was returned (nullable)
- `status` - Booking status (pending/confirmed/completed/cancelled)
- `total_amount` - Total cost
- `calculated_end_datetime` - Expected return time (computed)

**users table**:
- `phone` - User's phone number (required for SMS)
- `first_name` - User's first name
- `last_name` - User's last name

**vehicles table**:
- `license_plate` - Vehicle identifier
- `location` - Pickup location
- `owner_id` - Foreign key to vehicle owner

---

## Scheduler Setup

### Windows (XAMPP/Local Development)
Add a scheduled task that runs every minute:
```batch
php C:\xampp\htdocs\backup\GoMoto\GoMotoWebsite\artisan schedule:run
```

### Linux/Production Server
Add to crontab:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### Verify Scheduler is Running
```bash
# Check scheduled tasks
php artisan schedule:list

# Run scheduler once to test
php artisan schedule:run
```

---

## Future Enhancements

### Potential Improvements

1. **SMS Delivery Tracking**
   - Add `booking_sms_logs` table to track all SMS sent
   - Store delivery status and timestamps

2. **User Preferences**
   - Allow users to opt-out of reminder SMS
   - Add preference for reminder timing (5, 10, or 15 minutes)

3. **SMS Templates**
   - Move message templates to database
   - Allow admins to customize SMS content

4. **Multiple Reminders**
   - Add 1-hour reminder before return
   - Add 24-hour reminder before pickup

5. **Two-Way SMS**
   - Allow users to reply to SMS for extensions
   - Implement SMS-based customer support

6. **SMS Analytics**
   - Track delivery rates
   - Monitor SMS costs
   - Generate SMS usage reports

---

## Troubleshooting

### Common Issues

**Issue**: SMS not sending
- Check `.env` has correct SMS_BASE_URL and SMS_TOKEN
- Verify user has valid phone number
- Check logs: `storage/logs/laravel.log`
- Test SMS service manually: `php artisan tinker` → `app(SmsService::class)->send('phone', 'test')`

**Issue**: Return reminders not sending
- Verify scheduler is running: `php artisan schedule:list`
- Check if command is registered: `php artisan list | grep bookings`
- Run command manually: `php artisan bookings:send-return-reminders`
- Check if bookings have `return_time` set (should be null for active bookings)

**Issue**: Wrong phone number format
- Ensure phone numbers are stored with country code
- Format example: +639171234567
- Update SmsService to handle formatting if needed

---

## Support

For issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Enable debug mode: Set `APP_DEBUG=true` in `.env`
3. Test SMS gateway connection manually
4. Review this documentation for common solutions

## Version History

- **v1.0** (2025-11-07): Initial implementation of three SMS scenarios
  - Booking confirmation
  - 10-minute return reminder
  - Booking completion notification
