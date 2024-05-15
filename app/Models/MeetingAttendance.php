<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingAttendance extends Model
{
    use HasFactory;
    protected $fillable = ['meeting_id', 'user_id', 'is_present', 'note']; 

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    } 

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
