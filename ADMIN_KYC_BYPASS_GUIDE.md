# Admin Role KYC Bypass Implementation

## Overview

This implementation ensures that users with the `admin` role are exempt from KYC (Know Your Customer) verification requirements, email verification, and other verification processes that apply to regular users (`customer` and `owner` roles).

## Changes Made

### 1. User Model Updates (`app/Models/User.php`)

#### New Helper Methods:
- `isAdmin()` - Check if user has admin role
- `isOwner()` - Check if user has owner role  
- `isCustomer()` - Check if user has customer role
- `hasVerifiedEmail()` - Overrides email verification check for admins
- `requiresKycVerification()` - Returns false for admin users

#### Updated Permission Methods:
- `canMakeBookings()` - Now returns true for admin users regardless of KYC status
- `canListVehicles()` - Now returns true for admin users regardless of KYC status

### 2. Middleware Updates

#### KYC Verification Middleware (`app/Http/Middleware/KycVerifiedMiddleware.php`)
- Added early return for admin users to bypass all KYC verification checks

#### Email Verification Middleware (`app/Http/Middleware/EnsureEmailIsVerified.php`)
- Created custom middleware that extends Laravel's default email verification
- Admin users bypass email verification requirements

### 3. Controller Updates

#### Profile Controller (`app/Http/Controllers/ProfileController.php`)
- `submitKyc()` method now prevents admin users from submitting KYC documents with an informational message

#### Admin Controller (`app/Http\Controllers\AdminController.php`)
- `updateUserRole()` method now automatically grants admin permissions when promoting a user to admin role

### 4. Database Migration

#### Migration: `2025_08_30_000000_update_admin_users_permissions.php`
- Automatically updates existing admin users to have proper permissions:
  - `can_book` = true
  - `can_list_vehicles` = true
  - `kyc_status` = 'approved'
  - `kyc_verified_at` = current timestamp
  - `email_verified_at` = current timestamp
  - `status` = 'active'

### 5. User Observer (`app/Observers/UserObserver.php`)

- Automatically sets admin permissions when a user is created or updated with admin role
- Registered in `AppServiceProvider`

### 6. Console Command (`app/Console/Commands/GrantAdminRole.php`)

A new Artisan command to promote users to admin role:

```bash
php artisan user:make-admin user@example.com
```

This command:
- Finds the user by email
- Updates their role to admin
- Grants all admin permissions automatically
- Provides confirmation output

### 7. Middleware Registration (`bootstrap/app.php`)

- Registered custom email verification middleware to override Laravel's default

## Admin User Privileges

Admin users automatically receive the following privileges:

1. **Email Verification**: Not required - automatically considered verified
2. **KYC Verification**: Not required - automatically considered approved
3. **Driver's License**: Not required for booking vehicles
4. **Booking Permission**: Automatically granted
5. **Vehicle Listing Permission**: Automatically granted
6. **Account Status**: Automatically set to active

## Usage Examples

### Creating an Admin User via Command
```bash
php artisan user:make-admin admin@gomoto.com
```

### Promoting Existing User to Admin (via Admin Panel)
Admin users can promote other users through the admin dashboard user management interface.

### Checking Admin Status in Code
```php
if ($user->isAdmin()) {
    // Admin-specific logic
}

if (!$user->requiresKycVerification()) {
    // Skip KYC requirements
}
```

## Security Considerations

1. **Role Protection**: Existing admin users cannot have their roles changed by other admins
2. **Self-Role Protection**: Users cannot change their own roles
3. **Permission Inheritance**: Admin permissions are automatically applied when role is changed
4. **Audit Trail**: All role changes and permission updates are logged

## Testing

Comprehensive test suite included in `tests/Feature/AdminPermissionsTest.php` covering:
- Admin booking permissions without KYC
- Admin vehicle listing permissions without KYC
- Email verification bypass for admins
- Role helper method functionality
- KYC requirement bypass for admins

Run tests with:
```bash
php artisan test tests/Feature/AdminPermissionsTest.php
```

## Migration Instructions

1. Run the migration to update existing admin users:
   ```bash
   php artisan migrate
   ```

2. (Optional) Create your first admin user:
   ```bash
   php artisan user:make-admin your-email@example.com
   ```

3. Verify the changes are working by logging in as an admin user and checking that no KYC verification is required for booking or listing vehicles.
