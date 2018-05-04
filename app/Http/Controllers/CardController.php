<?php

namespace App\Http\Controllers;

use App\Board;
use App\Lists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($boardId, $listId)
    {
        $board=Board::find($boardId);

          if (Auth::user()->id !== $board->user_id) {
            return response()->json([
            	'status' => 'Error', 
            	'message' => 'Unauthorized, content not found or owned by other user'
            ], 401);
        }

        $list = $board->lists()->find($listId);
        return response()->json(['cards'=>$list->cards]);
    }

    public function show($boardId, $listId, $cardId)
    {
        $board=Board::find($boardId);

          if (Auth::user()->id !== $board->user_id) {
            return response()->json([
            	'status' => 'Error', 
            	'message' => 'Unauthorized, content not found or owned by other user'
            ], 401);
        }

        $list = $board->lists()->find($listId);
        $card = $list->cards()->find($cardId);

        return response()->json([
        	'status'=>'success',
        	'card'=>$card
        ], 200);
    }

    public function store(Request $request, $boardId, $listId)
    {
        $this->validate($request,['name'=>'required']);

        $board=Board::find($boardId);

        if (Auth::user()->id !== $board->user_id) {
            return response()->json([
            	'status' => 'Error', 
            	'message' => 'Unauthorized, content not found or owned by other user'
            ], 401);
        }

        $card = $board->lists()->find($listId)->cards()->create([
            'name' => $request->name,

        ]);

        return response()->json([
          'message' => 'Success',
          'card' => $card
        ], 200);
    } 

    public function update(Request $request, $cardId)
    {
        $this->validate($request,['name'=>'required']);

        $card = Card::find($cardId);

        if (Auth::user()->id !== $card->list->board->user_id) {
            return response()->json([
            	'status' => 'Error', 
            	'message' => 'Unauthorized, content not found or owned by other user'
            ], 401);
        }
        
        $card->update($request->all());

        return response()->json([
        	'message' => 'Success', 
        	'card' => $card
        ], 200);
    }

    public function destroy($cardId)
    {
        $card = Card::find($cardId);

        if (Auth::user()->id !== $card->list->board->user_id) {
            return response()->json([
              'status' => 'Error', 
              'message' => 'Unauthorized, content not found or owned by other user'
            ], 401);
        }


        if ($card->delete()) {
            return response()->json([
            	'status' => 'Success', 
            	'message' => 'Card Deleted Successfully'
            ], 201);
        }

        return response()->json([
        	'status' => 'Error', 
        	'message' => 'Something went wrong'
        ]);
    }
}