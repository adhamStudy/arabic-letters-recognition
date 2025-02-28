from flask import Flask
import tensorflow as tf
import numpy as np
from PIL import Image
import os
model = tf.keras.models.load_model("model.keras", compile=False)
model.save("model.h5")

# app = Flask(__name__)

# # Load the model
# model = tf.keras.models.load_model("model.keras")

# # Function to preprocess image
# def preprocess_image(image_path):
#     image = Image.open(image_path).convert("RGB")
#     image = image.resize((32, 32))  # Resize to match model's input shape
#     image = np.array(image) / 255.0  # Normalize pixel values
#     image = np.expand_dims(image, axis=0)  # Add batch dimension
#     return image

# # Path to the image on desktop
# image_path = os.path.expanduser("~/Desktop/a.jpg")
# image = preprocess_image(image_path)
# predictions = model.predict(image)
# predicted_class = np.argmax(predictions)
# print(f"Predicted class: {predicted_class}")

# if __name__ == '__main__':
#     app.run(debug=True)
