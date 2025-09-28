<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class OwnerGcashQrController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        return Inertia::render('Owner/UploadGcashQr', [
            'gcashQrUrl' => $user->gcash_qr ? Storage::url($user->gcash_qr) : '',
            'gcashQrUploadDate' => $user->gcash_qr ? $user->updated_at->toDateString() : '',
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'gcash_qr' => 'required|image|mimes:jpeg,png|max:5120', // Increased to 5MB
            ]);
            
            $user = $request->user();
            
            // Remove old QR if exists
            if ($user->gcash_qr) {
                Storage::disk('public')->delete($user->gcash_qr);
            }
            
            // Store the image without compression to preserve QR code quality
            $file = $request->file('gcash_qr');
            $filename = 'gcash_qr_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('gcash_qr_codes', $filename, 'public');
            
            $user->gcash_qr = $path;
            
            // Automatically allow user to accept GCash payments once QR is uploaded
            if (!$user->accepts_gcash) {
                $user->accepts_gcash = false; // Keep it false by default, user can enable later
            }
            
            $user->save();

            $response = [
                'success' => true,
                'message' => 'GCash QR code uploaded successfully!',
                'gcashQrUrl' => Storage::url($path),
                'gcashQrUploadDate' => $user->updated_at->toDateString(),
            ];

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json($response);
            }
            
            return redirect()->route('owner.gcash-qr.show')->with($response);
            
        } catch (\Illuminate\Validation\ValidationException $ve) {
            // Log validation errors
            Log::error('GCash QR validation failed', [
                'errors' => $ve->errors(),
                'message' => $ve->getMessage(),
            ]);
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed', 
                    'errors' => $ve->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($ve->errors());
            
        } catch (\Exception $e) {
            // Log all other errors
            Log::error('GCash QR upload failed: ' . $e->getMessage(), ['exception' => $e]);
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Upload failed: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withErrors(['gcash_qr' => 'Upload failed: ' . $e->getMessage()]);
        }
    }


public function destroy(Request $request)
{
    $user = $request->user();

    if ($user->gcash_qr) {
        Storage::disk('public')->delete($user->gcash_qr);
        $user->gcash_qr = null;
        // Automatically disable GCash payments when QR is removed
        $user->accepts_gcash = false;
        $user->save();
    }

    $response = [
        'gcashQrUrl' => '',
        'gcashQrUploadDate' => '',
    ];

    // 👇 Detect if this is an XHR or normal request
    if ($request->header('X-Inertia')) {
        // 👇 Return a full Inertia render instead of JSON
        return Inertia::render('Owner/UploadGcashQr', $response);
    }

    // 👇 Else return JSON (pure AJAX case)
    return response()->json($response);
}


}
