<?php

namespace App\Http\Controllers;

use App\Exports\UserDataExport;
use App\Http\Requests\UserShowRequest;
use App\Http\Requests\UserStoreRequest;
use App\Models\User;
use App\Services\ContactService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ContactController extends Controller
{
    private $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function index(Request $request)
    {
        return $this->contactService->show($request);

    }

    public function store(Request $request)
    {
        return $this->contactService->store($request);
    }

}
