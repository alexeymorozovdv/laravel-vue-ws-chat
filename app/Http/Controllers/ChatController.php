<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Chat\StoreRequest;
use App\Http\Resources\Chat\ChatResource;
use App\Http\Resources\User\UserResource;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Middleware;
use Inertia\Response;
use Monolog\Logger;

class ChatController extends Controller
{
    public function index(): Response
    {
        $users = UserResource::collection(User::whereNot('id', auth()->id())->get())->resolve();

        return inertia('Chat/Index', compact('users'));
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $usersIds = array_merge($data['users'], [auth()->id()]);
        sort($usersIds);
        $usersIdsString = implode('-', $usersIds);

        try {
            DB::beginTransaction();

            $chat = Chat::firstOrCreate([
                'users' => $usersIdsString
            ], [
                'title' => $data['title']
            ]);

            $chat->users()->sync($usersIds);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::channel('chat')->error($e->getMessage());
        }

        $chat = ChatResource::make($chat)->resolve();

        return inertia('Chat/Show', compact('chat'));
    }
}
