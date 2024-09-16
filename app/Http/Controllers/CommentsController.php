<?php

namespace App\Http\Controllers;

use App\Models\comments;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    public function createComment(Request $request,string $tasks_id)
    {
        $data = [
            'tasks_id' => $tasks_id,
            // 'users_id'=> $guest_user_id,
            // 'users_id'=> $request->users_id,
            // 'users_id' => auth()->user()->id,
            'comment' => $request->comment,
        ];
        $comments = comments::create($data);
        if ($comments) {
            return response()->json([
                'message' => 'berhasil membuat komen',
                'status'=> 'success',
            ]);
        }
    }
    public function getComment(string $tasks_id)
    {
        $comments = Comments::where('tasks_id', $tasks_id)->get();
        return response()->json([
            $comments,
            'message' => 'Comment Ditemukan',
        ]);
    }
}
