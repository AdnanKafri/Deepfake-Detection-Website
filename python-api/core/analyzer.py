from .audio_analyzer import AudioAnalyzer
from .media_analyzer import MediaAnalyzer

class DeepfakeAnalyzer:
    def __init__(self):
        self.audio_analyzer = AudioAnalyzer()
        self.media_analyzer = MediaAnalyzer()

    def analyze_image(self, image_path):
        return self.media_analyzer.analyze_image(image_path)

    def analyze_video(self, video_path, frames_to_sample=10, save_frames=False):
        return self.media_analyzer.analyze_video(video_path, frames_to_sample, save_frames)

    def analyze_audio(self, audio_path):
        return self.audio_analyzer.analyze_audio(audio_path)

    def analyze_audio_segments(self, audio_path):
        return self.audio_analyzer.analyze_audio_segments(audio_path)
