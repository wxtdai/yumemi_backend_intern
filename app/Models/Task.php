<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'rimotatsu_id',
        'title',
        'achievement_condition',
    ];

    public function rimotatsu() {
        return $this->belongsTo(Rimotatsu::class);
    }

    public function users() {
        return $this->belongsToMany(User::class, 'achievements');
    }
}
