<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
            min-height: 100vh;
            margin: 0;
            padding: 25px;
        }
        
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            padding: 25px;
            border-radius: 18px;
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(212, 175, 55, 0.3);
        }
        
        h1 {
        
        @keyframes sparkle {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
            background: linear-gradient(90deg, #FFD700 0%, #4FC3F7 25%, #FFD700 50%, #64B5F6 75%, #FFD700 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            animation: sparkle 3s linear infinite;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: sparkle 3s linear infinite;
            font-size: 1.8rem;
            font-weight: 800;
            margin: 0 0 15px 0;
            text-shadow: 0 2px 8px rgba(212, 175, 55, 0.2);
        }
        
        p {
            color: #B8B8B8;
            font-size: 14px;
            margin: 0;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Admin Dashboard</h1>
        
        @keyframes sparkle {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
        <p>BendlessTech platform administration.</p>
    </div>
</body>
</html>
