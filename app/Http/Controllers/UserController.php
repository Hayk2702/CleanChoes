<?php

namespace App\Http\Controllers;

use App\Exports\UserDataExport;
use App\Http\Requests\UserShowRequest;
use App\Http\Requests\UserStoreRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        return $this->userService->show($request);

    }

    public function store(UserStoreRequest $request)
    {
        return $this->userService->store($request);
    }


    public function destroy($id)
    {

        return $this->userService->destroy($id);

    }
}
