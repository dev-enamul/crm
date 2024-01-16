<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Designation extends Model
{ 
    use SoftDeletes;
    use HasFactory; 

    protected $fillable = ['title', 'status', 'commission_id', 'created_by', 'updated_by', 'deleted_by'];
 
    public function commission()
    {
        return $this->belongsTo(Commission::class);
    }
 
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
