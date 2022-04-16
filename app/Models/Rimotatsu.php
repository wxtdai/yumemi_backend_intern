<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rimotatsu extends Model
{
    use HasFactory;

    protected $fillable = [
        'title'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function votes() {
        return $this->hasMany(Vote::class);
    }

    public function tasks() {
        return $this->hasMany(Task::class);
    }
}
