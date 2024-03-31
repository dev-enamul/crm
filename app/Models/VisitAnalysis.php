<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitAnalysis extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'visitors',
        'projects',
        'remark',
        'employee_id',
        'approve_by',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approve_by');
    } 

    public function isPresentation(){
        return Presentation::where('customer_id',$this->customer_id)->exists();
    }
}
