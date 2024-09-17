<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class comments extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $fillable = [
        'comment',
        'tasks_id',
        'users_id',
    ];

    public function tasks()
    {
        return $this->belongsTo(tasks::class, 'tasks_id');
    }
}
