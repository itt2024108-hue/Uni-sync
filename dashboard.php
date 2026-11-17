<?php 
session_start(); 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'backend/db.php';

$today = date('Y-m-d');
$user_id = $_SESSION['user_id']; 

$daily_stmt = $conn->prepare("SELECT course_code, status FROM study_units WHERE user_id = ? AND assigned_date = ?");
$daily_stmt->bind_param("is", $user_id, $today);
$daily_stmt->execute();
$daily_result = $daily_stmt->get_result();

$todays_units = [];
$total_today = 0;
$completed_today = 0;

while ($row = $daily_result->fetch_assoc()) {
    $todays_units[] = $row;
    $total_today++;
    if ($row['status'] === 'completed') {
        $completed_today++;
    }
}
$daily_percentage = ($total_today > 0) ? round(($completed_today / $total_today) * 100) : 0;
$daily_stmt->close();

// --- 2. CALCULATE MASTER SYLLABUS COVERAGE ---
$master_stmt = $conn->prepare("SELECT course_code, status FROM study_units WHERE user_id = ?");
$master_stmt->bind_param("i", $user_id);
$master_stmt->execute();
$master_result = $master_stmt->get_result();

$course_progress = [];
$course_stats = []; 
$grand_total = 0;
$grand_completed = 0;

while ($row = $master_result->fetch_assoc()) {
    $code = $row['course_code'];
    
    if (!isset($course_stats[$code])) {
        $course_stats[$code] = ['total' => 0, 'completed' => 0];
    }
    
    $course_stats[$code]['total']++;
    $grand_total++;
    
    if ($row['status'] === 'completed') {
        $course_stats[$code]['completed']++;
        $grand_completed++;
    }
}
$master_stmt->close();

foreach ($course_stats as $code => $stats) {
    $course_progress[$code] = round(($stats['completed'] / $stats['total']) * 100);
}
$overall_percentage = ($grand_total > 0) ? round(($grand_completed / $grand_total) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Uni-Sync</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css?v=2">

</head>
<body>

<!-- NAV -->
<div class="nav-wrap">
  <div class="shell">
    <a href="index.php" class="brand"><span class="pulse"></span> Uni-Sync</a>
    <div class="nav-links">
      <a href="index.php">Home</a>
      <a href="planner.php">Study planner</a>
      <a href="dashboard.php" class="current">Dashboard</a>
      <a href="input.php">Add Course</a>
    </div>
    <div class="nav-cta">
    <button id="themeToggleBtn" class="btn-ghost btn me-2" title="Toggle Light/Dark Mode"><i class="bi bi-moon-stars"></i></button>

    <a href="planner.php" class="btn-ghost btn d-none d-sm-inline-flex">study planner</a>
    
    <div class="auth-buttons" style="display: flex; gap: 15px; align-items: center; margin-left: auto; padding-left: 20px; border-left: 1px solid rgba(255,255,255,0.1);">
        <?php if(isset($_SESSION['user_id'])): ?>
            
            <!-- PROFILE BUTTON -->
            <a href="profile.php" style="display: flex; align-items: center; gap: 8px; color: var(--text-main); text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: 0.2s; padding-right: 10px;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.05); display: flex; justify-content: center; align-items: center; border: 1px solid rgba(255,255,255,0.2); overflow: hidden;">
                    <?php
                    // Fetch profile pic if not already in session
                    if (!isset($_SESSION['profile_pic']) && isset($_SESSION['user_id'])) {
                        $db_conn = new mysqli("localhost", "root", "", "unisync_db");
                        $res = $db_conn->query("SELECT profile_pic FROM users WHERE id = " . $_SESSION['user_id']);
                        if ($res && $row = $res->fetch_assoc()) {
                            $_SESSION['profile_pic'] = $row['profile_pic'];
                        }
                        $db_conn->close();
                    }
                    
                    if (!empty($_SESSION['profile_pic'])): ?>
                        <img src="<?php echo htmlspecialchars($_SESSION['profile_pic']); ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--muted);"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <?php endif; ?>
                </div>
                Profile
            </a>

            <!-- LOG OUT BUTTON -->
            <a href="backend/logout.php" style="background: var(--signal); color: #000; padding: 6px 16px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 0.9rem; transition: 0.2s;">Log Out</a>
            
        <?php else: ?>
            
            <!-- LOGGED OUT STATE -->
            <a href="login.php" style="color: var(--text-main); text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: 0.2s;">Log In</a>
            <a href="signup.php" style="background: var(--signal); color: #000; padding: 6px 16px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 0.9rem; transition: 0.2s;">Sign Up</a>
            
        <?php endif; ?>
    </div>
    </div>
  </div>
</div>

<div id="mobileMenu" class="shell d-none" style="padding-top:1rem; padding-bottom:1rem; border-bottom:1px solid var(--line-soft);">
  <a href="index.php">Home</a>
  <a href="planner.php">Study planner</a>
  <a href="dashboard.php" class="current">Dashboard</a>
</div>

<!-- PAGE HEAD -->
<section class="page-head grid-bg">
  <div class="shell">
    <div class="kicker">What it looks like day to day</div>
    <h1>One screen for exams, deadlines, and today's plan.</h1>
    <p class="lead">Everything Uni-Sync tracks, rendered as a single command center instead of three separate apps.</p>
  </div>
</section>

<!-- DASHBOARD COMMAND CENTER -->
<section style="padding-top:1rem;">
  <div class="shell">
    <div class="browser-frame">
      <div class="browser-bar">
        <span class="dot" style="background:#3a4457;"></span>
        <span class="dot" style="background:#3a4457;"></span>
        <span class="dot" style="background:#3a4457;"></span>
      </div>
      
      <div class="row g-5 align-items-start" style="padding: 20px;">
          
          <!-- LEFT SIDE: PLAN & PROGRESS -->
          <div class="col-lg-6">
              
              <!-- Today's Plan Box -->
              <div class="shell" style="padding: 20px; margin-bottom: 20px;">
                  <h3 style="color: var(--text-main); margin-bottom: 15px;">
                      Today's Plan <span style="color: var(--signal); font-size: 0.85rem;">(<?php echo isset($daily_percentage) ? $daily_percentage : 0; ?>% Done)</span>
                  </h3>
                  <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                      <?php if (empty($todays_units)): ?>
                          <span style="color: var(--muted); font-size: 0.85rem;">No units scheduled for today!</span>
                      <?php else: ?>
                          <?php foreach ($todays_units as $unit): ?>
    <?php if (!isset($unit['status']) || $unit['status'] !== 'completed'): ?>
        <div class="badge p-2 bg-dark text-light border border-secondary">
            <?php echo htmlspecialchars($unit['course_code']); ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
                      <?php endif; ?>
                  </div>
              </div>

              <!-- Overall Syllabus Coverage Box -->
              <div class="shell" style="padding: 20px;">
                  <h3 style="color: var(--text-main); margin-bottom: 15px; font-size: 1.1rem;">Overall Syllabus Coverage</h3>
                  <?php if (empty($course_progress)): ?>
                      <span style="color: var(--muted); font-size: 0.85rem;">No courses tracked yet.</span>
                  <?php else: ?>
                      <?php foreach ($course_progress as $course_name => $percentage): ?>
                          <div style="margin-bottom: 15px;">
                              <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: var(--text-main); margin-bottom: 5px;">
                                  <span><?php echo htmlspecialchars($course_name); ?></span>
                                  <span style="color: var(--muted);"><?php echo htmlspecialchars($percentage); ?>%</span>
                              </div>
                              <div class="progress" style="height: 6px; background-color: #222;">
                                  <div class="progress-bar bg-signal" role="progressbar" style="width: <?php echo htmlspecialchars($percentage); ?>%;"></div>
                              </div>
                          </div>
                      <?php endforeach; ?>
                  <?php endif; ?>
              </div>
              
          </div>

          <!-- RIGHT SIDE: LOGGING & RING -->
          <div class="col-lg-6">
              
              <!-- Syllabus Coverage Ring -->
              <div class="card-mini" style="margin-bottom: 20px;">
                  <h5>Syllabus coverage</h5>
                  <div class="progress-ring" id="progressRing" style="background:conic-gradient(var(--go) <?php echo $overall_percentage; ?>% 0%, var(--line-soft) <?php echo $overall_percentage; ?>% 100%); transition: background 0.5s ease;">
                      <div class="ring-inner"><span class="center" id="progressText" style="font-weight: 600;"><?php echo $overall_percentage; ?>%</span></div>
                  </div>
              </div>
              <!-- Log Completed Work Form -->
              <div class="card-mini wide">
                  <h5>Log Completed Work</h5>
                  <p style="color:var(--muted); font-size:0.85rem; margin-bottom: 0.5rem;">Select a course and enter the units you finished today.</p>
                  
                  <form id="progressForm" action="backend/mark_completed.php" method="POST" class="d-flex flex-wrap align-items-center" style="gap:10px;">
                      <select id="progcourse" name="courseName" class="form-control bg-dark text-light border-secondary flex-grow-1" required>
                          <option value="" disabled selected>Select a course...</option>
                          <?php 
                              if (!empty($course_progress)) {
                                  foreach (array_keys($course_progress) as $code) {
                                      echo '<option value="' . htmlspecialchars($code) . '">' . htmlspecialchars($code) . '</option>';
                                  }
                              }
                          ?>
                      </select>
                      
                      <input type="number" id="progunits" name="units" class="form-control bg-dark text-light border-secondary" placeholder="Units" style="max-width: 150px;" required>
                      
                      <button type="submit" class="btn-signal btn"><i class="bi bi-check2-square"></i> Log</button>
                  </form>
              </div>
          <button type="button" onclick="deleteSelectedCourse()" class="btn btn-warning">
    Delete Course
</button>
              
          </div>
      </div>
    </div>
  </div>
</section>

<!-- LIVE COUNTDOWN CARD -->
<section>
  <div class="shell">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6">
        <h3>Countdown detail view</h3>
        <p style="color:var(--muted); max-width:440px;">Tapping any item on the dashboard opens its live countdown, ticking down to the exact minute.</p>
      </div>
      <div class="col-lg-6">
        <div class="console" style="padding:1.2rem 1.4rem;">
          <div class="console-label" id="dashCountdownTitle">Loading live data...</div>
          <div class="countdown mono" id="miniCountdown" style="gap:0.6rem; margin-bottom: 1rem;">
              <div class="unit"><div class="num" id="dashHd" style="font-size:1.7rem;">00</div><div class="lbl">days</div></div>
              <div class="unit"><div class="num" id="dashHh" style="font-size:1.7rem;">00</div><div class="lbl">hrs</div></div>
              <div class="unit"><div class="num" id="dashHm" style="font-size:1.7rem;">00</div><div class="lbl">min</div></div>
              <div class="unit"><div class="num" id="dashHs" style="font-size:1.7rem;">00</div><div class="lbl">sec</div></div>
          </div>
          <table style="width: 100%; margin-top: 1rem;">
              <tbody id="dynamicCourseTableBody">
                  <!-- JavaScript will inject the table rows (<tr>) here -->
              </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section>
  <div class="shell">
    <div class="cta-block">
      <h2>Ready to see the recalibration engine?</h2>
      <p>Simulate a missed session and watch the plan rebuild itself.</p>
      <a href="planner.php" class="btn-signal btn">Try the study planner</a>
    </div>
  </div>
</section>

<footer>
  <div class="shell">
    <div class="brand"><span class="pulse"></span> Uni-Sync</div>
    <div class="meta">
      Group 06 — The Uni Sync<br>
      Supervisor: Mr. Nandika Tennakoon, ITT Department
    </div>
    <div class="meta">&copy; 2026 Uni-Sync. Built for Rajarata University of Sri Lanka.</div>
  </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
<script>
// This runs a split-second after your page loads to overpower the bad inline styles
document.addEventListener("DOMContentLoaded", function() {
    setTimeout(function() {
        if (document.body.classList.contains('light-theme') || document.body.classList.contains('light-mode')) {
            
            // 1. Force the floating 9% to get its blue and gray ring back
            let ring = document.getElementById('progressRing');
            if (ring) {
                // This replaces the flat white background your JS was injecting
                ring.style.setProperty('background', 'conic-gradient(#0ea5e9 var(--progress, 9%), #E2E8F0 0)', 'important');
            }
            
            // 2. Force the stubborn dark headers to become Light Gray
            // This targets the headers even if we don't know their exact class name
            let headers = document.querySelectorAll('.console-head, .mac-header, .card-mini > div:first-child, .console > div:first-child');
            headers.forEach(function(header) {
                header.style.setProperty('background-color', '#F8FAFC', 'important');
                header.style.setProperty('border-bottom', '1px solid #E2E8F0', 'important');
                
                // Fix the text and dots inside the header
                let texts = header.querySelectorAll('*');
                texts.forEach(function(text) {
                    text.style.setProperty('color', '#64748B', 'important');
                });
            });
        }
    }, 300); // 300ms delay ensures it fires last
});

function deleteSelectedCourse() {
    // 1. Grab the dropdown
    const dropdown = document.getElementById("progcourse"); 
    
    if (!dropdown) {
        alert("Could not find the dropdown menu. Check the ID.");
        return;
    }
    
    // 2. Get the selected course code
    const courseCode = dropdown.value;

    if (!courseCode) {
        alert("Please select a course from the dropdown first!");
        return;
    }

    // 3. Ask for confirmation
    if (confirm("Are you sure you want to delete this course (" + courseCode + ")?")) {
        const formData = new FormData();
        formData.append('course_code', courseCode); 

        // 4. Send the delete request to the backend
        fetch('backend/delete_course.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text()) 
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.status === 'success') {
                    // 5. Wipe the browser memory and reload!
                    localStorage.clear();
                    sessionStorage.clear();
                    location.reload(); 
                } else {
                    alert("Error: " + data.message);
                }
            } catch(err) {
                alert("Server error details:\n\n" + text);
                console.error("Raw server response:", text);
            }
        })
        .catch(err => alert("Network error: Could not reach the server."));
    }
}
</script>
</body>
</html>