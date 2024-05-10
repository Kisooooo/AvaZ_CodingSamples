import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
from sklearn.cluster import KMeans

# Read the company names from a text file containing pre-extracted names from the master data
file_path = 'C:\\Users\\ava_y\\OneDrive\\Desktop\\VAST\\MC3\\MC3\\company_names.txt'

def read_company_names(file_path):
    with open(file_path, 'r') as file:
        company_names = file.read().splitlines()
    return company_names

# Preprocess the company names
def preprocess_company_names(company_names):
    preprocessed_names = []
    for name in company_names:
        # Convert to lowercase and remove special characters
        processed_name = name.lower().replace(',', '').replace('.', '')
        preprocessed_names.append(processed_name)
    return preprocessed_names

# Perform clustering on company names
def cluster_company_names(company_names, num_clusters):
    # Convert company names to numerical features using TF-IDF
    vectorizer = TfidfVectorizer()
    features = vectorizer.fit_transform(company_names)

    # Compute pairwise cosine similarity
    similarity_matrix = cosine_similarity(features)

    # Perform clustering using K-means
    kmeans = KMeans(n_clusters=num_clusters, random_state=42)
    kmeans.fit(similarity_matrix)

    return kmeans.labels_

# Write clustered company names to a text file
def write_clustered_names(company_names, clusters, output_file):
    data = {'Company Name': company_names, 'Cluster': clusters}
    df = pd.DataFrame(data)
    df.to_csv(output_file, index=False)

# Main function
def main():
    input_file = 'company_names.txt'
    output_file = 'clustered_names.txt'
    num_clusters = 5

    # Read company names from input file
    company_names = read_company_names(input_file)

    # Preprocess company names
    preprocessed_names = preprocess_company_names(company_names)

    # Cluster company names
    clusters = cluster_company_names(preprocessed_names, num_clusters)

    # Write clustered names to output file
    write_clustered_names(company_names, clusters, output_file)

    print(f"Clustered names written to {output_file}.")

if __name__ == '__main__':
    main()
