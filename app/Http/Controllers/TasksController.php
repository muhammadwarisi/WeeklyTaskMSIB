<?php

namespace App\Http\Controllers;

use App\Rules\enums;
use App\Models\User;
use App\Models\tasks;
use Illuminate\Http\Request;
use App\Http\Resources\TasksResource;
use App\Rules\Enums as RulesEnums;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;



class TasksController extends Controller
{
    public function getTasks(string $users_id)
    {
        $tasks = tasks::where("users_id", $users_id)->get();
        // dd(count($tasks)); 
        if(count($tasks) == 0) {
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
        $validator = Validator::make([
            'title'=> $request->title,
            'description'=> $request->description,
            'status' => $request->status
        ], [
            'title'=> 'required',
            'description'=> 'required',
            'status'=> ['required', new enums()]
        ]);

        if($validator->fails()){
            return response()->json([
                'status'=> false,
                'message'=> $validator->errors()
            ]);
        }
        

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
        $validator = Validator::make($request->all(),[
            'title'=> ['required'],
            'description'=> ['required'],
            'status'=> ['required', 'in:PENDING,ON PROGRESS, DONE']
        ]);
        // $data = [
        //     'title'=> $request->input('title'),
        //     'description'=> $request->input('description'),
        //     'status'=> $request->input('status'),
        // ];
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 400);
        }
       
        try {
            $data = $validator->validated();
            $tasks = tasks::where('id', $tasks_id)->update($data);
            return response()->json([
                        'status' => 'success',
                        'message' => 'Task Berhasil DiUpdate',
                    ], 200);
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ],500);
        }
        // if ($tasks) {
        //     return response()->json([
        //         'status' => 'success',
        //         'message' => 'Task Berhasil DiUpdate',
        //     ], 200);
        // } 
        // else {
            // return response()->json([
            //     'message' => 'Tasks Gagal DiUpdate',
            //     'status' => 'failed',
            //     'errors' => $validator->errors()
            // ],400);
        // }
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
