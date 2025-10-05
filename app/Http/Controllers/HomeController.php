<?php

namespace App\Http\Controllers;

use App\Services\HomeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
class HomeController extends Controller
{
    private $homeService;

    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
        $this->middleware('auth');
    }
    public function index()
    {
        return view('home');
    }
    public function abort403()
    {
        return abort(403,__('variable.abort_403'));
    }
    public function setLocale($lang)
    {
        return $this->homeService->setLocale($lang);
    }


}
