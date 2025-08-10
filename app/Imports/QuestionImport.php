<?php

namespace App\Imports;

use App\Models\Question;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionImport implements ToModel, WithHeadingRow
{
    private $categoryId;

    public function __construct($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Eloquent|null
     */
    public function model(array $row)
    {
        $correct = strtoupper($row['correct'] ?? '');

        // Bersihkan setiap nilai opsi dari Excel
        $options = [
            'A' => $this->cleanExcelValue($row['a'] ?? ''),
            'B' => $this->cleanExcelValue($row['b'] ?? ''),
            'C' => $this->cleanExcelValue($row['c'] ?? ''),
            'D' => $this->cleanExcelValue($row['d'] ?? ''),
        ];

        // Mengirimkan array options langsung ke model.
        // Laravel akan meng-encode-nya menjadi JSON secara otomatis
        // karena adanya 'casts' di model Question.
        return new Question([
            'category_id' => $this->categoryId,
            'question'    => $this->cleanExcelValue($row['question'] ?? ''),
            'options'     => $options,
            'correct'     => $correct,
        ]);
    }

    /**
     * Membersihkan nilai string dari Excel.
     */
    private function cleanExcelValue(string $value): string
    {
        // Trim spasi di awal/akhir dan hapus kutip ganda jika ada
        $value = trim($value);
        $value = trim($value, '"');
        $value = stripcslashes($value);

        return $value;
    }
}
