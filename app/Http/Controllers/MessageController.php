<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::orderBy('created_at','desc')->paginate();
        return ApiResponse::success($messages);
    }
    public function store()
    {
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
