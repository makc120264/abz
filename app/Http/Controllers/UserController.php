<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UserController extends Controller
{

    public static function getToken()
    {
        $apiUrl = env('API_URL');
        $response = Http::get($apiUrl . '/token');

        if ($response->successful()) {
            return $response->json() ?? [];
        }

        return [];
    }

    /**
     * @param Request $request
     * @return View|Factory|JsonResponse|Application
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(Request $request): View|Factory|JsonResponse|Application
    {
        $page = request()->get('page', 1);
        $count = request()->get('count', 6);

        $users = User::paginate($count, ['*'], 'page', $page);

        $page++;
        $hasMorePages = $users->lastPage() >= $page;

        if ($request->ajax()) {
            $view = view('users.more', compact('users'))->render();
            return response()->json(['html' => $view, 'page' => $page, 'hasMorePages' => $hasMorePages]);
        }

        if ($request->is('api/*')) {
            $userId = $request->route('id');
            if (!empty($userId)) {
                $user = User::find($userId);
                $response = $this->transformUserData($user);
            } else {
                $response = $this->transform($users);
            }
            return response()->json([$response]);
        } else {
            return view('users.index', compact('users'));
        }
    }

    /**
     * @param $user
     * @return array
     */
    private function transformUserData($user): array
    {
        return [
            "success"=> true,
            "name"=> $user->getAttribute('name'),
            "email"=> $user->getAttribute('email'),
            "phone"=> $user->getAttribute('phone'),
            "position_id"=> $user->getAttribute('position_id'),
            "position"=> $this->getPositionNameById($user->getAttribute('position_id')),
            "photo"=> App::make('url')->to('/') . Storage::url($user->getAttribute('photo'))
        ];
    }

    /**
     * @param $users
     * @return array
     */
    private function transform($users): array
    {
        $usersData = [];
        $items = $users->toArray();
        $response = [
            "success" => true,
            "total_pages" => $items["last_page"],
            "total_users" => $items["total"],
            "count" => $items["per_page"],
            "page" => $items["current_page"],
            "links" => [
                "next_url" => $items["next_page_url"],
                "prev_url" => $items["prev_page_url"]
            ]
        ];

        foreach ($items["data"] as $item) {
            $usersData[] = [
                "id" => $item["id"],
                "name" => $item["name"],
                "email" => $item["email"],
                "phone" => $item["phone"],
                "position" => $this->getPositionNameById($item["position_id"]),
                "position_id" => $item["position_id"],
                "registration_timestamp" => strtotime($item["created_at"]),
                "photo" => App::make('url')->to('/') . Storage::url($item["photo"])
            ];
        }
        $response["users"] = $usersData;

        return $response;
    }

    /**
     * @param $positionId
     * @return mixed
     */
    private function getPositionNameById($positionId): mixed
    {
        $positionName = '';
        $positions = Position::all();
        foreach ($positions as $position) {
            if ($position['id'] == $positionId) {
                $positionName = $position['name'];
                break;
            }
        }

        return $positionName;
    }
}
