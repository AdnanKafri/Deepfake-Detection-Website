<?php

namespace App\Jobs;

use App\Models\Analysis;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $analysis;

    public function __construct(Analysis $analysis)
    {
        $this->analysis = $analysis;
    }

    public function handle()
    {
        try {
            $path = storage_path('app/uploads/' . $this->analysis->file_name);

            // Synchronous call to FastAPI with a long timeout
            $response = Http::timeout(180) // 180-second timeout
                ->attach('file', file_get_contents($path), $this->analysis->file_name)
                ->post(env('PYTHON_API_URL') . '/analyze', [
                    'file_type' => $this->analysis->media_type
                ]);

            if ($response->successful()) {
                $this->analysis->update([
                    'status' => 'completed',
                    'result' => $response->body(),
                ]);
            } else {
                $this->analysis->update([
                    'status' => 'failed',
                    'result' => json_encode($response->json() ?? ['error' => 'Request failed with status: ' . $response->status()]),
                ]);
                Log::error('Analysis request failed: ' . $response->body());
            }
            
        } catch (\Exception $e) {
            Log::error('Processing failed: ' . $e->getMessage());
            $this->analysis->update([
                'status' => 'failed',
                'result' => json_encode(['error' => $e->getMessage()]),
            ]);
        }
    }
}
