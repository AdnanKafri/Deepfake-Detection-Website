<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analysis Report - #{{ $analysis->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            direction: ltr;
            text-align: left;
            font-size: 13px;
            color: #333;
        }
        .title {
            background-color: #1d4ed8;
            color: white;
            padding: 12px 20px;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #1e3a8a;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .info p {
            margin: 4px 0;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin: 15px 0;
        }
        .stat-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
        }
        .stat-box .label {
            font-size: 11px;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .stat-box .value {
            font-size: 16px;
            font-weight: bold;
            color: #495057;
        }
        .frames-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            table-layout: fixed;
        }
        .frames-table th,
        .frames-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }
        .frames-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 12px;
        }
        .frames-table td {
            font-size: 11px;
        }
        .frames-table th:nth-child(1),
        .frames-table td:nth-child(1) {
            width: 8%;
        }
        .frames-table th:nth-child(2),
        .frames-table td:nth-child(2) {
            width: 12%;
        }
        .frames-table th:nth-child(3),
        .frames-table td:nth-child(3) {
            width: 10%;
        }
        .frames-table th:nth-child(4),
        .frames-table td:nth-child(4) {
            width: 35%;
        }
        .frames-table th:nth-child(5),
        .frames-table td:nth-child(5) {
            width: 35%;
        }
        .frame-image-container {
            width: 100%;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .frame-image-container img {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        .prediction-real {
            color: #28a745;
            font-weight: bold;
        }
        .prediction-fake {
            color: #dc3545;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>

<div class="title">Deepfake Analysis Report - #{{ $analysis->id }}</div>

<div class="section info">
    <h3>General Information</h3>
    <p><strong>File Name:</strong> {{ $analysis->file_name }}</p>
    <p><strong>Media Type:</strong> {{ strtoupper($analysis->file_type) }}</p>
    <p><strong>Prediction:</strong>
        <span style="color: {{ $analysis->prediction === 'REAL' ? 'green' : 'red' }}; font-weight: bold;">
            {{ $analysis->prediction }}
        </span>
    </p>
    <p><strong>Confidence:</strong> {{ number_format($analysis->confidence * 100, 2) }}% <small style="color:#888">({{ rtrim(rtrim(number_format($analysis->confidence, 8, '.', ''), '0'), '.') }})</small></p>
    <p><strong>Date:</strong> {{ $analysis->created_at->format('Y-m-d H:i') }}</p>
    @if($analysis->user)
        <p><strong>User Name:</strong> {{ $analysis->user->name }}</p>
        <p><strong>Email:</strong> {{ $analysis->user->email }}</p>
    @endif
    <p><strong>Model Used:</strong> {{ $json['model'] ?? $mainResult['details']['model'] ?? 'Unknown' }}</p>
    @php
        $realConfidences = [];
        $fakeConfidences = [];
        // للصور
        if (isset($json['face_details'])) {
            foreach ($json['face_details'] as $face) {
                if (($face['prediction'] ?? null) === 'REAL') {
                    $realConfidences[] = $face['confidence'];
                } elseif (($face['prediction'] ?? null) === 'FAKE') {
                    $fakeConfidences[] = $face['confidence'];
                }
            }
        }
        // للفيديو
        if (isset($json['frame_images'])) {
            foreach ($json['frame_images'] as $frame) {
                if (($frame['prediction'] ?? null) === 'REAL') {
                    $realConfidences[] = $frame['confidence'];
                } elseif (($frame['prediction'] ?? null) === 'FAKE') {
                    $fakeConfidences[] = $frame['confidence'];
                }
            }
        }
        $avgReal = count($realConfidences) ? array_sum($realConfidences) / count($realConfidences) : null;
        $avgFake = count($fakeConfidences) ? array_sum($fakeConfidences) / count($fakeConfidences) : null;
    @endphp
    @if($avgReal !== null)
        <p>
            <strong>Average Real Confidence :</strong>
            {{ number_format($avgReal * 100, 2) }}% <small style="color:#888">({{ rtrim(rtrim(number_format($avgReal, 8, '.', ''), '0'), '.') }})</small>
        </p>
    @endif
    @if($avgFake !== null)
        <p>
            <strong>Average Fake Confidence :</strong>
            {{ number_format($avgFake * 100, 2) }}% <small style="color:#888">({{ rtrim(rtrim(number_format($avgFake, 8, '.', ''), '0'), '.') }})</small>
        </p>
    @endif
</div>

<div class="section">
    <h3>Technical Details</h3>
    @if ($analysis->file_type === 'image')
        <div class="stats-grid">
            <div class="stat-box">
                <div class="label">Faces Detected</div>
                <div class="value">{{ $json['faces_detected'] ?? 'N/A' }}</div>
            </div>
        </div>
    @elseif ($analysis->file_type === 'video')
        <div class="stats-grid">
            <div class="stat-box">
                <div class="label">Total Frames Analyzed</div>
                <div class="value">{{ $json['frames_analyzed'] ?? 'N/A' }}</div>
            </div>
            <div class="stat-box">
                <div class="label">Frames with Faces</div>
                <div class="value">{{ $json['frames_with_faces'] ?? 'N/A' }}</div>
            </div>
            <div class="stat-box">
                <div class="label">REAL Frames</div>
                <div class="value prediction-real">{{ $json['real_frames'] ?? 'N/A' }}</div>
            </div>
            <div class="stat-box">
                <div class="label">FAKE Frames</div>
                <div class="value prediction-fake">{{ $json['fake_frames'] ?? 'N/A' }}</div>
            </div>
            @if($analysis->prediction === 'REAL')
                <div class="stat-box">
                    <div class="label">Average Confidence (REAL)</div>
                    <div class="value prediction-real">{{ round($json['average_real_confidence'] ?? 0, 2) }}</div>
                </div>
            @else
                <div class="stat-box">
                    <div class="label">Average Confidence (FAKE)</div>
                    <div class="value prediction-fake">{{ round($json['average_fake_confidence'] ?? 0, 2) }}</div>
                </div>
            @endif
        </div>
    @elseif ($analysis->file_type === 'audio')
        <div class="stats-grid">
            <div class="stat-box">
                <div class="label">Segments Analyzed</div>
                <div class="value">{{ $json['segments_analyzed'] ?? (is_array($json['segments'] ?? null) ? count($json['segments']) : 'N/A') }}</div>
            </div>
        </div>
    @endif
</div>

@if(count($analysis->details))
    <div class="section">
        @if($analysis->file_type === 'video')
            <h3>Frame Analysis Details</h3>
            <table class="frames-table">
                <thead>
                    <tr>
                        <th>Frame #</th>
                        <th>Prediction</th>
                        <th>Confidence</th>
                        <th>Original Frame</th>
                        <th>Cropped Face</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($analysis->details as $detail)
                        <tr>
                            <td>{{ $detail->segment_index + 1 }}</td>
                            <td class="{{ $detail->prediction === 'REAL' ? 'prediction-real' : 'prediction-fake' }}">
                                {{ $detail->prediction }}
                            </td>
                            <td>
                                {{ number_format($detail->confidence * 100, 2) }}% <br>
                                <small style="color:#888">({{ rtrim(rtrim(number_format($detail->confidence, 8, '.', ''), '0'), '.') }})</small>
                            </td>
                            <td>
                                @if(!empty($detail->original_image_base64))
                                    <div class="frame-image-container">
                                        <img src="data:image/{{ $detail->original_image_type ?? 'jpeg' }};base64,{{ $detail->original_image_base64 }}" alt="Original Frame">
                                    </div>
                                @elseif($detail->original_image_path)
                                    <div class="frame-image-container">
                                        <img src="{{ asset($detail->original_image_path) }}" alt="Original Frame">
                                    </div>
                                @else
                                    <span style="color: #999; font-style: italic;">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if(!empty($detail->cropped_face_base64))
                                    <div class="frame-image-container">
                                        <img src="data:image/{{ $detail->cropped_face_type ?? 'jpeg' }};base64,{{ $detail->cropped_face_base64 }}" alt="Cropped Face">
                                    </div>
                                @elseif($detail->cropped_face_path)
                                    <div class="frame-image-container">
                                        <img src="{{ asset($detail->cropped_face_path) }}" alt="Cropped Face">
                                    </div>
                                @else
                                    <span style="color: #999; font-style: italic;">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif($analysis->file_type === 'image')
            <h3>Face Analysis Details</h3>
            <table class="frames-table">
                <thead>
                    <tr>
                        <th>Face #</th>
                        <th>Prediction</th>
                        <th>Confidence</th>
                        <th>Original Image</th>
                        <th>Cropped Face</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($analysis->details as $detail)
                        <tr>
                            <td>{{ $detail->segment_index + 1 }}</td>
                            <td class="{{ $detail->prediction === 'REAL' ? 'prediction-real' : 'prediction-fake' }}">
                                {{ $detail->prediction }}
                            </td>
                            <td>
                                {{ number_format($detail->confidence * 100, 2) }}% <br>
                                <small style="color:#888">({{ rtrim(rtrim(number_format($detail->confidence, 8, '.', ''), '0'), '.') }})</small>
                            </td>
                            <td>
                                @if(!empty($detail->original_image_base64))
                                    <div class="frame-image-container">
                                        <img src="data:image/{{ $detail->original_image_type ?? 'jpeg' }};base64,{{ $detail->original_image_base64 }}" alt="Original Image">
                                    </div>
                                @elseif($detail->original_image_path)
                                    <div class="frame-image-container">
                                        <img src="{{ asset($detail->original_image_path) }}" alt="Original Image">
                                    </div>
                                @else
                                    <span style="color: #999; font-style: italic;">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if(!empty($detail->cropped_face_base64))
                                    <div class="frame-image-container">
                                        <img src="data:image/{{ $detail->cropped_face_type ?? 'jpeg' }};base64,{{ $detail->cropped_face_base64 }}" alt="Cropped Face">
                                    </div>
                                @elseif($detail->cropped_face_path)
                                    <div class="frame-image-container">
                                        <img src="{{ asset($detail->cropped_face_path) }}" alt="Cropped Face">
                                    </div>
                                @else
                                    <span style="color: #999; font-style: italic;">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endif

@if($analysis->file_type === 'audio' && isset($json['segments']) && is_array($json['segments']))
    <div class="section">
        <h3>Audio Segment Analysis</h3>
        <table class="frames-table" style="table-layout: fixed; width: 100%;">
            <thead>
                <tr>
                    <th style="width: 10%; font-size: 14px; padding: 12px 8px;">Segment #</th>
                    <th style="width: 15%; font-size: 14px; padding: 12px 8px;">Prediction</th>
                    <th style="width: 20%; font-size: 14px; padding: 12px 8px;">Confidence</th>
                    <th style="width: 55%; font-size: 14px; padding: 12px 8px;">MFCC Visualization</th>
                </tr>
            </thead>
            <tbody>
                @foreach($json['segments'] as $i => $seg)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $seg['prediction'] ?? '-' }}</td>
                        <td>
                            @if(isset($seg['confidence']))
                                {{ number_format($seg['confidence'] * 100, 2) }}% <br>
                                <small style="color:#888">({{ rtrim(rtrim(number_format($seg['confidence'], 8, '.', ''), '0'), '.') }})</small>
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align: center; padding: 8px;">
                            @if(isset($seg['mfcc_image_base64']))
                                <img src="data:image/png;base64,{{ $seg['mfcc_image_base64'] }}" 
                                     alt="MFCC Segment {{ $i+1 }}" 
                                     style="max-width: 100%; height: auto; border: 1px solid #ccc; max-height: 60px;">
                                @if(isset($seg['is_most_suspicious']) && $seg['is_most_suspicious'])
                                    <br><small style="color: #dc3545; font-weight: bold;">* Most Suspicious Segment</small>
                                @endif
                            @else
                                <span style="color: #999; font-style: italic;">N/A</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @php
        // البحث عن المقطع الأكثر شكًا للتوضيح
        $most_suspicious_segment = null;
        foreach ($json['segments'] as $i => $seg) {
            if (isset($seg['is_most_suspicious']) && $seg['is_most_suspicious']) {
                $most_suspicious_segment = ['index' => $i, 'data' => $seg];
                break;
            }
        }
    @endphp
    
    @if($most_suspicious_segment && isset($most_suspicious_segment['data']['mfcc_image_base64']))
        <div class="section">
            <h3>Detailed Analysis - Most Suspicious Segment ({{ $most_suspicious_segment['index'] + 1 }})</h3>
            <div style="text-align: center; margin: 10px 0;">
                <img src="data:image/png;base64,{{ $most_suspicious_segment['data']['mfcc_image_base64'] }}" 
                     alt="MFCC Most Suspicious Segment" 
                     style="max-width: 100%; height: auto; border: 1px solid #ccc;">
            </div>
            <div style="font-size:12px; color:#555; margin-top:4px; text-align: center;">
                @if($analysis->prediction === 'FAKE')
                    This MFCC spectrogram shows the most suspicious segment (lowest confidence). The model detected artificial patterns, frequency discontinuities, or unnatural transitions that indicate audio manipulation or synthesis. Look for irregular frequency patterns, sudden jumps, or inconsistent spectral features.
                @else
                    This MFCC spectrogram shows the most confident segment (highest confidence). The frequency patterns maintain natural speech characteristics with smooth transitions, consistent spectral features, and regular frequency progression typical of authentic human speech.
                @endif
            </div>
        </div>
    @endif
@endif
@if($analysis->file_type === 'audio' && isset($json['override_reason']))
    <div class="section">
        <strong>Classification Reason:</strong>
        <p>{{ $json['override_reason'] }}</p>
    </div>
@endif
@if($analysis->file_type === 'audio' && isset($json['secondary_override_reason']))
    <div class="section">
        <strong>Additional Reason:</strong>
        <p>{{ $json['secondary_override_reason'] }}</p>
    </div>
@endif

<div class="footer">
    Report generated by Smart Deepfake Detection System © {{ now()->year }}
</div>

</body>
</html>
