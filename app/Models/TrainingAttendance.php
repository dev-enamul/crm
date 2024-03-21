<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingAttendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'training_id',
        'user_id',
        'status',
        'time',
        'note',
    ];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}