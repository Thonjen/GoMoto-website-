<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => fn () => $request->user()
                    ? [
                        'id' => $request->user()->id,
                        'first_name' => $request->user()->first_name,
                        'last_name' => $request->user()->last_name,
                        'name' => $request->user()->first_name . ' ' . $request->user()->last_name,
                        'email' => $request->user()->email,
                        'role' => $request->user()->role ? $request->user()->role->name : null,
                        'kyc_status' => $request->user()->kyc_status,
                        'drivers_license_front' => $request->user()->drivers_license_front,
                        'drivers_license_back' => $request->user()->drivers_license_back,
                        'can_book' => $request->user()->can_book,
                        'can_list_vehicles' => $request->user()->can_list_vehicles,
                        'status' => $request->user()->status,
                    ]
                    : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ]);
    }
}
