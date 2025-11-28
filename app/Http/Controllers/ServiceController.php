<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Services\CategoriesService;
use App\Services\ServiceService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    private $serviceService;

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    public function index(Request $request, Categories $category)
    {
        return $this->serviceService->show($request,$category);

    }

    public function store(Request $request)
    {
        return $this->serviceService->store($request);
    }


    public function destroy($id)
    {

        return $this->serviceService->destroy($id);

    }
}
