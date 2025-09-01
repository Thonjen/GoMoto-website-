<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\KycVerificationLog;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();
        
        // Include driver's license images if they exist
        $userData = $user->toArray();
        if ($user->drivers_license_front) {
            $userData['drivers_license_front_url'] = Storage::url($user->drivers_license_front);
        }
        if ($user->drivers_license_back) {
            $userData['drivers_license_back_url'] = Storage::url($user->drivers_license_back);
        }
        
        // Add profile picture URL
        $userData['profile_picture_url'] = $user->profile_picture_url;
        
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => $userData,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();
        
        Log::info('Profile update data:', $data);
        Log::info('Has file:', ['has_file' => $request->hasFile('profile_picture')]);
        
        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if it exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            
            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $data['profile_picture'] = $path;
            
            Log::info('Profile picture stored at:', ['path' => $path]);
        }
        
        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
        
        Log::info('User updated:', ['user_id' => $user->id, 'profile_picture' => $user->profile_picture]);

        return Redirect::route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Submit KYC documents for verification
     */
    public function submitKyc(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Admin users don't need KYC verification
        if ($user->role && $user->role->name === 'admin') {
            return Redirect::route('profile.edit')
                ->with('info', 'Admin users do not require KYC verification.');
        }
        
        // Check if user is banned or suspended
        if (in_array($user->status, ['banned', 'suspended'])) {
            return Redirect::route('profile.edit')
                ->withErrors(['kyc' => 'You cannot submit KYC documents while your account is ' . $user->status . '. Please contact support.']);
        }
        
        $request->validate([
            'drivers_license_front' => 'required_without:drivers_license_back|image|mimes:jpeg,png,jpg|max:2048',
            'drivers_license_back' => 'required_without:drivers_license_front|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $oldStatus = $user->kyc_status;
        
        // Handle file uploads
        $updates = [];
        
        if ($request->hasFile('drivers_license_front')) {
            // Delete old file if exists
            if ($user->drivers_license_front) {
                Storage::disk('public')->delete($user->drivers_license_front);
            }
            
            $frontPath = $request->file('drivers_license_front')->store('kyc/drivers_licenses', 'public');
            $updates['drivers_license_front'] = $frontPath;
        }

        if ($request->hasFile('drivers_license_back')) {
            // Delete old file if exists
            if ($user->drivers_license_back) {
                Storage::disk('public')->delete($user->drivers_license_back);
            }
            
            $backPath = $request->file('drivers_license_back')->store('kyc/drivers_licenses', 'public');
            $updates['drivers_license_back'] = $backPath;
        }

        // Set status and timestamp if documents uploaded
        if ($request->hasFile('drivers_license_front') || $request->hasFile('drivers_license_back')) {
            $updates['kyc_status'] = 'under_review';
            $updates['license_submitted_at'] = now();
            $updates['kyc_rejection_reason'] = null;
        }

        $user->update($updates);

        // Log the submission
        KycVerificationLog::create([
            'user_id' => $user->id,
            'admin_id' => $user->id, // Self-submission
            'action' => $oldStatus === 'rejected' ? 'resubmitted' : 'submitted',
            'old_status' => $oldStatus,
            'new_status' => $user->kyc_status,
            'documents_checked' => [
                'drivers_license_front' => $request->hasFile('drivers_license_front'),
                'drivers_license_back' => $request->hasFile('drivers_license_back'),
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return Redirect::route('profile.edit')->with('success', 'KYC documents submitted successfully. Your account will be reviewed within 24-48 hours.');
    }

    /**
     * Delete the user's profile picture
     */
    public function deleteProfilePicture(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
            $user->update(['profile_picture' => null]);
        }
        
        return Redirect::route('profile.edit')->with('success', 'Profile picture deleted successfully.');
    }
}
