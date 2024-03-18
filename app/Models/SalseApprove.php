<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalseApprove extends Model
{ 
    use HasFactory; 
    protected $fillable = [
        'salse_id',
        'customer_id',
        'user_id',
    ];
}
