<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Analysis;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessMediaJob;
use Barryvdh\DomPDF\Facade\Pdf;

class AnalysisController extends Controller
{
    public function index()
    {
        return view('upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'media' => 'required|file|mimes:jpg,jpeg,png,mp4,wav,mp3|max:51200',
        ]);

        $file = $request->file('media');
        $filename = time() . '_' . $file->getClientOriginalName();
        $mediaType = $this->detectMediaType($file->getClientMimeType());

        $path = $file->storeAs('uploads', $filename);

        $analysis = Analysis::create([
            'file_name' => $filename,
            'media_type' => $mediaType,
            'status' => 'pending',
        ]);

        ProcessMediaJob::dispatch($analysis);


        return redirect()->route('analysis.show', $analysis->id);

    }

    private function detectMediaType($mime)
    {
        if (str_contains($mime, 'image')) return 'image';
        if (str_contains($mime, 'video')) return 'video';
        if (str_contains($mime, 'audio')) return 'audio';
        return 'unknown';
    }

public function show($id)
{
    $analysis = \App\Models\Analysis::findOrFail($id);
    return view('analyses.show', compact('analysis'));
}


    public function downloadPDF($id)
    {
        $analysis = Analysis::findOrFail($id);
        $result = json_decode($analysis->result, true);

        $pdf = Pdf::loadView('report', compact('analysis', 'result'));

        return $pdf->download('analysis_report_' . $analysis->id . '.pdf');
    }


    public function generateReport($id)
    {
        $analysis = Analysis::with('details')->findOrFail($id);
        $json = json_decode($analysis->result_json, true);

        // معالجة الصور
        foreach ($analysis->details as $detail) {
            if ($detail->original_image_path && file_exists($detail->original_image_path)) {
                $detail->original_image_base64 = base64_encode(file_get_contents($detail->original_image_path));
            }
            if ($detail->cropped_face_path && file_exists($detail->cropped_face_path)) {
                $detail->cropped_face_base64 = base64_encode(file_get_contents($detail->cropped_face_path));
            }
        }

        $pdf = Pdf::loadView('reports.analysis', [
            'analysis' => $analysis,
            'json' => $json,
        ]);

        return $pdf->download("deepfake_report_{$analysis->id}.pdf");
    }


}
