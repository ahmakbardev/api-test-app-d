<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestSession extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'category_id', 'started_at', 'finished_at', 'score'];

    public function answers()
    {
        return $this->hasMany(TestAnswer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}