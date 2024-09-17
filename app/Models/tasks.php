<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class tasks extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $fillable = [
        'title',
        'description',
        'status',
        'users_id',
    ];
    public function comments(): HasMany
    {
        return $this->hasMany(comments::class,'tasks_id');
    }

    

}
