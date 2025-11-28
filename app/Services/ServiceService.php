<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ServiceService
{
    public function show($request,$category)
    {
        if ($request->ajax()) {
            $services = $category->services()->orderBy('id', 'DESC')->get();
            return Response::json(['data' => $services]);
        }
        return view('home');
    }

    public function store($request)
    {
        $validated = $request->validate([
            'id' => 'nullable|exists:services,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
        ]);


        DB::beginTransaction();
        try {
            $service = isset($validated['id']) ? Service::findOrFail($validated['id']) : new Service();
            $service->category_id = $validated['category_id'];
            $service->name = $validated['name'];
            $service->price = $validated['price'];
            $service->save();


            DB::commit();
            return Response::json(['isSuccess' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::json(['message' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $service = Service::find($id);
            if (!$service) {
                return Response::json(['isSuccess' => false, 'message' => __('variable.not_found_error')]);
            }

            $service->delete();
            return Response::json(['isSuccess' => true, 'message' => __('variable.deleted')]);

        } catch (\Exception $e) {
            return Response::json(['isSuccess' => false, 'message' => $e->getMessage()], 400);
        }
    }

}
