<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Facades\Excel;

class LatestSheetInfografis implements WithMultipleSheets
{
    protected $filePath;
    protected $userId;

    public function __construct($filePath, String $userId)
    {
        $this->filePath = $filePath;
        $this->userId = $userId;
    }

    public function sheets(): array
    {
        // Get sheet names without loading the entire file
        $sheetNames = Excel::sheetNames($this->filePath);

        // Get the last sheet (latest)
        $lastSheetName = end($sheetNames);

        // Return the latest sheet for import
        return [
            $lastSheetName => Excel::queueImport(new InfografisImport($this->filePath, $this->userId), $this->filePath), // Adjust this to use your actual sheet import class
        ];
    }
}
