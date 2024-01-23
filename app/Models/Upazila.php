<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Upazila extends Model
{
    use HasFactory;

    protected $fillable = ['district_id', 'name', 'bn_name', 'url'];
 
    public function district()
    {
        return $this->belongsTo(District::class);
    }

}
