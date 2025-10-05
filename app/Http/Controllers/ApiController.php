<?php

namespace App\Http\Controllers;


use App\Mail\ContactMail;
use App\Models\Categories;
use App\Models\Contact;
use App\Models\OurWork;
use App\Models\Reviews;
use App\Models\Service;
use App\Models\WorkPhotos;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use function Symfony\Component\Translation\t;


class ApiController extends Controller
{
    public function getContacts(Request $request)
    {
        try {
            $contacts = Contact::select('telegram', 'whatsapp', 'address', 'email', 'phone', 'facebook', 'instagram', 'youtube', 'lat', 'lng')->first();
            return Response::json($contacts);
        } catch (\Exception $e) {
            return Response::json([], 400);
        }

    }

    public function getCategories(Request $request)
    {
        try {
            $categories = Categories::select('id', 'name', 'image_path', 'description')->get();
            return Response::json($categories);
        } catch (\Exception $e) {
            return Response::json([], 400);
        }

    }

    public function getServicesByCategories(Request $request)
    {
        try {
            $categories = new Categories();
            if ($request->filled('category')) {
                $categories = $categories->where('id', $request->get('category'));
            }
            $categories = $categories->with("services")->get();
            /*
             * [
    {
        "id": 2,
        "name": "Choes",
        "image_path": "/storage/categories/XHNlAtqtwnlbXmNB4nnzJrkYc9ni5lRNE2ufcHni.png",
        "description": "<p>sdsdsdsd</p><p>dsds</p><p>ds</p><p>sd</p><p>sd</p>",
        "created_at": "2025-10-02T21:37:50.000000Z",
        "updated_at": "2025-10-02T21:37:50.000000Z",
        "services": [
            {
                "id": 4,
                "category_id": 2,
                "name": "cleaning",
                "price": 1500,
                "created_at": "2025-10-02T21:38:03.000000Z",
                "updated_at": "2025-10-02T21:38:03.000000Z"
            },
            {
                "id": 5,
                "category_id": 2,
                "name": "dsdssdsd",
                "price": 4500,
                "created_at": "2025-10-02T21:38:07.000000Z",
                "updated_at": "2025-10-02T21:38:07.000000Z"
            },
            {
                "id": 6,
                "category_id": 2,
                "name": "sddsfff",
                "price": 7800,
                "created_at": "2025-10-02T21:38:13.000000Z",
                "updated_at": "2025-10-02T21:38:13.000000Z"
            }
        ]
    }
]*/
            return Response::json($categories);
        } catch (\Exception $e) {
            return Response::json([], 400);
        }
    }

    public function getOurWorks(Request $request)
    {
        try {

            $ourWorks = OurWork::select('image_path_left', 'image_path_right')->get();
            return Response::json($ourWorks);
        } catch (\Exception $e) {
            return Response::json([], 400);
        }
    }

    public function getWorkPhotos(Request $request)
    {
        try {

            $workPhotos = WorkPhotos::select('category_id', 'image_path_left', 'image_path_right')->get();
            return Response::json($workPhotos);
        } catch (\Exception $e) {
            return Response::json([], 400);
        }
    }

    public function getWorkPhotosByCategories(Request $request)
    {
        try {
            $categories = new Categories();
            if ($request->filled('category')) {
                $categories = $categories->where('id', $request->get('category'));
            }
            $categories = $categories->with("workPhotos")->get();
            /*
             [
    {
        "id": 2,
        "name": "Choes",
        "image_path": "/storage/categories/XHNlAtqtwnlbXmNB4nnzJrkYc9ni5lRNE2ufcHni.png",
        "description": "<p>sdsdsdsd</p><p>dsds</p><p>ds</p><p>sd</p><p>sd</p>",
        "created_at": "2025-10-02T21:37:50.000000Z",
        "updated_at": "2025-10-02T21:37:50.000000Z",
        "work_photos": [
            {
                "id": 3,
                "category_id": 2,
                "image_path_left": "/storage/workphotos/i42ajvTvb5AMOUh3lGY011vfo6p0JB4GutEOmQc0.png",
                "image_path_right": "/storage/workphotos/8HyLGufvM7CDzdCe5WSzPZzRr0WzyTnDOmvaU6qt.png",
                "created_at": "2025-10-02T22:14:17.000000Z",
                "updated_at": "2025-10-02T22:14:17.000000Z"
            }
        ]
    },
    {
        "id": 4,
        "name": "sdffsdsf",
        "image_path": "/storage/categories/2VY21I1j8qls8UvWRwr98aEMfFnvn0x9PVBgqerX.png",
        "description": "<p>sdffsdfsfd</p>",
        "created_at": "2025-10-02T22:14:09.000000Z",
        "updated_at": "2025-10-02T22:14:09.000000Z",
        "work_photos": [
            {
                "id": 4,
                "category_id": 4,
                "image_path_left": "/storage/workphotos/NCtEAZfXmRf7V8u7EiMxkq3YU2seWhyWXRGaAZjd.png",
                "image_path_right": "/storage/workphotos/BIYQ7EbJmpKT0XN36An39oSP4Cv0UpRX959ecmCU.png",
                "created_at": "2025-10-02T22:16:15.000000Z",
                "updated_at": "2025-10-02T22:16:15.000000Z"
            }
        ]
    }
]*/
            return Response::json($categories);
        } catch (\Exception $e) {
            return Response::json([], 400);
        }
    }

    public function createReview(Request $request)
    {
        $errors = [];

        if (!$request->filled('name')) {
            $errors['name'][] = 'Name is required.';
        } elseif (strlen($request->name) > 255) {
            $errors['name'][] = 'Name must not exceed 255 characters.';
        }

        if (!$request->filled('email')) {
            $errors['email'][] = 'Email is required.';
        } elseif (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Email format is invalid.';
        }

        if (!$request->filled('comment')) {
            $errors['comment'][] = 'Comment is required.';
        } elseif (strlen($request->comment) > 5000) {
            $errors['comment'][] = 'Comment must not exceed 5000 characters.';
        }

        if (!$request->filled('rate')) {
            $errors['rate'][] = 'Rate is required.';
        } elseif (!is_numeric($request->rate)) {
            $errors['rate'][] = 'Rate must be a number.';
        } elseif ($request->rate < 1 || $request->rate > 5) {
            $errors['rate'][] = 'Rate must be between 1 and 5.';
        }

        // return errors if any
        if (!empty($errors)) {
            return Response::json($errors, 422);
        }

        DB::beginTransaction();
        try {
            $reviews = new Reviews();
            $reviews->name = $request->name;
            $reviews->email = $request->email;
            $reviews->comment = $request->comment;
            $reviews->rate = $request->rate;
            $reviews->save();
            DB::commit();
            return Response::json(['isSuccess' => true], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::json([], 400);
        }

    }

    public function getReviews(Request $request)
    {
        try {
            $reviews = Reviews::where('status',true)->get();
            return Response::json($reviews);
        } catch (\Exception $e) {
            return Response::json([], 400);
        }

    }

    public function sendContact(Request $request)
    {
        $errors = [];

        // name is still required
        if (!$request->filled('name')) {
            $errors['name'][] = 'Name is required.';
        }

        // email is optional but if provided, validate format
        if ($request->filled('email') && !filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Invalid email format.';
        }

        // phone is required
        if (!$request->filled('phone')) {
            $errors['phone'][] = 'Phone is required.';
        }

        if (!empty($errors)) {
            return Response::json($errors, 422);
        }

        $data = [
            'name'    => $request->name,
            'email'   => $request->email ?? null,
            'phone'   => $request->phone,
            'comment' => $request->comment ?? null,
            'photo'   => null,
        ];

        // handle optional photo upload
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $path = $request->file('photo')->store('contact_photos', 'public');
            $data['photo'] = "/storage/" . $path;
        }

        try {
            Mail::to('hayk.qocharyan.99@mail.ru')->send(new ContactMail($data));
            return Response::json(['isSuccess' => true], 200);
        } catch (\Exception $e) {
            return Response::json(['isSuccess' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
