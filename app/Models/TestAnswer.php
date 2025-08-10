<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestAnswer extends Model
{
    use HasFactory;

    protected $fillable = ['test_session_id', 'question_id', 'selected', 'is_correct'];

    public function session()
    {
        return $this->belongsTo(TestSession::class, 'test_session_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
