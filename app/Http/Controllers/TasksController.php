<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Rules\enums;
use App\Models\tasks;
use Illuminate\Http\Request;
use App\Rules\Enums as RulesEnums;
use App\Http\Resources\TasksResource;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;



class TasksController extends Controller
{
    public function getTasksByUserId(Request $request,string $users_id)
    {
        // Ambil parameter pencarian dari request
        $title = $request->input('title');
        $description = $request->input('description');
        $status = $request->input('status');
        $startDate = $request->input('start_date'); // Tanggal mulai
        $endDate = $request->input('end_date'); // Tanggal akhir
        $created_at = $request->input('created_at'); // Tanggal dibuat

        // Query dasar untuk mengambil tasks berdasarkan users_id
        $query = tasks::where("users_id", $users_id);

        // Tambahkan filter jika parameter ada
        if ($title) {
            $query->where('title', 'LIKE', "%$title%");
        }

        if ($description) {
            $query->where('description', 'LIKE', "%$description%");
        }

        if ($status) {
            $query->where('status', 'LIKE', $status);
        }
        
        if ($startDate && $endDate) {
            $query->whereBetween('deadline', [$startDate, $endDate]);
        }
        
        if ($created_at) {
            $query->where('created_at', 'LIKE', "%$created_at%");
        }
        // Ambil hasil query
        $tasks = $query->get();
        // dd($created_at);

        // Cek apakah ada data yang ditemukan
        if ($tasks->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Task Tidak Ditemukan',
            ], Response::HTTP_NOT_FOUND);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Task Ditemukan',
                'data' => TasksResource::collection($tasks),
            ], Response::HTTP_OK);
        }
    }

    public function getTaskByUserIdByKeyword(Request $request, string $users_id)
    {
        // Query dasar untuk mengambil tasks berdasarkan users_id
        $query = tasks::where("users_id", $users_id);

        // Ambil keyword dan filter dari request
        $keyword = $request->keyword;

        // Jika keyword ada, tambahkan filter pencarian berdasarkan title, description, status
        if ($keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'LIKE', "%$keyword%")
                    ->orWhere('description', 'LIKE', "%$keyword%")
                    ->orWhere('status', 'LIKE', "%$keyword%")
                    ->orWhere('created_at', "LIKE", "%$keyword%");
            });
        }

        // Ambil hasil query
        $tasks = $query->get();

        // Cek apakah ada data yang ditemukan
        if ($tasks->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Task Tidak Ditemukan',
            ], Response::HTTP_NOT_FOUND);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Task Ditemukan',
                'data' => TasksResource::collection($tasks),
            ], Response::HTTP_OK);
        }
    }

    public function getTasksById(string $task_id)
    {
        $tasks = tasks::where("id", $task_id)->first();

        if (!$tasks) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak Ada Tasks'
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'success' => true,
            'message' => 'Task Ditemukan',
            'data' => new TasksResource($tasks),
        ], Response::HTTP_OK);
    }

    public function createTasks(Request $request)
    {
        $validator = Validator::make([
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'status' => $request->status
        ], [
            'title' => 'required',
            'description' => 'required',
            'deadline' => 'required',
            'status' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ]);
        }

        $tasks = tasks::create([
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'status' => $request->status,
            'users_id' => $request->users_id,
        ]);
        if ($tasks) {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil Membuat Data',
                'data' => $tasks,
            ], Response::HTTP_CREATED);
        } else {
            return response()->json([
                'status' => 'false',
                'message' => 'Gagal membuat task'
            ], Response::HTTP_BAD_REQUEST);
        }
    }
    public function updateTasks(Request $request, string $tasks_id)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required'],
            'description' => ['required'],
            'deadline' => ['required'],
            'status' => ['required', 'in:PENDING,ON PROGRESS,DONE']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 400);
        }

        try {
            $data = $validator->validated();
            tasks::where('id', $tasks_id)->update($data);
            return response()->json([
                'status' => 'success',
                'message' => 'Task Berhasil DiUpdate',
            ], Response::HTTP_OK);
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
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
                'status' => 'success',
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
