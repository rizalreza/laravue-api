<?php

namespace App\Http\Controllers;

use App\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardController extends Controller
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

    public function index()
    {
    	return Auth::user()->boards;
    }

    public function show($id)
    {
    	$board=Board::findOrFail($id);

    	if(Auth::user()->id !== $board->user_id) {
    		return response()->json([
    			'status' => 'Error',
    			'message' => 'Unauthorized, content not found or owned by other user'
    		], 401);
    	}

    	return $board;
    }

    public function store(Request $request)
    {
    	$board = Auth::user()->boards()->create([
    		'name' => $request->name,
    	]);

    	return response()->json([
    		'message'=>'Success',
    		'board' => $board


    	], 200);
    }

    public function update(Request $request, $id)
    {
    	$this->validate($request, ['name' => 'required']);

    	$board = Board::find($id);

    	if (Auth::user()->id !== $board->user_id) {
    		return response()->json([
    			'status' => 'Error',
    			'message' => 'Unauthorized, content not found or owned by other user'
    		], 401);
    	}

    	$board->update($request->all());

    	return response()->json([
    		'message' => 'Success',
    		'board' => $board
    	], 200);
    }

    public function destroy($id)
    {
    	$board = Board::find($id);

    	if(Auth::user()->id !== $board->user_id) {
    		return response()->json([
    			'status' => 'Unauthorized, content not found or owned by other user'
    		], 401);
    	}

    	if(Board::destroy($id)) {
    		return response()->json([
    			'status'=> 'Success',
    			'message' => 'Board deleted successfully'
    		], 200);
    	}

    	return response()->json([
    		'status' => 'Error',
    		'message' => 'Something went wrong'
    	], 401);
    }
}
