<?php


namespace App\Services;


use App\Models\Branch;
use App\Models\Contact;
use App\Models\Roles;
use App\Models\User;
use App\Models\UserPermission;
use App\Models\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class ContactService
{
    public function show($request)
    {

        if ($request->ajax()) {
            $sortOrder = (($request->has('sortDesc') and $request->sortDesc == 'true') ? 'DESC' : 'ASC');
            $sortBy = (($request->has('sortBy') and $request->sortBy != '') ? $request->sortBy : 'id');

            $contact = new Contact();

            $contact = Contact::first();
            if (!$contact) {
                $contact = new Contact();
                $contact->save();
            }
            $filterArray = $request->filter;
            foreach ($filterArray as $index => $filter) {
                $filter = json_decode($filter);
                $cond = "AND";
                if ($index > 0) {
                    $cond = json_decode($filterArray[$index - 1])->condition ?? "AND";
                }
                if (property_exists($filter, 'action') and in_array($filter->action, ['LIKE', '=', '!=', '>', '<', '>=', '<='])) {
                    $action = $filter->action;
                } else {
                    $action = "LIKE";
                }
                if ($filter and $filter->key and $filter->key->value) {
                    if ($filter->text or $filter->text == 0) {
                        if ($action == "LIKE") {
                            $text = '%' . trim($filter->text) . '%';
                        } else {
                            $text = trim($filter->text);
                        }
                        if ($cond == "AND") {
                            $contact = $contact->where($filter->key->value, $action, $text);
                        } else {
                            $contact = $contact->orWhere($filter->key->value, $action, $text);
                        }
                    }
                }
            }

            $contact = $contact->orderBy($sortBy, $sortOrder)->paginate($request->perPage);
            return Response::json($contact);
        }
        return view('home');
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();
            $contact = Contact::first();
            if (!$contact) {
                $contact = new Contact();
            }


            $contact->telegram = $request->telegram;
            $contact->whatsapp = $request->whatsapp;
            $contact->address = $request->address;
            $contact->email = $request->email;
            $contact->phone = $request->phone;
            $contact->facebook = $request->facebook;
            $contact->instagram = $request->instagram;
            $contact->youtube = $request->youtube;
            $contact->lat = $request->lat;
            $contact->lng = $request->lng;

            $contact->save();

            DB::commit();
            return Response::json(['isSuccess' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::json(["message" => $e->getMessage()], 400);
        }
    }


}
