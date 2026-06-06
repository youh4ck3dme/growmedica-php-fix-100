from http.server import HTTPServer, BaseHTTPRequestHandler
import sys

class SaveHandler(BaseHTTPRequestHandler):
    def do_OPTIONS(self):
        self.send_response(200)
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'POST, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type')
        self.end_headers()

    def do_POST(self):
        content_length = int(self.headers['Content-Length'])
        print(f"Receiving {content_length} bytes...", flush=True)
        
        chunk_size = 1024 * 1024
        remaining = content_length
        
        with open('/Users/erikbabcan/Downloads/c1growmedical-full-web/dump.sql', 'wb') as f:
            while remaining > 0:
                to_read = min(remaining, chunk_size)
                chunk = self.rfile.read(to_read)
                if not chunk:
                    break
                f.write(chunk)
                remaining -= len(chunk)
                
        self.send_response(200)
        self.send_header('Access-Control-Allow-Origin', '*')
        self.end_headers()
        self.wfile.write(b"Saved")
        print("Done saving!", flush=True)
        sys.exit(0)

server = HTTPServer(('127.0.0.1', 9999), SaveHandler)
print("Server started on port 9999...", flush=True)
server.serve_forever()
