import os
import glob
from PIL import Image

def compress_images():
    slideshow_dir = "photos/slideshow"
    print(f"Scanning {slideshow_dir} for images...")
    
    # Supported input patterns
    patterns = ["*.png", "*.jpg", "*.jpeg"]
    files = []
    for p in patterns:
        files.extend(glob.glob(os.path.join(slideshow_dir, p)))
        
    print(f"Found {len(files)} files to process.")
    
    saved_bytes = 0
    
    for f in files:
        base, ext = os.path.splitext(f)
        webp_path = base + ".webp"
        
        orig_size = os.path.getsize(f)
        
        try:
            with Image.open(f) as img:
                # Convert to RGB if it's RGBA and saving as jpeg/webp if needed
                # WebP supports alpha channel, so we can save direct
                img.save(webp_path, "WEBP", quality=80, optimize=True)
                
            webp_size = os.path.getsize(webp_path)
            diff = orig_size - webp_size
            saved_bytes += diff
            reduction = (diff / orig_size) * 100
            
            print(f"Compressed {os.path.basename(f)}:")
            print(f"  Original: {orig_size / 1024:.1f} KB")
            print(f"  WebP:     {webp_size / 1024:.1f} KB ({reduction:.1f}% reduction)")
            
        except Exception as e:
            print(f"Error processing {f}: {e}")
            
    print("-" * 50)
    print(f"Total size reduction: {saved_bytes / (1024 * 1024):.2f} MB")

if __name__ == "__main__":
    compress_images()
