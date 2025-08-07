@extends('layouts.dashboard')

@section('title', 'عارض الصور')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MFCC Image Viewer - Segment {{ $segment_index + 1 }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #333;
            margin: 0;
        }
        
        .header p {
            color: #666;
            margin: 10px 0 0 0;
        }
        
        .image-container {
            text-align: center;
            margin: 30px 0;
        }
        
        .mfcc-image {
            max-width: 100%;
            height: auto;
            border: 2px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .segment-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .segment-info h3 {
            color: #333;
            margin-top: 0;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .info-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #007bff;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
            font-size: 14px;
        }
        
        .info-value {
            color: #333;
            font-size: 16px;
            margin-top: 5px;
        }
        
        .back-button {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        
        .back-button:hover {
            background: #0056b3;
        }
        
        .prediction-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        
        .prediction-fake {
            background: #dc3545;
            color: white;
        }
        
        .prediction-real {
            background: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>MFCC Spectrogram Analysis</h1>
            <p>Segment {{ $segment_index + 1 }} - {{ $analysis->file_name }}</p>
        </div>
        
        <div class="image-container">
            <img src="data:image/png;base64,{{ $image_base64 }}" 
                 alt="MFCC Segment {{ $segment_index + 1 }}" 
                 class="mfcc-image">
        </div>
        
        <div class="segment-info">
            <h3>Segment Analysis Details</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Prediction</div>
                    <div class="info-value">
                        <span class="prediction-badge prediction-{{ strtolower($segment_data['prediction'] ?? 'unknown') }}">
                            {{ $segment_data['prediction'] ?? 'Unknown' }}
                        </span>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Confidence</div>
                    <div class="info-value">
                        @if(isset($segment_data['confidence']))
                            {{ number_format($segment_data['confidence'] * 100, 2) }}%
                            <br><small style="color:#888">({{ rtrim(rtrim(number_format($segment_data['confidence'], 8, '.', ''), '0'), '.') }})</small>
                        @else
                            N/A
                        @endif
                    </div>
                </div>
                
                @if(isset($segment_data['jitter']))
                <div class="info-item">
                    <div class="info-label">Jitter</div>
                    <div class="info-value">{{ $segment_data['jitter'] }}</div>
                </div>
                @endif
                
                @if(isset($segment_data['shimmer']))
                <div class="info-item">
                    <div class="info-label">Shimmer</div>
                    <div class="info-value">{{ $segment_data['shimmer'] }}</div>
                </div>
                @endif
                
                @if(isset($segment_data['pitch_variance']))
                <div class="info-item">
                    <div class="info-label">Pitch Variance</div>
                    <div class="info-value">{{ $segment_data['pitch_variance'] }}</div>
                </div>
                @endif
            </div>
        </div>
        
        <div style="text-align: center;">
            <a href="{{ route('analysis.pdf', $analysis->id) }}" class="back-button">
                ← Back to Report
            </a>
        </div>
    </div>
</body>
</html>
@endsection 