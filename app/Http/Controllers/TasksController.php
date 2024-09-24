<?php

namespace App\Http\Controllers;

use App\Http\Resources\TasksResource;
use App\Models\tasks;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;



class TasksController extends Controller
{
    public function getTasks(string $users_id)
    {
        $tasks = tasks::where("users_id", '=' ,$users_id)->get();
        if (!$tasks) {
            return response()->json([
                'status'=> 'failed',
                'message' => 'Tidak Ada Tasks'
            ],400);
        } else {
            return response()->json([
                'status'=> 'success',
                'message' => 'Task Ditemukan',
                'data'=> $tasks
            ], 200);
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
        if ($tasks){
            return response()->json([
                'status' => 'success',
                'message'=> 'Berhasil Membuat Data',
                'data' => $tasks,
            ],200);
        } else {
            return response()->json([
                'status'=>'false',
                'message'=> 'Gagal membuat tasks'
            ], Response::HTTP_BAD_REQUEST);
        }
        
    }
    public function updateTasks(Request $request, string $tasks_id)
    {
        $request->validate([
            'title' => ['required'],
            'description' => ['required'],
            'status' => ['required'],
        ]);
        $data = [
            'title'=> $request->input('title'),
            'description'=> $request->input('description'),
            'status'=> $request->input('status'),
        ];
        $tasks = tasks::where('id', $tasks_id)->update($data);
        if ($tasks) {
            return response()->json([
                'status' => 'success',
                'message' => 'Task Berhasil DiUpdate',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Tasks Gagal DiUpdate',
                'status' => 'failed'
            ],400);
        }
    }
    public function deleteTasks(string $tasks_id)
    {
        // Temukan task berdasarkan ID
        $tasks = tasks::find($tasks_id);
        if ($tasks) {
            // Hapus task
            $tasks->delete();
            return response()->json([
                'status'=> 'success',
                'message' => 'Task Berhasil Dihapus'
            ], 200);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Task tidak ditemukan'
            ], 404);
        }
    }
}
