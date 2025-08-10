<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->string('selected'); // A/B/C/D
            $table->boolean('is_correct');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_answers');
    }
};
