<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AudioExtractController extends Controller
{
    public function index()
    {
        return view('audio_extract.index');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'video' => 'required|file|mimes:mp4,avi,mov|max:102400',
            'audio_format' => 'required|in:mp3,wav',
        ]);

        $video = $request->file('video');
        $audioFormat = $request->input('audio_format', 'mp3');
        $apiUrl = 'http://127.0.0.1:8000/extract-audio';

        try {
            $response = \Http::timeout(120)
                ->attach('file', file_get_contents($video->getRealPath()), $video->getClientOriginalName())
                ->post($apiUrl, [
                    'audio_format' => $audioFormat,
                ]);
            $data = $response->json();
            if ($response->ok() && $data['status'] === 'success') {
                return response()->json($data);
            } else {
                return response()->json(['status' => 'error', 'error' => $data['error'] ?? 'Unknown error'], 500);
            }
        } catch (\Exception $e) {
            \Log::error("Failed to send to FastAPI", ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'error' => 'فشل إرسال الفيديو إلى الخادم.'], 500);
        }
    }
    

    public function checkStatus(Request $request)
    {
        $taskId = $request->query('task_id');
        $path = storage_path("app/audio_extract_results/{$taskId}.json");
        if (!file_exists($path)) {
            return response()->json(['status' => 'pending']);
        }
        $data = json_decode(file_get_contents($path), true);
        return response()->json($data);
    }

    public function callback(Request $request)
    {
        \Log::info('AudioExtractController callback called', $request->all());
        $request->validate([
            'task_id' => 'required',
            'status' => 'required',
            'audio_url' => 'nullable|string',
            'format' => 'nullable|string',
            'error' => 'nullable|string',
        ]);

        $taskId = $request->input('task_id');
        $data = $request->only(['status', 'audio_url', 'format', 'error']);
        $dir = storage_path("app/audio_extract_results");
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        try {
            file_put_contents("$dir/{$taskId}.json", json_encode($data));
            \Log::info("AudioExtractController: Result file written for task $taskId");
        } catch (\Exception $e) {
            \Log::error("AudioExtractController: Failed to write result file for task $taskId", ['error' => $e->getMessage()]);
        }
        return response()->json(['success' => true]);
    }
}
