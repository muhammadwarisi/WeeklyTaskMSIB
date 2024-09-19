<?php

namespace App\Http\Controllers;

use App\Models\tasks;
use App\Models\User;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    public function getTasks(string $id)
    {
        $tasks = tasks::find($id);
        if ($tasks) {
            return response()->json([
                $tasks,
                'message' => 'Task Ditemukan'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Tidak Ada Tasks'
            ]);
        }
    }

    public function createTasks(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string'],
            'description' => ['required', 'string', 'max:100'],
            'status' => ['required'],
        ]);

        $tasks = tasks::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'users_id' => $request->users_id,
            // 'users_id' => auth()->user()->id,
            // 'users_id'=> User::with('tasks')
            //                     ->where('id', $request->users_id)
            //                     ->get(),
        ]);
        return response()->json([
            'status' => 'success',
            'message'=> 'Berhasil Membuat Data',
            'data' => $tasks,
        ]);
    }
    public function updateTasks(Request $request, string $id)
    {
        $request->validate([
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'status' => ['required'],
        ]);
        $data = $request->all();
        $tasks = tasks::where('id', $id)->update($data);
        if ($tasks) {
            return response()->json([
                'status' => 'success',
                'message' => 'Task Berhasil DiUpdate',
                'data' => $tasks,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Tasks Gagal DiUpdate',
                'status' => 'failed'
            ],400);
        }
    }
    public function deleteTasks(string $id)
    {
        // Temukan task berdasarkan ID
        $tasks = tasks::find($id);
        if ($tasks) {
            // Hapus task
            $tasks->delete();
            return response()->json([
                'message' => 'Task Berhasil Dihapus'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Task tidak ditemukan'
            ], 404);
        }
    }
}
