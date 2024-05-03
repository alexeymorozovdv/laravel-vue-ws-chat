<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Chat\StoreRequest;
use App\Http\Resources\Chat\MessageResource;
use App\Http\Resources\User\UserResource;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Response;
use Inertia\ResponseFactory;

class ChatController extends Controller
{
    public function index(): Response
    {
        $users = UserResource::collection(User::whereNot('id', auth()->id())->get())->resolve();
        $chats = MessageResource::collection(auth()->user()->chats()->has('messages')->get())->resolve();

        return inertia('Chat/Index', compact('users', 'chats'));
    }

    public function store(StoreRequest $request): RedirectResponse|JsonResponse
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

            return redirect()->route('chats.show', $chat->id);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::channel('chat')->error($e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Ошибка при создании чата. Попробуйте ещё раз.'
            ]);
        }
    }

    public function show(Chat $chat): Response|ResponseFactory
    {
        $users = UserResource::collection($chat->users()->get())->resolve();
        $chat = MessageResource::make($chat)->resolve();

        return inertia('Chat/Show', compact('chat', 'users'));
    }
}
