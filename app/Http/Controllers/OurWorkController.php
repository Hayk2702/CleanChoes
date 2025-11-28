<?php

namespace App\Http\Controllers;

use App\Models\OurWork;
use App\Services\CategoriesService;
use App\Services\OurWorkService;
use Illuminate\Http\Request;

class OurWorkController extends Controller
{
    private $ourWorkService;

    public function __construct(OurWorkService $ourWorkService)
    {
        $this->ourWorkService = $ourWorkService;
    }

    public function index(Request $request)
    {
        return $this->ourWorkService->show($request);

    }

    public function store(Request $request)
    {
        return $this->ourWorkService->store($request);
    }


    public function destroy($id)
    {

        return $this->ourWorkService->destroy($id);

    }
}
