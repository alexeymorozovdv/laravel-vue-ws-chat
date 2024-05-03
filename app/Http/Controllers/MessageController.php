<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\StoreRequest;
use App\Http\Resources\Message\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use App\Models\MessageStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function store(StoreRequest $request): array|JsonResponse
    {
        try {
            $data = $request->validated();

            DB::beginTransaction();

            $message = Message::create([
                'body' => $data['body'],
                'chat_id' => $data['chat_id'],
                'user_id' => auth()->id(),
            ]);

            foreach ($data['user_ids'] as $userId) {
                MessageStatus::create([
                    'chat_id' => $data['chat_id'],
                    'message_id' => $message->id,
                    'user_id' => $userId,
                ]);
            }

            DB::commit();

            return MessageResource::make($message)->resolve();
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::channel('chat')->error($e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Ошибка при отправке сообщения.Попробуйте ещё раз'
            ]);
        }
    }
}
