<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'category_id',
        'question',
        'options',
        'correct',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function indexByCategory($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $questions = $category->questions()->with('category')->get();

        return response()->json($questions);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
