# 🚘 About GoMoto  (expanded for chatbot)

Why was GoMoto created?
GoMoto was built to replace manual, error-prone vehicle rentals in Surigao City with a real-time, mobile-first system that’s faster, clearer, and more trustworthy for both renters and owners.

What’s the main purpose?
To streamline the full rental flow — from discovering vehicles, booking, and paying, to managing rentals and reports — while giving owners and admins the right tools to handle operations efficiently.

Who uses it?
GoMoto is designed for three types of users:

Renters: People looking to book vehicles.

Owners: Individuals listing their vehicles for rent.

Administrators: Staff managing listings, payments, and reports.
Each has role-based dashboards and permissions for security and clarity.

What can I do as a renter?
You can browse available vehicles, view details and pickup locations, choose a rental plan, confirm your booking, and pay via QR code or cash-on-delivery (COD).

What tech is GoMoto built on?

Backend: PHP/Laravel + MySQL

Dashboards: React + Inertia.js

Mobile & PWA Frontend: React Native (Expo) + Tailwind

Maps & GPS: react-native-maps + Expo Location

Hosting: cPanel/Railway with HTTPS

Any limitations?
GoMoto currently focuses on Surigao City and does not support vans, long-distance rentals, or ride-hailing. An internet connection and phone GPS are required for full functionality.



# Owner — Capabilities (expanded for chatbot)

Purpose: This document explains what a vehicle Owner can do and includes example chatbot replies, step-by-step actions, and troubleshooting notes so the bot can answer owner queries accurately.

Overview
- Role: Owners manage only the vehicles they own. They cannot access global admin features or other owners' data.
- Scope: Create and maintain listings, set pricing & availability, manage bookings for owned vehicles, and configure payment preferences.

Key responsibilities
- Create, update, and remove your vehicle listings.
- Configure pricing tiers and availability windows per vehicle.
- Manage bookings (confirmations, extensions, and communication with renters).
- Set up payment preferences (upload GCash QR or other payout details) and view received payments.
- Handle minor disputes and add overcharges when appropriate.

Common owner tasks (with example chatbot responses)
- "How do I list a vehicle?"
  - Bot reply (short): "Go to Owner → Vehicles → Create. Fill in details, add photos, and save."
  - Bot reply (expanded): "Open Owner → Vehicle → Create. Add title, description, specs (type, transmission, fuel), upload photos, and set pricing tiers under Pricing Tiers. Save and publish. If your listing requires admin approval it will show a 'Pending' status."
  - Steps:
    1. Owner → Vehicle → Create
    2. Add vehicle details (make, model, year, features)
    3. Upload photos (use recommended aspect ratio)
    4. Add at least one pricing tier and availability
    5. Save and publish
- "How to change availability?"
  - Bot reply: "Open Owner → Vehicle → Availability, pick the vehicle, and block out dates or set available windows on the calendar."
- "How to approve an extension request?"
  - Bot reply: "Go to Owner → Extension Requests, open the request and click Approve or Decline. The renter will be notified."

Payments & payouts
- Setup: Owner → Payment Settings or Upload GCash QR. Upload QR or payout account details.
- View receipts: Owner → Payments or the specific booking → Payment section. The payments table stores receipts and methods.

Overcharges (damage/late fees)
- If your owner account allows overcharge operations, create an overcharge in Owner → Overcharge → Settings or apply a charge to the booking. Always include photos and notes for audit.

Where to go (UI + controller references)
- UI pages:
  - Owner dashboard: `resources/js/Pages/Owner/Dashboard.vue`
  - Create / edit vehicle: `resources/js/Pages/Owner/Vehicle/Create.vue`, `resources/js/Pages/Owner/Vehicle/Edit.vue`
  - Availability: `resources/js/Pages/Owner/Vehicle/Availability.vue`
  - Booking calendar: `resources/js/Pages/Owner/Booking/Calendar.vue`
  - Extension Requests: `resources/js/Pages/Owner/ExtensionRequests/Index.vue`
  - Payment settings: `resources/js/Pages/Owner/PaymentSettings.vue`, `resources/js/Pages/Owner/UploadGcashQr.vue`
- Backend controllers:
  - `app/Http/Controllers/OwnerDashboardController.php`
  - `app/Http/Controllers/VehicleController.php`
  - `app/Http/Controllers/VehicleDataController.php`
  - `app/Http/Controllers/BookingExtensionController.php`

Troubleshooting & tips for bot replies
- Q: "My photo upload failed" — A: "Check file size and format. Ensure `storage` is writable and run `php artisan storage:link` on the server."
- Q: "A booking doesn't appear" — A: "Refresh the calendar and ensure the booking isn't pending approval. Check Owner → Booking → Calendar and filter by vehicle."

Limitations & safety
- Owners cannot change platform-wide settings or manage other users. For platform-level requests, recommend contacting an Admin.
- For financial disputes or refunds, escalate to Admin/Finance; owners can propose overcharges but cannot perform refunds across the platform.



# User (Renter) — Capabilities (expanded for chatbot)

Purpose: Provide clear, flexible answers a chatbot can use when renters ask how to browse, book, pay, and manage bookings.

Quick start (short canonical reply)
- "Browse vehicles → Open vehicle → Select dates & pricing tier → Book → Pay → View in My Bookings." 

Step-by-step: How to rent a vehicle
1. Browse: Open the Vehicles listing (Home or Vehicles page) and filter by location, type, price, or features.
2. Select a vehicle: Click a listing to open the Vehicle Detail page and review photos, features, and pricing tiers.
3. Choose dates & pricing tier: Use the 'Select Pricing Tier' control to pick a rate and the rental period.
4. Create booking: Click Book / Reserve to create the booking record.
5. Upload required documents: If the vehicle or platform requires KYC/license, upload them during booking or under Profile → KYC.
6. Pay: Complete payment on the Payment page via the available payment modes.
7. Confirmations: After payment you’ll get a booking confirmation, and the booking will appear in My Bookings.

Key renter actions and flexible bot replies
- "Where do I find my bookings?"
  - Bot: "Open Menu → My Bookings. You'll see upcoming and past bookings and can click a booking to see details and actions."
- "How do I upload my license?"
  - Bot: "Go to Profile → KYC / Upload License or use the upload step during booking. After uploading, KYC will show as Pending until an Admin reviews it."
- "Can I cancel my booking?"
  - Bot: "If user cancellation is enabled, open the booking and choose Cancel. If not, contact support or the owner. Cancellation policies vary by booking and pricing tier."
- "How do I pay?"
  - Bot: "On the booking Payment page select a payment method (GCash, card, etc.) and follow the prompts. You’ll receive a receipt after successful payment."

Saved items and ratings
- Save a vehicle: click the heart / Save button on a listing. View saved vehicles in Saved Vehicles.
- Leave a rating: after a completed booking open the booking and choose Leave Review / Rating.

Error handling & troubleshooting (bot tips)
- Payment failed: "Check your payment method and try again. If it still fails, contact support and include the booking ID and a screenshot of the error."
- Unable to upload documents: "Check file size/format. Files must be within the configured size limit and a supported format (JPEG/PNG/PDF). If you're on mobile, try a smaller image."
- Booking not visible: "Confirm you are logged in and refresh My Bookings; check email for confirmation. If missing, provide booking reference to support."

Where to go (UI pages and references)
- Browse / listing: `resources/js/Pages/Public/VehiclesPublic.vue` or `resources/js/Pages/Public/VehiclesRefactored.vue`
- Vehicle detail: `resources/js/Pages/Public/VehicleDetail.vue`
- Booking flow: `resources/js/Pages/Booking/SelectPricingTier.vue`, `resources/js/Pages/Booking/Create.vue`, `resources/js/Pages/Booking/Payment.vue`, `resources/js/Pages/Booking/Show.vue`
- My Bookings: `resources/js/Pages/Renters/MyBookings.vue`, `resources/js/Pages/Renters/BookingDetail.vue`
- Upload proof / license: `resources/js/Pages/Renters/UploadProof.vue`, `resources/js/Pages/Profile/Partials/KycVerificationForm.vue`
- Saved vehicles: `resources/js/Pages/Renters/SavedVehicles.vue` or `resources/js/Pages/UserAccount/SavedVehicles.vue`
- Ratings: `resources/js/Pages/Rating/Create.vue`

Sample chatbot reply templates (use for natural variants)
- Booking success: "Your booking is confirmed. Reference: {booking_id}. You can view details at My Bookings." 
- Payment pending: "Payment is pending for booking {booking_id}. Please complete payment within {time_limit} or the reservation may be released." 
- KYC status: "Your KYC is {status}. If rejected, check the rejection reason and re-upload the required documents."

Escalation & contact
- If the question requires admin-level action (refunds, blocking another user, global listing removal), the bot should reply: "I can't perform that action — would you like me to escalate this to support or an Admin?"

Notes for bot implementers
- Provide slots for: {vehicle_id}, {booking_id}, {date_range}, {pricing_tier}, {error_code} so the bot can answer specific queries.
- When unsure, ask clarifying questions (e.g., "Which booking do you mean? Provide booking ID or vehicle name.").

