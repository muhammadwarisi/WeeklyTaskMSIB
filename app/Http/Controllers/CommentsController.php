<?php

namespace App\Http\Controllers;

use App\Models\comments;
use App\Models\tasks;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{
    public function createComment(Request $request,string $tasks_id)
    {
        
        // $data = [
        //     'tasks_id' => $tasks_id,
        //     // 'users_id'=> $guest_user_id,
        //     // 'users_id'=> $request->users_id,
        //     // 'users_id'=> User::with('comments')->find('users_id')->get(1),
        //     'users_id' => Auth::id(),
        //     'comment' => $request->comment,
        // ];
        // $comments = comments::create($data);
        // Validasi input
        $request->validate([
            'comment' => 'required|string|max:255',
        ]);

        // Cek apakah task ada
        $task = tasks::find($tasks_id);
        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        // Simpan komentar
        $comment = comments::create([
            'tasks_id' => $tasks_id,
            // 'users_id' => Auth::user()->id, // Ambil ID user yang sedang login
            'users_id' => $request->input('users_id'),
            'comment' => $request->input('comment'),
        ]);
        if ($comment) {
            return response()->json([
                'message' => 'berhasil membuat komen',
                'status'=> 'success',
                'data' => $comment
            ]);
        }
    }
    public function getComment(string $tasks_id)
    {
        $task = Comments::where('tasks_id', $tasks_id)
                                ->find($tasks_id);
        // $comment = comments::where('comment', $tasks_id)->get();
        if ($task) {
        return response()->json([
            'comment' => $task,
            'message' => 'Comment Ditemukan',
            'status'=> 'success'
        ]);
        } else {
            return response()->json(['message'=> 'tidak ada comment'],404);
        }
    }
}
