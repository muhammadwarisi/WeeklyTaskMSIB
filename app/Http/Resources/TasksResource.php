<?php

namespace App\Http\Resources;

use App\Models\tasks;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TasksResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $output = '';
         // Cek apakah deadline kosong atau null
         if (is_null($this->deadline)) {
            $output = 'Tidak ada deadline';
        } else {
            $today = Carbon::today();
            $deadline = Carbon::parse($this->deadline);

            // Tentukan status deadline
            if ($today->lt($deadline)) {
                $output = 'Tepat waktu';
            } elseif ($today->eq($deadline)) {
                $output = 'Hari deadline';
            } elseif ($today->gt($deadline)) {
                $output = 'Telat';
            }
        }
        return [
            "id" => $this->id,
            "users_id" => $this->users_id,
            'title' => $this->title,
            'description' => $this->description,
            'deadline' => $this->deadline,
            'deadline_status' => $output,
            'status-task' => $this->status
        ];
    }
}
