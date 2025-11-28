<?php

namespace App\Http\Controllers;

use App\Services\ReviewsService;
use Illuminate\Http\Request;

class ReviewsController extends Controller
{
    private $service;

    public function __construct(ReviewsService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return $this->service->show($request);
    }

    public function toggle($id)
    {
        return $this->service->toggle($id);
    }
}
