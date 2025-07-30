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
                'gcash_qr' => 'required|image|mimes:jpeg,png|max:2048',
            ]);
            $user = $request->user();
            // Remove old QR if exists
            if ($user->gcash_qr) {
                Storage::disk('public')->delete($user->gcash_qr);
            }
            $path = $request->file('gcash_qr')->store('gcash_qr_codes', 'public');
            $user->gcash_qr = $path;
            $user->save();

            $response = [
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
                return response()->json(['message' => 'Validation failed', 'errors' => $ve->errors()], 422);
            }
            return redirect()->back()->withErrors($ve->errors());
        } catch (\Exception $e) {
            // Log all other errors
            Log::error('GCash QR upload failed: ' . $e->getMessage(), ['exception' => $e]);
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Upload failed: ' . $e->getMessage()], 500);
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
