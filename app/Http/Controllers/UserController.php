<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return View|Factory|JsonResponse|Application
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(Request $request): View|Factory|JsonResponse|Application
    {
        $page = request()->get('page', 1);
        $users = User::paginate(6, ['*'], 'page', $page);

        $page++;
        $hasMorePages = $users->lastPage() >= $page;

        if ($request->ajax()) {
            $vars = compact('users', 'page', 'hasMorePages');
            $view = view('users.more', $vars)->render();
            return response()->json(['html' => $view, 'page' => $page, 'hasMorePages' => $hasMorePages]);
        }

        return view('users.index', compact('users'));
    }
}
