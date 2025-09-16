<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignTodo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'todo_id'
    ];

    public function user()
     {
        return $this->belongsTo(User::class);
    }

    public function todo()
     {
        return $this->belongsTo(Todo::class);
    }

}
