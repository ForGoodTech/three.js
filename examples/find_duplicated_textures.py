import os
from collections import defaultdict

def find_duplicate_files(start_path):
    file_paths = defaultdict(list)  # Dictionary to store file paths

    for root, _, files in os.walk(start_path):
        for file in files:
            file_paths[file].append(os.path.join(root, file))

    duplicates = {file: paths for file, paths in file_paths.items() if len(paths) > 1}
    return duplicates

# Replace 'your_directory_path' with the path to the root directory you want to scan
root_directory = '.\\textures'
duplicates = find_duplicate_files(root_directory)

# Print the duplicate files and their paths
for file_name, paths in duplicates.items():
    print(f"Duplicate file: {file_name}")
    for path in paths:
        print(f"    Path: {path}")
    print()
