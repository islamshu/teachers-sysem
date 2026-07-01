<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        if ($user) {
            if ($user->isSchool()) {
                $user->load('schoolProfile');
            } else {
                $user->load('teacherProfile.subject', 'teacherProfile.grades');
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
            ],
            'locale' => session('locale', 'ar'),
            'flash' => [
                'message' => session('message') ?: session('success') ?: session('error'),
                'status' => session('status') ?: (session('success') ? 'success' : (session('error') ? 'error' : null)),
            ],
            'settings' => Setting::getAll(),
        ];
    }
}
