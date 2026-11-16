<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In — Uni-Sync</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #121418; /* Deep dark background */
            color: #ffffff;
            font-family: system-ui, -apple-system, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .auth-card {
            background-color: #1e2128; /* Dashboard card color */
            padding: 40px;
            border-radius: 12px;
            width: 100%;
            max-width: 380px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
        }
        .auth-label {
            color: #94a3b8; /* Muted text */
            font-size: 0.85rem;
            display: block;
        }
        .auth-input {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.2);
            color: #fff;
            box-sizing: border-box;
            margin-bottom: 15px;
            margin-top: 6px;
            font-size: 0.95rem;
        }
        .auth-input:focus {
            outline: none;
            border-color: #ff9800; /* Signal orange highlight */
        }
        .auth-btn {
            background-color: #ff9800; /* Signal orange */
            color: #000;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 10px;
            transition: 0.2s;
        }
        .auth-btn:hover {
            background-color: #e68900;
        }
        .auth-link {
            color: #ff9800;
            text-decoration: none;
        }
        .auth-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="auth-card">
        <h2 style="margin-top: 0; margin-bottom: 25px; text-align: center;">Welcome back</h2>
        
        <form action="backend/process_login.php" method="POST">
            <label class="auth-label">Email or Username</label>
            <input type="text" name="username" class="auth-input" required>
            
            <label class="auth-label">Password</label>
            <input type="password" name="password" class="auth-input" required>
            
            <button type="submit" class="auth-btn">Log In</button>
        </form>
        
        <p style="color: #94a3b8; font-size: 0.85rem; text-align: center; margin-top: 25px;">
            Don't have an account? <a href="signup.php" class="auth-link">Sign up</a>
        </p>
        <div style="text-align: center; margin-top: 15px;">
            <a href="index.php" style="color: #64748b; font-size: 0.8rem; text-decoration: none;">&larr; Back to Home</a>
        </div>
    </div>

</body>
</html>