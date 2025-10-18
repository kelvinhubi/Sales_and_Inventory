<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .btn {
            background: #007cba;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #005a87;
        }
        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .output {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            font-family: monospace;
            white-space: pre-wrap;
            max-height: 400px;
            overflow-y: auto;
        }
        .success {
            color: #28a745;
        }
        .error {
            color: #dc3545;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Laravel Database Setup</h1>
        <p>This tool will help you set up your database on InfinityFree hosting.</p>
        
        <div class="warning">
            <strong>⚠️ Security Warning:</strong> 
            Remove this setup route from your routes/web.php file after completing the setup!
        </div>

        <button id="setupBtn" class="btn" onclick="runSetup()">
            🔧 Run Database Setup
        </button>
        
        <button id="testBtn" class="btn" onclick="testConnection()">
            🔍 Test Database Connection
        </button>

        <div id="output" class="output" style="display: none;"></div>
    </div>

    <script>
        async function runSetup() {
            const btn = document.getElementById('setupBtn');
            const output = document.getElementById('output');
            
            btn.disabled = true;
            btn.textContent = '⏳ Setting up...';
            output.style.display = 'block';
            output.textContent = 'Starting database setup...\n';

            try {
                const token = '{{ request()->route("token") }}';
                const response = await fetch(`/setup-database/${token}`);
                const data = await response.json();
                
                if (data.status === 'success') {
                    output.className = 'output success';
                    output.textContent = data.output.join('\n');
                } else {
                    output.className = 'output error';
                    output.textContent = data.output.join('\n');
                }
            } catch (error) {
                output.className = 'output error';
                output.textContent = 'Error: ' + error.message;
            } finally {
                btn.disabled = false;
                btn.textContent = '🔧 Run Database Setup';
            }
        }

        async function testConnection() {
            const btn = document.getElementById('testBtn');
            const output = document.getElementById('output');
            
            btn.disabled = true;
            btn.textContent = '⏳ Testing...';
            output.style.display = 'block';
            output.textContent = 'Testing database connection...\n';

            try {
                // Simple test by making a request to our setup endpoint
                const token = '{{ request()->route("token") }}';
                const response = await fetch(`/setup-database/${token}`);
                
                if (response.ok) {
                    output.className = 'output success';
                    output.textContent = '✅ Connection test successful!\n\nYou can now run the full database setup.';
                } else {
                    output.className = 'output error';
                    output.textContent = '❌ Connection test failed. Check your database configuration.';
                }
            } catch (error) {
                output.className = 'output error';
                output.textContent = '❌ Connection test failed: ' + error.message;
            } finally {
                btn.disabled = false;
                btn.textContent = '🔍 Test Database Connection';
            }
        }
    </script>
</body>
</html>