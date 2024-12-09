import os
import json

def list_files(startpath):
    root_dict = {"text": "textures", "children": []}

    for root, dirs, files in os.walk(startpath, topdown=True):
        dirs.sort(key=str.lower)  # Sort directories in-place, case-insensitive
        files.sort(key=str.lower)  # Sort files in-place, case-insensitive

        path = root.split(os.sep)
        current_level = root_dict["children"]
        
        # Walk through path parts and ensure we're at the correct level in the structure
        for part in path[1:]:
            found = False
            # Look for the current part in the children of the current level
            for child in current_level:
                if child["text"].lower() == part.lower():  # Case-insensitive comparison
                    current_level = child.get("children", [])
                    found = True
                    break
            if not found:
                new_child = {"text": part, "children": []}
                current_level.append(new_child)
                current_level = new_child["children"]

        # Append directories first
        for d in dirs:
            dir_dict = {"text": d, "children": []}
            current_level.append(dir_dict)

        # Then append files
        for file in files:
            if not file.startswith('.'):  # Ignore hidden files
                file_dict = {"text": file, "icon": "jstree-file"}
                current_level.append(file_dict)

    return root_dict

textures_path = './textures'  # Adjust this path to your Textures folder
textures_tree = list_files(textures_path)

with open('textures.json', 'w') as f:
    json.dump([textures_tree], f, indent=4)

print('JSON file has been created successfully.')
