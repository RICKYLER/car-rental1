<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminSystemSettingController extends Controller
{
    public function updateV2g(Request $request): RedirectResponse
    {
        SystemSetting::putValue('v2g_enabled', $request->boolean('enabled'), 'boolean');

        return back()->with('status', 'V2G research mode setting updated.');
    }
}
