<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Helpers\ApiResponse;
use App\Http\Requests\MessageCreateRequest;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 20;
        $page = $request->input('page', 1);

        $total = Message::count();
        $offset = max($total - ($page * $perPage), 0);

        $messages = Message::with('user')
            ->orderBy('created_at', 'asc') // ASC: en eski başta, en yeni sonda
            ->skip($offset)
            ->take($perPage)
            ->get();

        // Laravel LengthAwarePaginator ile paginate benzeri response oluştur
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $messages,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return ApiResponse::success($paginator);
    }


    public function store(MessageCreateRequest $request)
    {
        $message = Message::create([
            'user_id' => auth()->id(),
            'content' => [
                'type' => 'text',
                'text' => $request->content,
            ],
        ]);

        $message->load('user');

        // Broadcast the message
        broadcast(new MessageSent($message));

        return ApiResponse::success($message, 'Mesaj oluşturuldu');
    }

    public function delete(Message $message)
    {
        if (auth()->user()->id != $message->user_id){
            return ApiResponse::accessDenied('Başka Kişiye ait mesajı silemezsiniz.');
        }

        $message->delete();
        return ApiResponse::deleted();
    }
}
