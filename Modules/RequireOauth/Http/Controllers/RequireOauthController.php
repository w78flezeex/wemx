<?php

namespace Modules\RequireOauth\Http\Controllers;

use App\Facades\AdminTheme;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class RequireOauthController extends Controller
{
    private array $drivers = ['google', 'discord', 'github'];
    public function index()
    {
        return view(AdminTheme::moduleView('requireoauth', 'index'), ['drivers' => $this->drivers]);
    }

    public function store(Request $request)
    {
        foreach ($this->drivers as $driver) {
                $oauth = $request->boolean('oauth::'.$driver);
                $data = array_merge(json_decode(Settings::get('oauth::'.$driver, '{}'), true), ['require' => $oauth]);
                Settings::put('oauth::' . $driver, json_encode($data));
        }
        return redirect()->back()->with('success', __('responses.settings_store_success'));
    }

}
