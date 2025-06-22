<?php

namespace App\Imports;

use App\Models\Content;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
class ContentsImport implements ToModel, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $gradeId = $row[0];
        $subjectId = $row[1];
        $quarterId = $row[2];
        $contentValue = $row[3];

        // Check if a record with this exact combination already exists
        $existingRecord = Content::where('grade_id', $gradeId)
                                 ->where('subject_id', $subjectId)
                                 ->where('quarter_id', $quarterId)
                                 ->where('content', $contentValue)
                                 ->first();

        if ($existingRecord) {
            // If the record exists, return null to skip processing this row
            return null;
        } else {
            // If the record does not exist, create a new one
            return new Content([
                'grade_id' => $gradeId,
                'subject_id' => $subjectId,
                'quarter_id' => $quarterId,
                'content' => $contentValue,
            ]);
        }
    }

    public function startRow(): int
    {
        return 2;
    }
}
