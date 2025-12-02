<?php

namespace App\Services;

use App\Models\Reviews;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class ReviewsService
{
    public function show($request)
    {
        if ($request->ajax()) {
            $sortOrder = ($request->boolean('sortDesc') ? 'DESC' : 'ASC');
            $sortBy = $request->get('sortBy', 'id');

            // enforce id DESC by default
            if (!$request->has('sortBy')) {
                $sortBy = 'id';
                $sortOrder = 'DESC';
            }

            $q = Reviews::query();

            // (Optional) support the generic filter shape like your other pages
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

    public function toggle($id)
    {
        try {
            $review = Reviews::find($id);
            if (!$review) {
                return Response::json(['isSuccess' => false, 'message' => __('variable.not_found_error')], 404);
            }

            $review->status = !$review->status; // flip
            $review->save();

            return Response::json(['isSuccess' => true, 'status' => $review->status]);
        } catch (\Exception $e) {
            return Response::json(['isSuccess' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
