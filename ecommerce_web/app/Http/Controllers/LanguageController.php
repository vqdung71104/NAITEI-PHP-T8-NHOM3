<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function changeLanguage(Request $request) {
        $lang = (string) $request->input('lang', 'en');
        Session::put('lang', $lang);
        if (! in_array($lang, ['en','vi'], true)) {
            abort(422, 'Unsupported language');
        }

        session(['lang' => $lang]);
        app()->setLocale($lang);

        return response()->noContent();
    }

    public function changeLanguageBlade(Request $request, $language ) {
        Session::put('lang',$language);

        return redirect()->back();
    }
}