<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'total_floor',
        'google_map',
        'address',
        'description',
        'status',
        'countrie_id',
        'division_id',
        'district_id',
        'upazila_id',
        'village_id',
        'union_id',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
 
    public function country()
    {
        return $this->belongsTo(Country::class, 'countrie_id');
    } 
    
    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }
 
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }
 
    public function upazila()
    {
        return $this->belongsTo(Upazila::class, 'upazila_id');
    }
 
    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id');
    }
 
    public function union()
    {
        return $this->belongsTo(Union::class, 'union_id');
    }

    public function images()
    {
        return $this->hasMany(ProjectImage::class, 'project_id');
    } 

    public function units()
    {
        return $this->hasMany(ProjectUnit::class, 'project_id');
    }
}