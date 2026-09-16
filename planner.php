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
<link rel="stylesheet" href="css/style.css">
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
      <a href="dashboard.php" class="btn-ghost btn d-none d-sm-inline-flex">See the dashboard</a>
      <a href="index.php" class="btn-signal btn">Get started</a>
      <button class="nav-toggle" id="navToggle"><i class="bi bi-list"></i></button>
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
          <div class="console-label">Assignment 3 — Database Systems</div>
          <div class="countdown mono" id="miniCountdown" style="gap:0.6rem;">
            <div class="unit"><div class="num" style="font-size:1.7rem;" id="md">00</div><div class="lbl">days</div></div>
            <div class="unit"><div class="num" style="font-size:1.7rem;" id="mh">00</div><div class="lbl">hrs</div></div>
            <div class="unit"><div class="num" style="font-size:1.7rem;" id="mm">00</div><div class="lbl">min</div></div>
            <div class="unit"><div class="num" style="font-size:1.7rem;" id="ms">00</div><div class="lbl">sec</div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ENGINE DEMO -->
<section class="grid-bg">
  <div class="shell">
    <div class="row g-5">
      <div class="col-lg-5">
        <div class="kicker">Try it</div>
        <h2 style="font-size:1.9rem;">Simulate a missed study session.</h2>
        <p style="color:var(--muted); max-width:420px;">This is the exact formula from the Uni-Sync methodology. Monday's block is already done — see what happens to the rest of the week if Tuesday gets skipped.</p>
        <div class="formula mono">New Daily Load = Remaining Workload &divide; Remaining Days</div>
        <button id="missBtn" class="btn-signal btn">Simulate a missed session (Tuesday)</button>

        <button id="resetBtn" class="btn-ghost btn" style="display:none;">Reset week</button>
        <div class="engine-note mt-3" id="engineNote">Monday's block is done. 80 units remain across Tuesday–Friday — 20 units a day.</div>
      </div>
      <div class="col-lg-7">
        <div class="engine-panel">
          <div class="console-label mb-2">Database Systems — remaining week</div>
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
        <p>If a planned block is skipped, its workload isn't deleted — it's marked as still owed to the plan.</p>
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