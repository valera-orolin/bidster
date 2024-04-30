<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    /**
     * Store a newly created message in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Auction  $auction
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Auction $auction): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|min:1|max:500',
        ]);

        $message = new Message();
        $message->auction()->associate($auction);
        $message->user()->associate($request->user());
        $message->content = $validated['content'];
        $message->save();

        $message->is_current = $message->user->id == auth()->user()->id;
        $message->is_seller = $message->user->id == $auction->seller->id;
        $message->name = $message->user->name;
        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }
}
