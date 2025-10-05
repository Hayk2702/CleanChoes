<?php

namespace App\Http\Controllers;

use App\Services\WorkPhotoService;
use Illuminate\Http\Request;

class WorkPhotoController extends Controller
{
    private $service;

    public function __construct(WorkPhotoService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return $this->service->show($request);
    }

    public function categories(Request $request)
    {
        return $this->service->categories($request);
    }

    public function store(Request $request)
    {
        return $this->service->store($request);
    }

    public function destroy($id)
    {
        return $this->service->destroy($id);
    }
}
