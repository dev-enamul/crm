<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presentation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [ 'customer_id', 'priority', 'followup_date', 'remark', 'employee_id', 'approve_by', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approve_by');
    } 

    public function isVisited(){
        return VisitAnalysis::where('customer_id',$this->customer_id)->exists();
    }
}
