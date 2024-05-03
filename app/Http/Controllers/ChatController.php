<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Chat\StoreRequest;
use App\Http\Resources\Chat\ChatResource;
use App\Http\Resources\Message\MessageResource;
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
        $chats = ChatResource::collection(auth()->user()->chats()->get())->resolve();

        return inertia('Chat/Index', compact('users', 'chats'));
    }

    public function store(StoreRequest $request): RedirectResponse|JsonResponse
    {
        // TODO: отрефакторить, вынести в сервис
        $data = $request->validated();
        $usersIds = array_merge($data['users'], [auth()->id()]);
        sort($usersIds);
        $usersIdsString = implode('-', $usersIds);

        if (!$data['isGroup'] && Chat::where('users', $usersIdsString)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'A chat with this user already exists'
            ]);
        }

        try {
            DB::beginTransaction();

            $chat = Chat::firstOrCreate([
                'users' => $usersIdsString
            ], [
                'title' => $data['title']
            ]);

            $chat->users()->sync($usersIds);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'The chat was successfully created',
                'chat' => ChatResource::make($chat)->resolve()
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::channel('chat')->error($e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => "Error while chat's creating. Try again"
            ]);
        }
    }

    public function show(Chat $chat): Response|ResponseFactory
    {
        $users = UserResource::collection($chat->users()->get())->resolve();
        $messages = MessageResource::collection($chat->messages()->get())->resolve();
        $chat = ChatResource::make($chat)->resolve();

        return inertia('Chat/Show', compact('chat', 'users', 'messages'));
    }

    public function destroy(Chat $chat): JsonResponse
    {
        try {
            $chat->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'The chat was successfully deleted',
                'chats' => ChatResource::collection(auth()->user()->chats()->get())->resolve()
            ]);
        } catch (\Throwable $e) {
            Log::channel('chat')->error($e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => "Error while chat's deleting. Try again"
            ]);
        }
    }
}
