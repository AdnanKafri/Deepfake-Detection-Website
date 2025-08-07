import ffmpeg
import os
import uuid

class AudioExtractor:
    @staticmethod
    def extract_audio(video_path, output_dir, audio_format='mp3'):
        """
        Extracts audio from a video file and saves it as mp3 or wav.
        Returns the output file path.
        """
        assert audio_format in ['mp3', 'wav'], "Unsupported format"
        os.makedirs(output_dir, exist_ok=True)

        base_name = os.path.splitext(os.path.basename(video_path))[0]
        unique_id = uuid.uuid4().hex[:8]
        output_file = os.path.join(output_dir, f"{base_name}_{unique_id}.{audio_format}")

        (
            ffmpeg
            .input(video_path)
            .output(
                output_file,
                acodec='mp3' if audio_format == 'mp3' else 'pcm_s16le',
                ar='44100'
            )
            .run(overwrite_output=True, quiet=True)
        )

        return output_file
