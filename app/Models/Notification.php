<?php

namespace App\Models;

use App\Model;

class Notification extends Model
{
    protected $table = 'notifies';
    protected $fillable = ['title','content','description','admin_id'];
    public function admin(){
        return $this->belongsTo(Admin::class);
    }
}
