# Vehicle Availability System - Enhanced Features

## Overview
The vehicle availability system has been enhanced with time-based restrictions and advanced recurring patterns. This allows vehicle owners to have granular control over when their vehicles are available for rent.

## New Features

### 1. Time-Based Availability Restrictions
Vehicle owners can now block specific hours of the day instead of entire days.

**Use Cases:**
- Block midnight hours (11 PM - 6 AM) for safety
- Block evening hours (6 PM - 8 AM) for personal use
- Allow only morning/afternoon rentals

**How it works:**
- Check "Time-based restriction" when creating a block
- Uncheck "Block entire day" to set specific hours
- Set start and end times (e.g., 23:00 - 06:00)

### 2. Recurring Blocks for Specific Days
Owners can create recurring blocks for specific days of the week.

**Use Cases:**
- Every Tuesday and Friday for maintenance
- Every Monday for personal use
- Every weekend for family time

**How it works:**
- Use the "Recurring Days Block" button
- Select specific days (e.g., Tuesday, Friday)
- Set optional end date for the recurring pattern
- Can be combined with time restrictions

### 3. Enhanced Block Types
New block type added:
- **Time Restriction**: For general time-based limitations

### 4. Updated Database Schema
New fields in `vehicle_availability_blocks` table:
- `is_time_based`: Boolean flag for time-based blocks
- `start_time`: Start time for restriction (e.g., "23:00")
- `end_time`: End time for restriction (e.g., "06:00")
- `affects_whole_day`: Whether to ignore time settings
- `recurring_days`: JSON array of day numbers (0=Sunday, 1=Monday, etc.)

### 5. Enhanced User Interface
- **Three Modal Types:**
  - Block Selected Dates: For specific date selections
  - Block Date Range: For continuous date ranges
  - Recurring Days Block: For repeating day patterns

- **Visual Indicators:**
  - Purple badges for recurring blocks
  - Blue badges for time-based blocks
  - Time displays (🕐 23:00 - 06:00)
  - Recurring pattern displays (📅 Every Tue, Fri)

## Usage Examples

### Example 1: Block Midnight Hours
```
Block Type: Time Restriction
Time-based: ✓
Affects whole day: ✗
Start Time: 23:00
End Time: 06:00
Reason: Safety - no midnight rentals
```

### Example 2: Maintenance Every Tuesday and Friday
```
Recurring Days Block:
Selected Days: Tuesday, Friday
Block Type: Maintenance
Time-based: ✗ (whole day)
Reason: Regular maintenance schedule
```

### Example 3: Personal Use Every Evening
```
Recurring Days Block:
Selected Days: All days (Mon-Sun)
Block Type: Personal Use
Time-based: ✓
Start Time: 18:00
End Time: 08:00
Reason: Personal and family use
```

## API Endpoints

### New Route Added:
```php
POST /vehicles/{vehicle}/availability/recurring-days
```

### Updated Validation Rules:
```php
'is_time_based' => 'boolean',
'affects_whole_day' => 'boolean',
'start_time' => 'nullable|required_if:is_time_based,true|date_format:H:i',
'end_time' => 'nullable|required_if:is_time_based,true|date_format:H:i|after:start_time',
'recurring_days' => 'nullable|array|min:1',
'recurring_days.*' => 'integer|between:0,6',
'recurring_type' => 'nullable|in:weekly,monthly,yearly,custom_days',
```

## Model Methods

### New Methods in VehicleAvailabilityBlock:
- `isDateBlocked($vehicleId, $date, $time = null)`: Check if date/time is blocked
- `getTimeBlocksForDate($vehicleId, $date)`: Get time blocks for specific date
- `getDaysOfWeek()`: Get array of day names
- Enhanced `isDateInRecurringPattern()`: Supports custom day patterns

## Benefits

1. **Flexible Scheduling**: Owners can block specific hours instead of entire days
2. **Maintenance Planning**: Easy setup of recurring maintenance schedules
3. **Safety Controls**: Block unsafe hours (e.g., midnight rentals)
4. **Personal Use**: Reserve evening/morning hours for personal use
5. **Business Optimization**: Maximize availability while maintaining control

## Technical Implementation

### Database Migration:
```sql
ALTER TABLE vehicle_availability_blocks 
ADD COLUMN is_time_based BOOLEAN DEFAULT FALSE,
ADD COLUMN start_time TIME NULL,
ADD COLUMN end_time TIME NULL,
ADD COLUMN affects_whole_day BOOLEAN DEFAULT TRUE,
ADD COLUMN recurring_days JSON NULL;
```

### Frontend Components:
- Enhanced Vue.js modals with time pickers
- Day selection checkboxes
- Time-based form validation
- Visual block indicators

This enhanced system provides vehicle owners with comprehensive control over their vehicle availability while maintaining a user-friendly interface.
