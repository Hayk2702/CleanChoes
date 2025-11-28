<?php

namespace App\Services;

use App\Models\Categories;
use App\Models\OurWork;
use App\Models\Service;
use App\Traits\UploadFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class OurWorkService
{
    use UploadFile;
    public function show($request)
    {
        if ($request->ajax()) {
            $sortOrder = ($request->boolean('sortDesc') ? 'DESC' : 'ASC');
            $sortBy = $request->get('sortBy', 'id');

            $q = OurWork::query();

            // Optional: same filter shape as your other pages
            foreach ((array) $request->get('filter', []) as $index => $filter) {
                $filter = json_decode($filter);
                $cond = $index > 0 ? (json_decode($request->filter[$index - 1])->condition ?? 'AND') : 'AND';
                $action = (isset($filter->action) && in_array($filter->action, ['LIKE','=','!=','>','<','>=','<='])) ? $filter->action : 'LIKE';
                if ($filter && isset($filter->key->value)) {
                    if (isset($filter->text) || $filter->text === 0) {
                        $text = ($action === 'LIKE') ? ('%' . trim($filter->text) . '%') : trim($filter->text);
                        if ($cond === 'AND') $q->where($filter->key->value, $action, $text);
                        else $q->orWhere($filter->key->value, $action, $text);
                    }
                }
            }

            $data = $q->orderBy($sortBy, $sortOrder)->paginate((int) $request->get('perPage', 10));
            return Response::json($data);
        }
        return view('home');
    }

    public function store($request)
    {
        $request->validate([
            'image_left'  => 'nullable|image|max:4096',
            'image_right' => 'nullable|image|max:4096',
        ]);

        if (!$request->hasFile('image_left') && !$request->hasFile('image_right')) {
            return Response::json(['image_path_left' => ['At least one image is required']], 422);
        }

        DB::beginTransaction();
        try {
            $ourWork = new OurWork();

            if ($request->hasFile('image_left')) {
                $path = $request->file('image_left')->store('ourworks', 'public');
                $ourWork->image_path_left = "/storage/" . $path;

            }

            if ($request->hasFile('image_right')) {
                $path = $request->file('image_right')->store('ourworks', 'public');
                $ourWork->image_path_right = "/storage/" . $path;
            }

            $ourWork->save();

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


            $ourWork = OurWork::find($id);
            if (!$ourWork) {
                return Response::json(['isSuccess' => false, 'message' => __('variable.not_found_error')]);
            }

            foreach (['image_path_left', 'image_path_right'] as $col) {
                if ($ourWork->{$col}) {
                    $maybePath = str_replace(Storage::disk('public')->url(''), '', $ourWork->{$col});
                    if ($maybePath) {
                        if (str_contains($maybePath, "/storage")) {
                            $maybePath = substr($maybePath, strlen("/storage"));
                        }
                        if (Storage::disk('public')->exists($maybePath)) {
                            Storage::disk('public')->delete($maybePath);
                        }
                    }
                }
            }

            $ourWork->delete();
            return Response::json(['isSuccess' => true, 'message' => __('variable.deleted')]);

        } catch (\Exception $e) {
            return Response::json(['isSuccess' => false, 'message' => $e->getMessage()], 400);
        }
    }

}
