<?php

namespace App\Http\Controllers;

use App\Services\CategoriesService;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    private $categoriesService;

    public function __construct(CategoriesService $categoriesService)
    {
        $this->categoriesService = $categoriesService;
    }

    public function index(Request $request)
    {
        return $this->categoriesService->show($request);

    }

    public function store(Request $request)
    {
        return $this->categoriesService->store($request);
    }


    public function destroy($id)
    {

        return $this->categoriesService->destroy($id);

    }
}
