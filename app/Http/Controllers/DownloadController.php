<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function downloadFile($filepath)
    {
        $getId = Agreement::find($filepath);
        // Check if the file exists
        if (Storage::exists($getId->spk_filepath)) {
            // Serve the file for download
            return Storage::download($getId->spk_filepath);
        }

        // Handle the case when the file doesn't exist
        return abort(404, 'File not found.');
    }
}
