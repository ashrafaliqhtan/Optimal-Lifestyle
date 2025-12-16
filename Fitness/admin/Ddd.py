


import numpy as np
import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns
from wordcloud import WordCloud
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import linear_kernel
from sklearn.decomposition import TruncatedSVD
from sklearn.model_selection import train_test_split
from sklearn.tree import DecisionTreeClassifier
from sklearn.ensemble import RandomForestClassifier
from lightgbm import LGBMClassifier
from sklearn.neighbors import NearestNeighbors
from sklearn.preprocessing import StandardScaler, MinMaxScaler
from sklearn.metrics import (classification_report, confusion_matrix, 
                           accuracy_score, precision_recall_curve, roc_curve, auc)
from sklearn.cluster import KMeans
from sklearn.manifold import TSNE
import nltk
from nltk.corpus import stopwords
from nltk.stem import WordNetLemmatizer
import string
import warnings
warnings.filterwarnings('ignore')

from surprise import Dataset, Reader, KNNBasic, accuracy
from surprise.model_selection import train_test_split as surprise_train_test_split






dtypes = {
    'asin': 'string',
    'title': 'string',
    'imgUrl': 'string',
    'productURL': 'string',
    'stars': 'float32',
    'reviews': 'int32',
    'price': 'float32',
    'listPrice': 'float32',
    'category_id': 'int32',
    'isBestSeller': 'boolean'
}

products_df = pd.read_csv('/root/.cache/kagglehub/datasets/asaniczka/amazon-products-dataset-2023-1-4m-products/versions/17/amazon_products.csv', dtype=dtypes)
categories_df = pd.read_csv('/root/.cache/kagglehub/datasets/asaniczka/amazon-products-dataset-2023-1-4m-products/versions/17/amazon_categories.csv')

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

# Merge datasets
merged_df = pd.merge(products_df, categories_df, left_on='category_id', right_on='id', how='left')

# Data cleaning
def clean_data(df):
    # Handle missing values
    df['stars'] = df['stars'].fillna(0)
    df['reviews'] = df['reviews'].fillna(0)
    df['price'] = df['price'].fillna(0)
    df['listPrice'] = df['listPrice'].fillna(df['price'])  # If no list price, assume no discount
    df['isBestSeller'] = df['isBestSeller'].fillna(False)
    
    # Calculate derived features
    df['discount_pct'] = np.where(
        df['listPrice'] > 0,
        ((df['listPrice'] - df['price']) / df['listPrice']) * 100,
        0
    )
    
    # Create binary flags
    df['has_discount'] = df['discount_pct'] > 0
    df['has_reviews'] = df['reviews'] > 0
    
    # Create popularity score (weighted combination of stars and reviews)
    df['popularity_score'] = (df['stars'] * np.log1p(df['reviews'])).fillna(0)
    
    # Create value for money metric
    df['value_for_money'] = (df['stars'] / (df['price'] + 1)).fillna(0)  # Adding 1 to avoid division by zero
    
    # Create price categories
    bins = [0, 10, 25, 50, 100, 250, 500, 1000, float('inf')]
    labels = ['<$10', '$10-25', '$25-50', '$50-100', '$100-250', '$250-500', '$500-1000', '>$1000']
    df['price_range'] = pd.cut(df['price'], bins=bins, labels=labels, right=False)
    
    return df

merged_df = clean_data(merged_df)

# Text preprocessing for title analysis
def preprocess_text(text):
    if pd.isna(text):
        return ""
    
    # Convert to lowercase
    text = text.lower()
    
    # Remove punctuation
    text = text.translate(str.maketrans('', '', string.punctuation))
    
    # Remove stopwords
    stop_words = set(stopwords.words('english'))
    words = text.split()
    words = [word for word in words if word not in stop_words]
    
    # Lemmatization
    lemmatizer = WordNetLemmatizer()
    words = [lemmatizer.lemmatize(word) for word in words]
    
    return ' '.join(words)

# Apply text preprocessing (this may take a while for large datasets)
merged_df['clean_title'] = merged_df['title'].apply(preprocess_text)

# Set style for visualizations
sns.set_style("whitegrid")
plt.rcParams['figure.figsize'] = (12, 6)
plt.rcParams['font.size'] = 12

# 1. Product Ratings Distribution
plt.figure(figsize=(14, 6))
ax = sns.histplot(merged_df[merged_df['stars'] > 0]['stars'], bins=30, kde=True, color='royalblue')
plt.title('Distribution of Product Ratings', fontsize=16)
plt.xlabel('Rating (1-5 stars)', fontsize=14)
plt.ylabel('Number of Products', fontsize=14)
plt.axvline(x=merged_df['stars'].mean(), color='red', linestyle='--', label=f'Mean: {merged_df["stars"].mean():.2f}')
plt.legend()
plt.show()

# 2. Price Analysis
plt.figure(figsize=(14, 6))
sns.boxplot(x=merged_df[merged_df['price'] > 0]['price'], color='lightgreen')
plt.title('Product Price Distribution', fontsize=16)
plt.xlabel('Price ($)', fontsize=14)
plt.xlim(0, 200)  # Focusing on majority of products
plt.show()

# 3. Best Sellers Analysis
best_sellers = merged_df[merged_df['isBestSeller'] == True]
regular_products = merged_df[merged_df['isBestSeller'] == False]

plt.figure(figsize=(14, 6))
sns.kdeplot(best_sellers['stars'], label='Best Sellers', shade=True, color='green')
sns.kdeplot(regular_products['stars'], label='Regular Products', shade=True, color='blue')
plt.title('Rating Distribution: Best Sellers vs Regular Products', fontsize=16)
plt.xlabel('Rating', fontsize=14)
plt.ylabel('Density', fontsize=14)
plt.legend()
plt.show()

# 4. Top Categories Word Cloud
plt.figure(figsize=(14, 8))
category_text = ' '.join(merged_df['category_name'].dropna().astype(str))
wordcloud = WordCloud(width=800, height=400, background_color='white').generate(category_text)
plt.imshow(wordcloud, interpolation='bilinear')
plt.axis('off')
plt.title('Most Common Product Categories', fontsize=16)
plt.show()

# 5. Correlation Heatmap
numeric_cols = ['stars', 'reviews', 'price', 'listPrice', 'discount_pct', 'popularity_score', 'value_for_money']
plt.figure(figsize=(12, 8))
corr_matrix = merged_df[numeric_cols].corr()
sns.heatmap(corr_matrix, annot=True, cmap='coolwarm', center=0)
plt.title('Feature Correlation Heatmap', fontsize=16)
plt.show()

# 6. Price vs. Ratings
plt.figure(figsize=(14, 6))
sns.scatterplot(x='price', y='stars', data=merged_df[(merged_df['price'] < 500) & (merged_df['stars'] > 0)],
                alpha=0.3, hue='isBestSeller', palette=['blue', 'green'])
plt.title('Price vs. Product Ratings', fontsize=16)
plt.xlabel('Price ($)', fontsize=14)
plt.ylabel('Rating', fontsize=14)
plt.legend(title='Best Seller')
plt.show()


# 1. Content-Based Filtering (TF-IDF + Cosine Similarity)
# Sample a subset for demonstration (full dataset would be too large)
sample_df = merged_df.sample(5000, random_state=42).copy()

# Create TF-IDF matrix
tfidf = TfidfVectorizer(stop_words='english')
tfidf_matrix = tfidf.fit_transform(sample_df['clean_title'])

# Compute cosine similarities
cosine_sim = linear_kernel(tfidf_matrix, tfidf_matrix)

def content_based_recommendations(title, cosine_sim=cosine_sim, df=sample_df, top_n=5):

    idx = df[df['title'] == title].index[0]

    sim_scores = list(enumerate(cosine_sim[idx]))

    sim_scores = sorted(sim_scores, key=lambda x: x[1], reverse=True)

    sim_scores = sim_scores[1:top_n+1]  # Skip 
    product_indices = [i[0] for i in sim_scores]

    return df.iloc[product_indices][['title', 'stars', 'price', 'category_name']]

# Example usage
print("\nContent-Based Recommendations for 'Wireless Earbuds':")
print(content_based_recommendations(sample_df.iloc[10]['title']))

# 2. Collaborative Filtering (Using Surprise Library)
# For demonstration, we'll create synthetic user-item interactions
np.random.seed(42)
num_users = 1000
user_ids = np.random.randint(10000, 99999, size=num_users)
product_ids = sample_df['asin'].sample(num_users, replace=True).values
ratings = np.random.randint(1, 6, size=num_users)  # Random ratings 1-5

# Create ratings dataframe
ratings_df = pd.DataFrame({
    'user_id': user_ids,
    'item_id': product_ids,
    'rating': ratings
})

# Load data into Surprise format
reader = Reader(rating_scale=(1, 5))
data = Dataset.load_from_df(ratings_df[['user_id', 'item_id', 'rating']], reader)

# Split data
trainset, testset = surprise_train_test_split(data, test_size=0.25, random_state=42)

# Train KNN model
knn_cf = KNNBasic(sim_options={'name': 'cosine', 'user_based': False})
knn_cf.fit(trainset)

# Make predictions
predictions = knn_cf.test(testset)

# Evaluate
print("\nCollaborative Filtering Performance:")
print(f"RMSE: {accuracy.rmse(predictions):.4f}")
print(f"MAE: {accuracy.mae(predictions):.4f}")

# 3. Hybrid Recommendation System (Content + Popularity)
def hybrid_recommendation(product_id, df=sample_df, cosine_sim=cosine_sim, popularity_weight=0.3, top_n=5):
    # Get content-based similarity
    idx = df[df['asin'] == product_id].index[0]
    sim_scores = list(enumerate(cosine_sim[idx]))
    sim_scores = sorted(sim_scores, key=lambda x: x[1], reverse=True)
    
    # Get indices and similarity scores
    indices = [i[0] for i in sim_scores]
    sim_values = [i[1] for i in sim_scores]
    
    # Get popularity scores
    pop_scores = df['popularity_score'].values[indices]
    
    # Normalize both metrics
    sim_values = MinMaxScaler().fit_transform(np.array(sim_values).reshape(-1, 1)).flatten()
    pop_scores = MinMaxScaler().fit_transform(np.array(pop_scores).reshape(-1, 1)).flatten()
    
    # Combine scores
    combined_scores = (1 - popularity_weight) * sim_values + popularity_weight * pop_scores
    
    # Sort by combined score
    combined_indices = sorted(zip(indices, combined_scores), key=lambda x: x[1], reverse=True)
    
    # Get top_n recommendations
    top_indices = [i[0] for i in combined_indices[1:top_n+1]]  # Skip first (itself)
    
    return df.iloc[top_indices][['title', 'stars', 'price', 'category_name', 'popularity_score']]

# Example usage
print("\nHybrid Recommendations:")
print(hybrid_recommendation(sample_df.iloc[10]['asin']))

# 4. Classification Model for Recommendation (Predict Highly Rated Products)
# Prepare data for classification
classification_df = sample_df.copy()
classification_df['highly_rated'] = (classification_df['stars'] >= 4).astype(int)

# Select features and target
features = ['price', 'isBestSeller', 'discount_pct', 'reviews', 'popularity_score', 'value_for_money']
X = classification_df[features]
y = classification_df['highly_rated']

# Split data
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.3, random_state=42)

# Train models
models = {
    'Decision Tree': DecisionTreeClassifier(max_depth=5, random_state=42),
    'Random Forest': RandomForestClassifier(n_estimators=100, random_state=42),
    'LightGBM': LGBMClassifier(random_state=42)
}

for name, model in models.items():
    model.fit(X_train, y_train)
    y_pred = model.predict(X_test)
    y_proba = model.predict_proba(X_test)[:, 1]
    
    print(f"\n{name} Performance:")
    print(classification_report(y_test, y_pred))
    print(f"Accuracy: {accuracy_score(y_test, y_pred):.4f}")
    
    # Plot ROC curve
    fpr, tpr, _ = roc_curve(y_test, y_proba)
    roc_auc = auc(fpr, tpr)
    
    plt.figure(figsize=(8, 6))
    plt.plot(fpr, tpr, color='darkorange', lw=2, label=f'ROC curve (area = {roc_auc:.2f})')
    plt.plot([0, 1], [0, 1], color='navy', lw=2, linestyle='--')
    plt.xlim([0.0, 1.0])
    plt.ylim([0.0, 1.05])
    plt.xlabel('False Positive Rate')
    plt.ylabel('True Positive Rate')
    plt.title(f'ROC Curve - {name}')
    plt.legend(loc="lower right")
    plt.show()


tfidf_small = TfidfVectorizer(stop_words='english', max_features=5000)
tfidf_matrix_small = tfidf_small.fit_transform(sample_df['clean_title'])

# Reduce dimensionality with t-SNE
tsne = TSNE(n_components=2, perplexity=30, random_state=42)
embeddings_2d = tsne.fit_transform(tfidf_matrix_small.toarray())

# Plot
plt.figure(figsize=(14, 10))
scatter = plt.scatter(embeddings_2d[:, 0], embeddings_2d[:, 1], 
                     c=sample_df['stars'], cmap='viridis', alpha=0.6)
plt.colorbar(scatter, label='Star Rating')
plt.title('t-SNE Visualization of Product Embeddings (Colored by Rating)', fontsize=16)
plt.xlabel('t-SNE Dimension 1')
plt.ylabel('t-SNE Dimension 2')
plt.show()

# 2. Cluster Analysis
# Perform K-means clustering
kmeans = KMeans(n_clusters=5, random_state=42)
clusters = kmeans.fit_predict(tfidf_matrix_small)

# Plot clusters
plt.figure(figsize=(14, 10))
scatter = plt.scatter(embeddings_2d[:, 0], embeddings_2d[:, 1], 
                     c=clusters, cmap='tab10', alpha=0.6)
plt.colorbar(scatter, label='Cluster')
plt.title('Product Clusters Identified by K-means', fontsize=16)
plt.xlabel('t-SNE Dimension 1')
plt.ylabel('t-SNE Dimension 2')
plt.show()

# 3. Feature Importance from LightGBM
lgb_model = models['LightGBM']
importance = pd.DataFrame({
    'feature': features,
    'importance': lgb_model.feature_importances_
}).sort_values('importance', ascending=False)

plt.figure(figsize=(10, 6))
sns.barplot(x='importance', y='feature', data=importance, palette='viridis')
plt.title('Feature Importance for Predicting Highly Rated Products', fontsize=16)
plt.xlabel('Importance Score')
plt.ylabel('Feature')
plt.show()


def evaluate_recommendations(recommendation_function, product_ids, df, top_n=5):
    precision_scores = []
    recall_scores = []
    
    for pid in product_ids:
        # Get ground truth (products with same category)
        true_category = df[df['asin'] == pid]['category_name'].values[0]
        relevant_items = df[df['category_name'] == true_category]['asin'].values
        
        # Get recommendations
        recommendations = recommendation_function(pid, top_n=top_n)['asin'].values
        
        # Calculate precision and recall
        relevant_recommended = len(set(recommendations) & set(relevant_items))
        
        precision = relevant_recommended / len(recommendations)
        recall = relevant_recommended / min(len(relevant_items), top_n)
        
        precision_scores.append(precision)
        recall_scores.append(recall)
    
    return np.mean(precision_scores), np.mean(recall_scores)

# Evaluate content-based
content_precision, content_recall = evaluate_recommendations(
    lambda pid, top_n: content_based_recommendations(df[df['asin'] == pid]['title'].values[0], top_n=top_n),
    sample_df['asin'].sample(20, random_state=42),
    sample_df
)

# Evaluate hybrid
hybrid_precision, hybrid_recall = evaluate_recommendations(
    hybrid_recommendation,
    sample_df['asin'].sample(20, random_state=42),
    sample_df
)

print("\nRecommendation System Evaluation:")
print(f"Content-Based - Avg Precision: {content_precision:.4f}, Avg Recall: {content_recall:.4f}")
print(f"Hybrid - Avg Precision: {hybrid_precision:.4f}, Avg Recall: {hybrid_recall:.4f}")


def get_recommendations(product_title, method='hybrid', top_n=5):
    """
    Unified recommendation function that can use different methods
    """
    # Find the product ID
    product_row = sample_df[sample_df['title'] == product_title]
    if len(product_row) == 0:
        return pd.DataFrame()  # Return empty if product not found
    
    product_id = product_row['asin'].values[0]
    
    if method == 'content':
        return content_based_recommendations(product_title, top_n=top_n)
    elif method == 'hybrid':
        return hybrid_recommendation(product_id, top_n=top_n)
    else:
        raise ValueError("Invalid method. Choose 'content' or 'hybrid'.")

def predict_rating(product_features):
    """
    Predict whether a product will be highly rated based on its features
    """
    # Prepare input features
    input_data = pd.DataFrame([product_features])
    
    # Make prediction
    model = models['LightGBM']
    prediction = model.predict(input_data)[0]
    probability = model.predict_proba(input_data)[0][1]
    
    return {
        'prediction': 'Highly Rated' if prediction == 1 else 'Not Highly Rated',
        'probability': probability
    }

# Example usage
print("\nUnified Recommendation Example:")
print(get_recommendations(sample_df.iloc[50]['title'], method='hybrid'))

print("\nRating Prediction Example:")
example_features = {
    'price': 29.99,
    'isBestSeller': True,
    'discount_pct': 20.0,
    'reviews': 150,
    'popularity_score': 8.5,
    'value_for_money': 0.25
}
print(predict_rating(example_features))