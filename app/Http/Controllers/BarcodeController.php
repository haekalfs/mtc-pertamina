<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BarcodeController extends Controller
{
    public function index()
    {
        return view('barcode.index');
    }

    public function generateQR(Request $request)
    {
        $validated = $request->validate([
            'qr_text' => 'required|string|max:255',
        ]);

        $qrText = $validated['qr_text'];

        // Generate the QR Code
        $qrCodeData = QrCode::format('png')
            ->size(200)
            ->merge('/storage/app/MTC.jpeg', 0.3)
            ->errorCorrection('H') // High error correction level
            ->generate($qrText);

        // Save the QR Code as a temporary file
        $fileName = 'qr-code-' . time() . '.png';
        $filePath = storage_path('app/public/' . $fileName);
        file_put_contents($filePath, $qrCodeData);

        // Return the file as a download response
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
