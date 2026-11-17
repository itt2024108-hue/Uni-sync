<?php 
session_start(); 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Study Planner — Uni-Sync</title>

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
      <a href="planner.php" class="current">Study planner</a>
      <a href="dashboard.php">Dashboard</a>
      <a href="input.php">Add Course</a>
    </div>
    <div class="nav-cta">
    <button id="themeToggleBtn" class="btn-ghost btn me-2" title="Toggle Light/Dark Mode"><i class="bi bi-moon-stars"></i></button>
   <a href="dashboard.php" class="btn-ghost btn d-none d-sm-inline-flex">See the dashboard</a>
    
    <div class="auth-buttons" style="display: flex; gap: 15px; align-items: center; margin-left: auto; padding-left: 20px; border-left: 1px solid rgba(255,255,255,0.1);">
        <?php if(isset($_SESSION['user_id'])): ?>
            
            <!-- PROFILE BUTTON -->
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
</div>

<div id="mobileMenu" class="shell d-none" style="padding-top:1rem; padding-bottom:1rem; border-bottom:1px solid var(--line-soft);">
  <a href="index.php">Home</a>
  <a href="planner.php" class="current">Study planner</a>
  <a href="dashboard.php">Dashboard</a>
</div>

<!-- PAGE HEAD -->
<section class="page-head grid-bg">
  <div class="shell">
    <div class="kicker">The core innovation</div>
    <h1>A rule-based engine, not a mood.</h1>
    <p class="lead">Uni-Sync doesn't guilt you with red "overdue" badges. Miss a session and it runs one deterministic formula to redraw the rest of the week, with a daily cap so no single day absorbs everything.</p>
  </div>
</section>

<!-- COUNTDOWN EXAMPLE -->
<section style="padding-top:1rem;">
  <div class="shell">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6">
        <h3>Every deadline gets a visual countdown</h3>
        <p style="color:var(--muted); max-width:440px;">Abstract dates become something you can feel getting closer, instead of a flat entry on a grid.</p>
      </div>
      <div class="col-lg-6">
        <div class="console" style="padding:1.2rem 1.4rem;">
         <div class="console-label" id="promoCourseName">Loading live data...</div>
<div class="countdown mono" id="miniCountdown" style="gap:0.6rem;">
    <div class="unit"><div class="num" id="promoHd" style="font-size:1.7rem;">00</div><div class="lbl">days</div></div>
    <div class="unit"><div class="num" id="promoHh" style="font-size:1.7rem;">00</div><div class="lbl">hrs</div></div>
    <div class="unit"><div class="num" id="promoHm" style="font-size:1.7rem;">00</div><div class="lbl">min</div></div>
    <div class="unit"><div class="num" id="promoHs" style="font-size:1.7rem;">00</div><div class="lbl">sec</div></div>
</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ENGINE DEMO -->
<!-- WORKLOAD ASSIGNMENT PANEL -->
<section style="padding-top:2rem; padding-bottom:1rem;">
  <div class="shell">
    <div class="console" style="padding:1.5rem;">
      <h3 style="margin-bottom: 1rem; font-size:1.4rem;">Set Course Workload</h3>
      <p style="color:var(--muted); font-size:0.9rem; margin-bottom:1.5rem;">
<div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); padding: 15px; border-radius: 8px;">
    <p style="color: var(--text-main); font-size: 0.9rem; margin-top: 0; margin-bottom: 10px;"><strong>💡 How to calculate your units:</strong></p>
    <ul style="color: var(--muted); font-size: 0.85rem; padding-left: 20px; margin-bottom: 15px; line-height: 1.6;">
        <li><strong>Theory Exams:</strong> 1 Unit = 1 Lecture Topic or 1 Textbook Page.</li>
        <li><strong>Math/Coding Exams:</strong> 1 Unit = 1 Practice Question or 1 Algorithm.</li>
    </ul>
    
    <!-- NEW DEADLINE TIP -->
    <div style="background: rgba(255, 165, 0, 0.1); border-left: 3px solid var(--signal); padding: 10px; margin-bottom: 15px; border-radius: 4px;">
        <span style="color: var(--signal); font-weight: bold; font-size: 0.85rem;">⚠️ Deadline Strategy:</span><br>
        <span style="color: var(--text-main); font-size: 0.85rem;">Do not select your exact exam date. Set your deadline to the <strong>day before the exam</strong> so your workload finishes early!</span>
    </div>

    <p style="color: var(--muted); font-size: 0.8rem; margin-bottom: 0;"><em>Count the total units in your syllabus and enter them below to generate your daily plan.</em></p>
</div>
      
      <form id="workloadForm" class="row g-3 align-items-end">
        <div class="col-md-5">
          <label class="console-label mb-2">Select Course</label>
          <select id="wlCourse" class="form-control bg-dark text-light border-secondary" required>
            <option value="" disabled selected>Loading active courses...</option>
            <!-- Courses will be injected here via JS -->
          </select>
        </div>
        <div class="col-md-4">
          <label class="console-label mb-2">Total Workload (Units)</label>
          <input type="number" id="wlUnits" class="form-control bg-dark text-light border-secondary" required min="1" placeholder="e.g. 120">
        </div>
        <div class="col-md-3">
          <button type="submit" class="btn-signal btn w-100">Save Workload</button>
        </div>
      </form>
      <div id="wlSuccessMsg" class="mt-3 text-success d-none" style="font-size:0.9rem;">
        <i class="bi bi-check-circle-fill me-2"></i> Workload successfully updated!
      </div>
    </div>
  </div>
</section>

<section class="grid-bg">
  <div class="shell">
    <div class="row g-5">
      <div class="col-lg-5">
        <div class="kicker">Try it</div>
        <h2 style="font-size:1.9rem;">Simulate a missed study session.</h2>
        <p style="color:var(--muted); max-width:420px;">This is the exact formula from the Uni-Sync methodology. Monday's block is already done see what happens to the rest of the week if Tuesday gets skipped.</p>
     

        <button id="resetBtn" class="btn-ghost btn" style="display:none;">Reset week</button>
        <div class="engine-note mt-3" id="engineNote">Monday's block is done. 80 units remain across Tuesday–Friday — 20 units a day.</div>
      </div>
      <div class="col-lg-7">
        <div class="engine-panel">
          <div class="console-label mb-2">Remaining week</div>
          <div class="bars" id="engineBars">
            <div class="bar-col"><div class="bar" id="b0" style="height:47%"></div><div class="bar-day">Tue</div><div class="bar-val mono" id="v0">20u</div></div>
            <div class="bar-col"><div class="bar" id="b1" style="height:47%"></div><div class="bar-day">Wed</div><div class="bar-val mono" id="v1">20u</div></div>
            <div class="bar-col"><div class="bar" id="b2" style="height:47%"></div><div class="bar-day">Thu</div><div class="bar-val mono" id="v2">20u</div></div>
            <div class="bar-col"><div class="bar" id="b3" style="height:47%"></div><div class="bar-day">Fri</div><div class="bar-val mono" id="v3">20u</div></div>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-5 steps">
      <div class="step">
        <div class="step-no">Step 1</div>
        <h4>Split the syllabus</h4>
        <p>Total topics and pages are converted into workload units and spread evenly across the days left before the exam.</p>
      </div>
      <div class="step">
        <div class="step-no">Step 2</div>
        <h4>Detect a miss</h4>
        <p>If a planned block is skipped, its workload isn't deleted it's marked as still owed to the plan.</p>
      </div>
      <div class="step">
        <div class="step-no">Step 3</div>
        <h4>Recalculate, with a cap</h4>
        <p>Remaining workload is divided across remaining days. A maximum daily cap stops any single day from absorbing too much.</p>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section>
  <div class="shell">
    <div class="cta-block">
      <h2>See how this looks on your dashboard</h2>
      <p>Countdowns, today's blocks, and coverage all in one screen.</p>
      <a href="dashboard.php" class="btn-signal btn">Open the dashboard preview</a>
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
</body>
</html