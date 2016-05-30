<?php

namespace App\Http\Controllers\Dashboard;

use App\Config;
use App\Http\Controllers\Controller;
use Cache;
use Flash;
use Illuminate\Http\Request;
use Redirect;

class ApplicationController extends Controller
{
    /**
     * Get the terms of service and privacy policy edit form.
     *
     * @return \Illuminate\View\View
     */
    public function tosAndPpView()
    {
        return view('dashboard.tosPp');
    }

    /**
     * Update the terms of service and privacy policy.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function tosAndPp(Request $request)
    {
        $key = 'application-service';

        $application = array_merge(
            Config::getConfig($key),
            $request->only(['terms_of_service', 'privacy_policy'])
        );

        Config::findOrFail($key)->update(['value' => $application]);

        Cache::forget($key);

        Flash::success('更新成功');

        return Redirect::route('dashboard.tos-pp.index');
    }
}
