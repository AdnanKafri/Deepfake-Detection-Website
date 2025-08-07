import os
import sys
import logging
import torch
import tensorflow as tf
from PIL import Image
import cv2
import numpy as np

# Set up logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

def check_dependencies():
    """Check if all required dependencies are installed and working."""
    logger.info("Checking dependencies...")
    
    # Check Python version
    logger.info(f"Python version: {sys.version}")
    
    # Check PyTorch
    logger.info(f"PyTorch version: {torch.__version__}")
    logger.info(f"CUDA available: {torch.cuda.is_available()}")
    if torch.cuda.is_available():
        logger.info(f"CUDA device: {torch.cuda.get_device_name(0)}")
    
    # Check TensorFlow
    logger.info(f"TensorFlow version: {tf.__version__}")
    logger.info(f"GPU available for TensorFlow: {len(tf.config.list_physical_devices('GPU')) > 0}")
    
    # Check OpenCV
    logger.info(f"OpenCV version: {cv2.__version__}")
    
    # Check PIL
    logger.info(f"Pillow version: {Image.__version__}")
    
    # Check NumPy
    logger.info(f"NumPy version: {np.__version__}")

def check_models():
    """Check if all required model files exist."""
    logger.info("Checking model files...")
    
    required_models = [
        "models/fine_tuned_epoch_25.pt",
        "models/best_lstm_model_v3.keras"
    ]
    
    for model_path in required_models:
        if os.path.exists(model_path):
            logger.info(f"Found model: {model_path}")
        else:
            logger.error(f"Missing model: {model_path}")

def test_image_processing():
    """Test basic image processing functionality."""
    logger.info("Testing image processing...")
    
    # Create a test image
    test_image = np.zeros((224, 224, 3), dtype=np.uint8)
    test_image[100:150, 100:150] = [255, 255, 255]  # White square
    
    # Test PIL
    try:
        pil_image = Image.fromarray(test_image)
        logger.info("PIL image conversion successful")
    except Exception as e:
        logger.error(f"PIL image conversion failed: {str(e)}")
    
    # Test OpenCV
    try:
        cv2.imwrite("test_image.jpg", test_image)
        logger.info("OpenCV image writing successful")
    except Exception as e:
        logger.error(f"OpenCV image writing failed: {str(e)}")
    
    # Clean up
    if os.path.exists("test_image.jpg"):
        os.remove("test_image.jpg")

def main():
    """Run all checks."""
    logger.info("Starting environment check...")
    
    check_dependencies()
    check_models()
    test_image_processing()
    
    logger.info("Environment check complete!")

if __name__ == "__main__":
    main() 