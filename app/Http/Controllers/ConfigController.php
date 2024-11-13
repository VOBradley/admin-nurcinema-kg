<?php

namespace App\Http\Controllers;

use App\Models\VSConfig;
use App\ResponseService;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function getConfig(Request $request)
    {

        $configSlug = $request->get('config_slug');
        $config  = VSConfig::whereSlug($configSlug)->first();
        return ResponseService::success($config?->value);
    }

    public function changeConfig(Request $request)
    {
        $slug = $request->input('slug');
        $value = $request->input('value');
        $config = VSConfig::whereSlug($slug)->first();
        $config->value = $value;
        $config->save();
        return redirect(route('dashboard', absolute: false));
    }
}
