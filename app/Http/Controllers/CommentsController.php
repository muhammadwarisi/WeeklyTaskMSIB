<?php

namespace App\Http\Controllers;

use App\Enums\enums;
use App\Models\User;
use App\Models\tasks;
use App\Models\comments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;
use Symfony\Component\HttpFoundation\Response;

class CommentsController extends Controller
{
    public function createComment(Request $request, string $tasks_id)
    {
        $request->validate([
            'comment' => ['required', 'string', 'max:255'],
        ]);

        // Cek apakah task ada
        $task = tasks::find($tasks_id);
        if (!$task) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Task tidak ditemukan'
            ], 404);
        }

        // Simpan komentar
        $comment = comments::create([
            'tasks_id' => $tasks_id,
            // 'users_id' => Auth::user()->id, // Ambil ID user yang sedang login
            'users_id' => $request->input('users_id'),
            'comment' => $request->input('comment'),
        ]);
        if (!$comment) {
            return response()->json([
                'status' => 'failed',
                'message' => 'gagal membuat komen',
            ], Response::HTTP_BAD_REQUEST);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'berhasil membuat comment',
                'data' => $comment
            ], Response::HTTP_CREATED);
        }
    }
    public function getCommentByKeyword(Request $request, string $tasks_id)
    {
        // Query dasar mengambil komentar berdasarkan tasks_id
        $query = Comments::where('tasks_id', $tasks_id);

        // Ambil keyword dari request jika tersedia
        $keyword = $request->keyword;

        // Jika keyword ada, tambahkan filter pencarian
        if ($keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->where('users_id', 'LIKE', "%$keyword%")
                    ->orWhere('comment', 'LIKE', "%$keyword%")
                    ->orWhereHas('user', function ($query) use ($keyword) {
                        $query->where('username', 'LIKE', "%$keyword%")
                              ->orWhere('firstname', 'LIKE', "%$keyword%")
                              ->orWhere('lastname', 'LIKE', "%$keyword%")
                              ->orWhere('firstname', 'LIKE', "%$keyword%")
                              ->orwhere(DB::raw("CONCAT(firstname, ' ', lastname)"), 'LIKE', "%$keyword%");
                    });
            });
        }

        // Ambil hasil query
        $task = $query->get();

        // Cek apakah ada data yang ditemukan
        if ($task->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Comment tidak ditemukan',
            ], Response::HTTP_NOT_FOUND);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Comment ditemukan',
                'data' => $task
            ], Response::HTTP_OK);
        }
    }

    public function getCommentByField(Request $request, string $tasks_id)
    {
        $task = tasks::find($tasks_id)->first();
        if ($tasks_id != $task->id) {
            return response()->json([
                'success' => false,
                'message' => 'Task Tidak Ditemukan',
            ], Response::HTTP_NOT_FOUND);
        }
        // Ambil parameter pencarian dari request
        $userId = $request->input('user_id');
        $username = $request->input('username');
        $comment = $request->input('comment');
        $name = $request->input('name');

        // Query dasar untuk mengambil comments berdasarkan tasks_id
        $query = Comments::where("tasks_id", $tasks_id);

        // Tambahkan filter jika parameter ada
        if ($userId) {
            $query->where('users_id', $userId);
        }

        if ($username) {
            $query->whereHas('user', function ($query) use ($username) {
                $query->where('username', 'LIKE', "%$username%");
            });
        }

        if ($comment) {
            $query->where('comment', 'LIKE', "%$comment%");
        }

        if ($name) {
            // Gabungkan firstname dan lastname sebagai alias 'nama'
            $query->whereHas('user', function ($query) use ($name) {
                $query->where(DB::raw("CONCAT(firstname, ' ', lastname)"), 'LIKE', "%$name%");
            });
        }

        // Ambil hasil query
        $comments = $query->get();

        // Cek apakah ada data yang ditemukan
        if ($comments->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Comment Tidak Ditemukan',
            ], Response::HTTP_NOT_FOUND);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Comment Ditemukan',
                'data' => $comments,
            ], Response::HTTP_OK);
        }
    }
}
