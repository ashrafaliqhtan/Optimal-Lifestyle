# ---------------------------
# Import Required Libraries
# ---------------------------
import os
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns
import tensorflow as tf

from tensorflow.keras.preprocessing.image import ImageDataGenerator
from tensorflow.keras.applications import VGG16
from tensorflow.keras.models import Model
from tensorflow.keras.layers import Dense, Flatten, Dropout
from tensorflow.keras.optimizers import Adam
from tensorflow.keras.callbacks import ModelCheckpoint, EarlyStopping
from sklearn.metrics import classification_report, confusion_matrix

# ---------------------------
# 1. Parameters and Dataset Path
# ---------------------------
IMG_SIZE = (224, 224)  # Image dimensions for resizing
BATCH_SIZE = 32        # Number of images per batch
EPOCHS = 30            # Number of training epochs
DATASET_PATH = '/kaggle/input/potato-viral-disease-dataset'  # Path to dataset

# ---------------------------
# 2. Data Preprocessing and Augmentation
# ---------------------------
# Define ImageDataGenerator for data augmentation
datagen = ImageDataGenerator(
    rescale=1./255,
    rotation_range=20,
    width_shift_range=0.1,
    height_shift_range=0.1,
    shear_range=0.1,
    zoom_range=0.1,
    horizontal_flip=True,
    validation_split=0.2  # 20% for validation
)

# Load training dataset
train_generator = datagen.flow_from_directory(
    directory=DATASET_PATH,
    target_size=IMG_SIZE,
    batch_size=BATCH_SIZE,
    class_mode='categorical',
    subset='training'
)

# Load validation dataset
validation_generator = datagen.flow_from_directory(
    directory=DATASET_PATH,
    target_size=IMG_SIZE,
    batch_size=BATCH_SIZE,
    class_mode='categorical',
    subset='validation'
)

# Get number of classes dynamically
num_classes = len(train_generator.class_indices)
class_labels = list(train_generator.class_indices.keys())
print(f"Detected {num_classes} classes: {class_labels}")

# ---------------------------
# 3. Model Building with Transfer Learning (VGG16)
# ---------------------------
# Load pre-trained VGG16 model (excluding top layers)
base_model = VGG16(weights='imagenet', include_top=False, input_shape=(IMG_SIZE[0], IMG_SIZE[1], 3))
base_model.trainable = False  # Freeze base layers

# Add custom layers for classification
x = Flatten()(base_model.output)
x = Dense(512, activation='relu')(x)
x = Dropout(0.5)(x)
output = Dense(num_classes, activation='softmax')(x)  # Dynamically adjust based on num_classes

# Create final model
model = Model(inputs=base_model.input, outputs=output)

# Compile model
model.compile(optimizer=Adam(learning_rate=1e-4), loss='categorical_crossentropy', metrics=['accuracy'])

# Display model summary
model.summary()

# ---------------------------
# 4. Callbacks for Model Optimization
# ---------------------------
# Save the best model based on validation accuracy (using .keras extension)
checkpoint = ModelCheckpoint("best_model.keras", monitor="val_accuracy", save_best_only=True, verbose=1)

# Stop training early if validation accuracy does not improve for 5 consecutive epochs
early_stopping = EarlyStopping(monitor="val_accuracy", patience=5, restore_best_weights=True, verbose=1)

# ---------------------------
# 5. Model Training (Multiprocessing parameters removed)
# ---------------------------
history = model.fit(
    train_generator,
    epochs=EPOCHS,
    validation_data=validation_generator,
    callbacks=[checkpoint, early_stopping]
)

# ---------------------------
# 6. Training History Visualization
# ---------------------------
# Extract training history
acc = history.history['accuracy']
val_acc = history.history['val_accuracy']
loss = history.history['loss']
val_loss = history.history['val_loss']
epochs_range = range(len(acc))  # Adjust for early stopping if triggered

# Plot Accuracy and Loss graphs
plt.figure(figsize=(14, 6))

plt.subplot(1, 2, 1)
plt.plot(epochs_range, acc, label='Training Accuracy', marker='o')
plt.plot(epochs_range, val_acc, label='Validation Accuracy', marker='o')
plt.legend(loc='lower right')
plt.title('Training and Validation Accuracy')

plt.subplot(1, 2, 2)
plt.plot(epochs_range, loss, label='Training Loss', marker='o')
plt.plot(epochs_range, val_loss, label='Validation Loss', marker='o')
plt.legend(loc='upper right')
plt.title('Training and Validation Loss')

plt.show()

# ---------------------------
# 7. Evaluate Model on Validation Data
# ---------------------------
# Generate predictions for the validation set
y_true = validation_generator.classes
y_pred = np.argmax(model.predict(validation_generator), axis=1)

# Print Classification Report
print("\nClassification Report:\n")
print(classification_report(y_true, y_pred, target_names=class_labels))

# ---------------------------
# 8. Confusion Matrix
# ---------------------------
# Compute confusion matrix
conf_matrix = confusion_matrix(y_true, y_pred)

# Plot confusion matrix using Seaborn heatmap
plt.figure(figsize=(8, 6))
sns.heatmap(conf_matrix, annot=True, fmt='d', cmap='Blues', xticklabels=class_labels, yticklabels=class_labels)
plt.xlabel('Predicted')
plt.ylabel('Actual')
plt.title('Confusion Matrix')
plt.show()
