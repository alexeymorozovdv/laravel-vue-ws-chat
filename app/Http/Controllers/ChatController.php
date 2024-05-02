<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\User\UserResource;
use App\Models\User;
use Inertia\Response;

class ChatController extends Controller
{
    public function index(): Response
    {
        $users = UserResource::collection(User::all())->resolve();

        return inertia('Chat/Index', compact('users'));
    }
}
