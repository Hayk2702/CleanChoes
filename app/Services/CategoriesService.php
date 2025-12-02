<?php

namespace App\Services;

use App\Models\Categories;
use App\Models\Service;
use App\Traits\UploadFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class CategoriesService
{
    use UploadFile;
    public function show($request)
    {
        if ($request->ajax()) {
            $sortOrder = ($request->boolean('sortDesc') ? 'DESC' : 'ASC');
            $sortBy = $request->get('sortBy', 'id');

            $q = Categories::query();

            foreach ((array) $request->get('filter', []) as $index => $filter) {
                $filter = json_decode($filter);
                $cond = $index > 0 ? (json_decode($request->filter[$index - 1])->condition ?? 'AND') : 'AND';
                $action = (isset($filter->action) && in_array($filter->action, ['LIKE', '=', '!=', '>', '<', '>=', '<='])) ? $filter->action : 'LIKE';
                if ($filter && isset($filter->key->value)) {
                    if (isset($filter->text) || $filter->text === 0) {
                        $text = ($action === 'LIKE') ? ('%' . trim($filter->text) . '%') : trim($filter->text);
                        if ($cond === 'AND') {
                            $q->where($filter->key->value, $action, $text);
                        } else {
                            $q->orWhere($filter->key->value, $action, $text);
                        }
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
        $validated = $request->validate([
            'id' => 'nullable|exists:categories,id',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:4096', // file key from formData
        ]);


        DB::beginTransaction();
        try {
            $category = isset($validated['id']) ? Categories::findOrFail($validated['id']) : new Categories();


            $category->name = $validated['name'] ?? $category->name;
            $category->description = $validated['description'] ?? $category->description;


            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('categories', 'public');
                $category->image_path = "/storage/" . $path;

            }

            $category->save();
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
            $category = Categories::find($id);
            if (!$category) {
                return Response::json(['isSuccess' => false, 'message' => __('variable.not_found_error')]);
            }
            if (Service::where("category_id",$id)->first()) {
                return Response::json(['isSuccess' => false, 'message' => __('variable.can_not_delete')]);
            }

            $maybePath = str_replace(Storage::disk('public')->url(''), '', $category->image_path);
            if ($maybePath) {
                if (str_contains($maybePath, "/storage")) {
                    $maybePath = substr($maybePath, strlen("/storage"));
                }
                if (Storage::disk('public')->exists($maybePath)) {
                    Storage::disk('public')->delete($maybePath);
                }
            }

            $category->delete();
            return Response::json(['isSuccess' => true, 'message' => __('variable.deleted')]);

        } catch (\Exception $e) {
            return Response::json(['isSuccess' => false, 'message' => $e->getMessage()], 400);
        }
    }

}
