<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Question;
use App\Models\TestSession;
use App\Models\TestAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserTestController extends Controller
{
    public function submit(Request $request, $slug)
    {
        $user = $request->user('user');
        $category = Category::where('slug', $slug)->firstOrFail();

        $request->validate([
            'answers' => 'required|array|min:1',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.selected' => 'required|string|in:A,B,C,D'
        ]);

        DB::beginTransaction();

        try {
            // Buat sesi tes
            $session = TestSession::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'started_at' => $request->started_at ?? now(),
                'finished_at' => now()
            ]);

            $score = 0;

            foreach ($request->answers as $answer) {
                $question = Question::find($answer['question_id']);
                $isCorrect = strtoupper($answer['selected']) === strtoupper($question->correct);

                TestAnswer::create([
                    'test_session_id' => $session->id,
                    'question_id' => $question->id,
                    'selected' => strtoupper($answer['selected']),
                    'is_correct' => $isCorrect
                ]);

                if ($isCorrect) $score++;
            }

            $session->update(['score' => $score]);

            DB::commit();

            return response()->json([
                'message' => 'Tes selesai',
                'score' => $score,
                'total' => count($request->answers),
                'session_id' => $session->id,
                'started_at' => $session->started_at,
                'finished_at' => $session->finished_at,
                'duration_human' => Carbon::parse($session->started_at)->diffForHumans($session->finished_at, true),
                'duration_seconds' => Carbon::parse($session->started_at)->diffInSeconds($session->finished_at)
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal submit tes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $user = $request->user('user');

        $sessions = TestSession::with('category')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json($sessions);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user('user');

        $session = TestSession::with([
            'category',
            'answers.question'
        ])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return response()->json([
            'session_id' => $session->id,
            'category' => $session->category->name,
            'score' => $session->score,
            'total_questions' => $session->answers->count(),
            'started_at' => $session->started_at,
            'finished_at' => $session->finished_at,
            'duration' => Carbon::parse($session->started_at)->diffForHumans($session->finished_at, true),
            'answers' => $session->answers->map(function ($a) {
                return [
                    'question' => $a->question->question,
                    'selected' => $a->selected,
                    'correct_answer' => $a->question->correct,
                    'is_correct' => $a->is_correct
                ];
            })
        ]);
    }
}
