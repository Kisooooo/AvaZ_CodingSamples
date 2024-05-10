# Data processing sample: clustering similar company names to identify potential dummy companies in the master dataset

import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
from sklearn.cluster import KMeans

# Read the company names from a text file containing names from the original data extracted earlier
file_path = 'C:\\Users\\...\\company_names.txt'

def read_company_names(file_path):
    with open(file_path, 'r') as file:
        company_names = file.read().splitlines()
    return company_names

# Preprocess the company names (change to lower case, remove special characters) for easier comparison
def preprocess_company_names(company_names):
    preprocessed_names = []
    for name in company_names:
        processed_name = name.lower().replace(',', '').replace('.', '')
        preprocessed_names.append(processed_name)
    return preprocessed_names

# Perform clustering on company names by converting company names to numerical features using TF-IDF
def cluster_company_names(company_names, num_clusters):

    vectorizer = TfidfVectorizer()
    features = vectorizer.fit_transform(company_names)

    # Compute pairwise cosine similarity and cluster using k-means
    similarity_matrix = cosine_similarity(features)
    kmeans = KMeans(n_clusters=num_clusters, random_state=42)
    kmeans.fit(similarity_matrix)

    return kmeans.labels_

# Write clustered company names to dataframe in a csv file
def write_clustered_names(company_names, clusters, output_file):
    data = {'Company Name': company_names, 'Cluster': clusters}
    df = pd.DataFrame(data)
    df.to_csv(output_file, index=False)

# Main function
def main():
    input_file = 'company_names.txt'
    output_file = 'clustered_names.txt'
    num_clusters = 5

    company_names = read_company_names(input_file)
    preprocessed_names = preprocess_company_names(company_names)
    clusters = cluster_company_names(preprocessed_names, num_clusters)
    write_clustered_names(company_names, clusters, output_file)
    print(f"Clustered names written to {output_file}.")

if __name__ == '__main__':
    main()
