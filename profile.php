<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data from the database
$conn = new mysqli("localhost", "root", "", "unisync_db");
$stmt = $conn->prepare("SELECT username, email, profile_pic FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

$current_pic = !empty($user['profile_pic']) ? $user['profile_pic'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile — Uni-Sync</title>
    <style>
        :root {
            --bg-dark: #121212;
            --card-bg: #1e1e1e;
            --text-main: #ffffff;
            --muted: #a0a0a0;
            --signal: #ff9800;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-dark); color: var(--text-main);
            display: flex; justify-content: center; align-items: center; height: 100vh; direction: ltr;
        }
        .profile-card {
            background-color: var(--card-bg); padding: 40px; border-radius: 12px; width: 100%; max-width: 420px;
            border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6); text-align: center;
        }
        h2 { color: var(--text-main); margin-top: 0; margin-bottom: 25px; text-align: center; }
        .form-group { margin-bottom: 20px; text-align: left; }
        label.input-label { display: block; color: var(--muted); font-size: 0.85rem; margin-bottom: 6px; text-align: left; }
        
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%; padding: 12px; border-radius: 6px; border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.2); color: var(--text-main); font-size: 0.95rem; text-align: left;
        }
        input:focus { outline: none; border-color: var(--signal); }
        
        .save-btn {
            background-color: var(--signal); color: #000; width: 100%; padding: 12px; border: none;
            border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 1rem; margin-top: 10px; transition: 0.2s;
        }
        .save-btn:hover { opacity: 0.85; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: var(--muted); font-size: 0.8rem; text-decoration: none; }
        .back-link:hover { color: var(--text-main); }
    </style>
</head>
<body>

    <div class="profile-card">
        <h2>Edit Profile</h2>
        
        <!-- Added onsubmit check to the form -->
        <form action="backend/update_profile.php" method="POST" enctype="multipart/form-data" onsubmit="return checkPasswords()">
            
            <!-- Avatar Section -->
            <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 25px;">
                <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.05); border: 2px dashed rgba(255,255,255,0.2); display: flex; justify-content: center; align-items: center; margin-bottom: 10px; overflow: hidden;">
                    
                    <img id="avatar-preview" 
                         src="<?php echo $current_pic ? htmlspecialchars($current_pic) : ''; ?>" 
                         style="width: 100%; height: 100%; object-fit: cover; display: <?php echo $current_pic ? 'block' : 'none'; ?>;">
                    
                    <svg id="avatar-placeholder" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--muted); display: <?php echo $current_pic ? 'none' : 'block'; ?>;">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                
                <label style="color: var(--signal); font-size: 0.85rem; font-weight: 600; cursor: pointer;">
                    Change Picture
                    <input type="file" name="profile_pic" accept="image/*" style="display: none;" onchange="previewImage(event)">
                </label>
            </div>

            <!-- Username Field -->
            <div class="form-group">
                <label class="input-label">Username</label>
                <input type="text" name="username" placeholder="Update username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>">
            </div>
            
            <!-- Email Field -->
            <div class="form-group">
                <label class="input-label">Email</label>
                <input type="email" name="email" placeholder="Update email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
            </div>
            
            <!-- Cleaned Up New Password Field -->
            <div class="form-group">
                <label class="input-label">New Password</label>
                <input type="password" name="new_password" id="new_pw" placeholder="Enter new password">
            </div>

            <!-- Added Confirm Password Field -->
            <div class="form-group">
                <label class="input-label">Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_pw" placeholder="Confirm new password">
            </div>
            
            <button type="submit" class="save-btn">Save Changes</button>
        </form>
        
        <a href="dashboard.php" class="back-link">&larr; Back to Dashboard</a>
    </div>

    <!-- JavaScript to preview image AND validate passwords -->
    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                    document.getElementById('avatar-preview').style.display = 'block';
                    document.getElementById('avatar-placeholder').style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        }

        // New function to check if passwords match before saving
        function checkPasswords() {
            const p1 = document.getElementById('new_pw').value;
            const p2 = document.getElementById('confirm_pw').value;
            
            // If they typed something in New Password, but it doesn't match Confirm
            if (p1 !== '' && p1 !== p2) {
                alert("Passwords do not match! Please re-type them.");
                return false; // Blocks the form from saving
            }
            return true; // Allows the form to save
        }
    </script>
</body>
</html>