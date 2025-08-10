<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class QuestionTemplateExport implements WithHeadings
{
    public function headings(): array
    {
        return [
            'question', // kolom pertanyaan
            'A',        // opsi A
            'B',        // opsi B
            'C',        // opsi C
            'D',        // opsi D
            'correct',  // jawaban yang benar (A/B/C/D)
        ];
    }
}
