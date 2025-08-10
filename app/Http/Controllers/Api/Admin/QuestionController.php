<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Exports\QuestionTemplateExport;
use App\Imports\QuestionImport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class QuestionController extends Controller
{
    public function index()
    {
        return response()->json(Question::with('category')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'question' => 'required|string',
            'options' => 'required|array|min:2',
            'correct' => 'required|string|in:' . implode(',', array_keys($request->options)),
        ]);

        $question = Question::create([
            'category_id' => $request->category_id,
            'question' => $request->question,
            'options' => $request->options,
            'correct' => $request->correct,
        ]);

        return response()->json($question, 201);
    }

    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'question' => 'required|string',
            'options' => 'required|array|min:2',
            'correct' => 'required|string|in:' . implode(',', array_keys($request->options)),
        ]);

        $question->update([
            'category_id' => $request->category_id,
            'question' => $request->question,
            'options' => $request->options,
            'correct' => $request->correct,
        ]);

        return response()->json($question);
    }

    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();

        return response()->json(['message' => 'Soal berhasil dihapus']);
    }

    public function show($id)
    {
        $question = Question::with('category')->findOrFail($id);
        return response()->json($question);
    }


    // By Slug
    public function indexByCategory($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $questions = $category->questions()->with('category')->get();

        return response()->json($questions);
    }


    public function storeByCategory(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $request->validate([
            'question' => 'required|string',
            'options' => 'required|array|min:2',
            'correct' => 'required|string|in:' . implode(',', array_keys($request->options)),
        ]);

        $question = $category->questions()->create([
            'question' => $request->question,
            'options' => $request->options,
            'correct' => $request->correct,
        ]);

        return response()->json($question, 201);
    }

    public function showByCategory($slug, $id)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $question = $category->questions()->with('category')->findOrFail($id);

        return response()->json($question);
    }

    public function updateByCategory(Request $request, $slug, $id)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $question = $category->questions()->findOrFail($id);

        $request->validate([
            'question' => 'required|string',
            'options' => 'required|array|min:2',
            'correct' => 'required|string|in:' . implode(',', array_keys($request->options)),
        ]);

        $question->update([
            'question' => $request->question,
            'options' => $request->options,
            'correct' => $request->correct,
        ]);

        return response()->json($question);
    }

    public function destroyByCategory($slug, $id)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $question = $category->questions()->findOrFail($id);
        $question->delete();

        return response()->json(['message' => 'Soal berhasil dihapus']);
    }

    public function downloadTemplate()
    {
        // Log::info('Download template dipanggil');
        return Excel::download(new QuestionTemplateExport, 'template_soal.xlsx');
    }

    public function import(Request $request, $slug)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $category = \App\Models\Category::where('slug', $slug)->firstOrFail();

        Excel::import(new QuestionImport($category->id), $request->file('file'));

        return response()->json(['message' => 'Berhasil import soal.']);
    }
}
