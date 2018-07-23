<?php

namespace App\Models;
use App\Model;

class Skill extends Model
{
    //
    protected $table = 'skills';
    protected $fillable = ['name'];
    public function jobs(){
        return $this->belongsToMany(Job::class,'job_position');
    }
}
