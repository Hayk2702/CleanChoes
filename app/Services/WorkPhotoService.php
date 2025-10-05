<?php

namespace App\Services;

use App\Models\WorkPhotos;
use App\Models\Categories;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class WorkPhotoService
{
    public function show($request)
    {
        if ($request->ajax()) {
            $sortOrder = ($request->boolean('sortDesc') ? 'DESC' : 'ASC');
            $sortBy = $request->get('sortBy', 'id');

            $q = WorkPhotos::with('category');

            // Optional: support your generic filter shape if present
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

    public function categories($request)
    {
        // lean list for the select
        $list = Categories::select('id','name')->orderBy('name')->get();
        return Response::json(['data' => $list]);
    }

    public function store($request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'image_left'  => 'nullable|image|max:4096',
            'image_right' => 'nullable|image|max:4096',
        ]);

        if (!$request->hasFile('image_left') && !$request->hasFile('image_right')) {
            return Response::json(['image_path_left' => ['At least one image is required']], 422);
        }

        DB::beginTransaction();
        try {
            $wp = new WorkPhotos();
            $wp->category_id = (int) $request->category_id;

            if ($request->hasFile('image_left')) {
                $path = $request->file('image_left')->store('workphotos', 'public');
                $wp->image_path_left = "/storage/" . $path;
            }

            if ($request->hasFile('image_right')) {
                $path = $request->file('image_right')->store('workphotos', 'public');
                $wp->image_path_right = "/storage/" . $path;
            }

            $wp->save();
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
            $wp = WorkPhotos::find($id);
            if (!$wp) {
                return Response::json(['isSuccess' => false, 'message' => __('variable.not_found_error')]);
            }

            foreach (['image_path_left', 'image_path_right'] as $col) {
                if ($wp->{$col}) {
                    $maybePath = str_replace(Storage::disk('public')->url(''), '', $wp->{$col});
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

            $wp->delete();
            return Response::json(['isSuccess' => true, 'message' => __('variable.deleted')]);
        } catch (\Exception $e) {
            return Response::json(['isSuccess' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
