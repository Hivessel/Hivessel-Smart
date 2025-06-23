<?php

namespace App\Imports;

use App\Models\Competency;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
class CompetenciesImport implements ToModel, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $contentId = $row[0];
        $competency = $row[1];
        $reference = $row[2];

        // Check if a record with this exact combination already exists
        $existingRecord = Competency::where('content_id', $contentId)
                                 ->where('competency', $competency)
                                 ->where('reference', $reference)
                                 ->first();

        if ($existingRecord) {
            // If the record exists, return null to skip processing this row
            return null;
        } else {
            // If the record does not exist, create a new one
            return new Competency([
                'content_id' => $contentId,
                'competency' => $competency,
                'reference' => $reference
            ]);
        }
    }

    public function startRow(): int
    {
        return 2;
    }
}
