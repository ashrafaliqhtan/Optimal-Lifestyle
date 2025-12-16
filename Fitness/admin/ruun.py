
import kagglehub

# Download the dataset
path = kagglehub.dataset_download("asaniczka/amazon-products-dataset-2023-1-4m-products")
print("Dataset downloaded to:", path)



import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns

# Load the datasets
products_df = pd.read_csv('/root/.cache/kagglehub/datasets/asaniczka/amazon-products-dataset-2023-1-4m-products/versions/17/amazon_products.csv')
categories_df = pd.read_csv('/root/.cache/kagglehub/datasets/asaniczka/amazon-products-dataset-2023-1-4m-products/versions/17/amazon_categories.csv')

# Display basic info
print("Products DataFrame Info:")
print(products_df.info())
print("\nCategories DataFrame Info:")
print(categories_df.info())

# Display first few rows
print("\nFirst 5 rows of Products:")
print(products_df.head())
print("\nFirst 5 rows of Categories:")
print(categories_df.head())

# Step 5: Data Preprocessing
# Merge the datasets
merged_df = pd.merge(products_df, categories_df, left_on='category_id', right_on='id', how='left')

# Handle missing values
print("\nMissing values before handling:")
print(merged_df.isnull().sum())

# Fill missing values appropriately
merged_df['stars'].fillna(0, inplace=True)
merged_df['reviews'].fillna(0, inplace=True)
merged_df['price'].fillna(0, inplace=True)
merged_df['listPrice'].fillna(0, inplace=True)
merged_df['isBestSeller'].fillna(False, inplace=True)

# Convert data types
merged_df['stars'] = merged_df['stars'].astype(float)
merged_df['reviews'] = merged_df['reviews'].astype(int)
merged_df['price'] = merged_df['price'].astype(float)
merged_df['listPrice'] = merged_df['listPrice'].astype(float)
merged_df['isBestSeller'] = merged_df['isBestSeller'].astype(bool)

# Calculate discount percentage
merged_df['discount_pct'] = np.where(
    merged_df['listPrice'] > 0,
    ((merged_df['listPrice'] - merged_df['price']) / merged_df['listPrice']) * 100,
    0
)

print("\nMissing values after handling:")
print(merged_df.isnull().sum())

# Step 6: Exploratory Data Analysis
# Basic statistics
print("\nDescriptive Statistics:")
print(merged_df.describe())

# Visualization 1: Distribution of ratings
plt.figure(figsize=(10, 6))
sns.histplot(merged_df[merged_df['stars'] > 0]['stars'], bins=20, kde=True)
plt.title('Distribution of Product Ratings')
plt.xlabel('Rating')
plt.ylabel('Count')
plt.show()

# Visualization 2: Price distribution
plt.figure(figsize=(10, 6))
sns.histplot(merged_df[merged_df['price'] > 0]['price'], bins=50, kde=True)
plt.title('Distribution of Product Prices')
plt.xlabel('Price ($)')
plt.ylabel('Count')
plt.xlim(0, 500)  # Limit x-axis to better see the distribution
plt.show()

# Visualization 3: Top categories
top_categories = merged_df['category_name'].value_counts().head(10)
plt.figure(figsize=(12, 6))
sns.barplot(x=top_categories.values, y=top_categories.index, palette='viridis')
plt.title('Top 10 Product Categories')
plt.xlabel('Number of Products')
plt.ylabel('Category')
plt.show()

# Visualization 4: Best sellers vs ratings
plt.figure(figsize=(10, 6))
sns.boxplot(x='isBestSeller', y='stars', data=merged_df[merged_df['stars'] > 0])
plt.title('Product Ratings: Best Sellers vs Regular Products')
plt.xlabel('Is Best Seller?')
plt.ylabel('Rating')
plt.show()

# Step 7: Prepare data for recommendation system
# Feature engineering
merged_df['popularity_score'] = merged_df['stars'] * np.log1p(merged_df['reviews'])
merged_df['value_for_money'] = merged_df['stars'] / (merged_df['price'] + 1)  # Adding 1 to avoid division by zero

# Select relevant features for recommendation
rec_df = merged_df[['asin', 'title', 'category_name', 'stars', 'reviews', 'price', 
                    'isBestSeller', 'popularity_score', 'value_for_money']]

# Step 8: Basic Recommendation Models
from sklearn.model_selection import train_test_split
from sklearn.tree import DecisionTreeClassifier
from sklearn.neighbors import NearestNeighbors
from sklearn.metrics import classification_report, confusion_matrix, accuracy_score

# For demonstration, let's create a binary target variable: whether a product is highly rated (stars >= 4)
rec_df['highly_rated'] = (rec_df['stars'] >= 4).astype(int)

# Prepare features and target
X = rec_df[['price', 'isBestSeller', 'popularity_score', 'value_for_money']]
y = rec_df['highly_rated']

# Split data
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.3, random_state=42)

# Model 1: Decision Tree
dt_model = DecisionTreeClassifier(max_depth=5, random_state=42)
dt_model.fit(X_train, y_train)
y_pred_dt = dt_model.predict(X_test)

print("\nDecision Tree Performance:")
print(classification_report(y_test, y_pred_dt))
print("Accuracy:", accuracy_score(y_test, y_pred_dt))

# Model 2: KNN (for similarity-based recommendation)
# First, normalize the features
from sklearn.preprocessing import StandardScaler
scaler = StandardScaler()
X_scaled = scaler.fit_transform(X)

# Create KNN model for finding similar products
knn_model = NearestNeighbors(n_neighbors=5, algorithm='auto')
knn_model.fit(X_scaled)

# Example: Find similar products to the first product in our dataset
distances, indices = knn_model.kneighbors([X_scaled[0]])

print("\nSimilar products to:", rec_df.iloc[0]['title'])
for i in indices[0][1:]:  # Skip the first one as it's the same product
    print(f"- {rec_df.iloc[i]['title']} (Rating: {rec_df.iloc[i]['stars']}, Price: ${rec_df.iloc[i]['price']:.2f})")

# Step 9: Visualize model performance
# Confusion matrix for Decision Tree
plt.figure(figsize=(8, 6))
cm = confusion_matrix(y_test, y_pred_dt)
sns.heatmap(cm, annot=True, fmt='d', cmap='Blues', 
            xticklabels=['Not Highly Rated', 'Highly Rated'],
            yticklabels=['Not Highly Rated', 'Highly Rated'])
plt.title('Decision Tree Confusion Matrix')
plt.xlabel('Predicted')
plt.ylabel('Actual')
plt.show()

# Feature importance for Decision Tree
plt.figure(figsize=(10, 6))
importances = dt_model.feature_importances_
features = X.columns
sns.barplot(x=importances, y=features, palette='viridis')
plt.title('Feature Importance in Decision Tree Model')
plt.xlabel('Importance Score')
plt.ylabel('Feature')
plt.show()