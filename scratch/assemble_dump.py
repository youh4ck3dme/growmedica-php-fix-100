import json
import re
import sys

files = [
    "/Users/erikbabcan/.gemini/antigravity/brain/1d0bf9b2-38e8-47d8-af5c-7fe49e10b599/.system_generated/steps/460/output.txt",
    "/Users/erikbabcan/.gemini/antigravity/brain/1d0bf9b2-38e8-47d8-af5c-7fe49e10b599/.system_generated/steps/466/output.txt",
    "/Users/erikbabcan/.gemini/antigravity/brain/1d0bf9b2-38e8-47d8-af5c-7fe49e10b599/.system_generated/steps/468/output.txt",
    "/Users/erikbabcan/.gemini/antigravity/brain/1d0bf9b2-38e8-47d8-af5c-7fe49e10b599/.system_generated/steps/470/output.txt",
    "/Users/erikbabcan/.gemini/antigravity/brain/1d0bf9b2-38e8-47d8-af5c-7fe49e10b599/.system_generated/steps/472/output.txt",
    "/Users/erikbabcan/.gemini/antigravity/brain/1d0bf9b2-38e8-47d8-af5c-7fe49e10b599/.system_generated/steps/474/output.txt",
    "/Users/erikbabcan/.gemini/antigravity/brain/1d0bf9b2-38e8-47d8-af5c-7fe49e10b599/.system_generated/steps/476/output.txt",
    "/Users/erikbabcan/.gemini/antigravity/brain/1d0bf9b2-38e8-47d8-af5c-7fe49e10b599/.system_generated/steps/478/output.txt"
]

with open('/Users/erikbabcan/Downloads/c1growmedical-full-web/dump.sql', 'w', encoding='utf-8') as outfile:
    for idx, filepath in enumerate(files):
        print(f"Processing {filepath}...")
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Match from ```json to ```
        match = re.search(r'```json\n(.*)\n```', content, re.DOTALL)
        if not match:
            print(f"Error: Could not find JSON block in {filepath}")
            sys.exit(1)
        
        json_str = match.group(1).strip()
        try:
            chunk_data = json.loads(json_str)
        except Exception as e:
            print(f"JSON load failed on file {filepath}: {e}")
            sys.exit(1)
            
        outfile.write(chunk_data)
        print(f"Wrote chunk {idx} successfully.")

print("Assembled dump.sql successfully!")
