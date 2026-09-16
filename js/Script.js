// ---------- mobile nav ----------
document.addEventListener('DOMContentLoaded', function () {
  var navToggle = document.getElementById('navToggle');
  var mobileMenu = document.getElementById('mobileMenu');
  if (navToggle && mobileMenu) {
    navToggle.addEventListener('click', function () {
      mobileMenu.classList.toggle('d-none');
    });
  }

  initCountdowns();
  initEngineDemo();
});

// ---------- live countdowns ----------
function initCountdowns() {
  function makeCountdown(targetTime, ids) {
    var dEl = document.getElementById(ids.d);
    var hEl = document.getElementById(ids.h);
    var mEl = document.getElementById(ids.m);
    var sEl = document.getElementById(ids.s);
    if (!dEl || !hEl || !mEl || !sEl) return;

    function tick() {
      var now = new Date().getTime();
      var diff = targetTime - now;
      if (diff < 0) diff = 0;
      var d = Math.floor(diff / (1000 * 60 * 60 * 24));
      var h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      var m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      var s = Math.floor((diff % (1000 * 60)) / 1000);
      dEl.textContent = String(d).padStart(2, '0');
      hEl.textContent = String(h).padStart(2, '0');
      mEl.textContent = String(m).padStart(2, '0');
      sEl.textContent = String(s).padStart(2, '0');
    }
    tick();
    setInterval(tick, 1000);
  }

  // Hero countdown (index.html): 12 days 4 hours from now
  var heroTarget = new Date().getTime() + (12 * 24 * 60 * 60 * 1000) + (4 * 60 * 60 * 1000);
  makeCountdown(heroTarget, { d: 'hd', h: 'hh', m: 'hm', s: 'hs' });

  // Secondary countdown (planner.html or dashboard.html): 3 days 11 hours from now
  var miniTarget = new Date().getTime() + (3 * 24 * 60 * 60 * 1000) + (11 * 60 * 60 * 1000);
  makeCountdown(miniTarget, { d: 'md', h: 'mh', m: 'mm', s: 'ms' });
}

// ---------- recalibration engine demo (planner.html) ----------
function initEngineDemo() {
  var missBtn = document.getElementById('missBtn');
  var resetBtn = document.getElementById('resetBtn');
  var note = document.getElementById('engineNote');
  if (!missBtn || !resetBtn || !note) return;

  var bars = [
    { bar: 'b0', val: 'v0' },
    { bar: 'b1', val: 'v1' },
    { bar: 'b2', val: 'v2' },
    { bar: 'b3', val: 'v3' }
  ];

  function setBars(load, missedIndex) {
    var pct = Math.min(90, (load / 40) * 90);
    bars.forEach(function (b, i) {
      var elBar = document.getElementById(b.bar);
      var elVal = document.getElementById(b.val);
      if (!elBar || !elVal) return;
      if (i === missedIndex) {
        elBar.style.height = '10%';
        elBar.classList.add('missed');
        elVal.textContent = 'missed';
      } else {
        elBar.style.height = pct + '%';
        elBar.classList.remove('missed');
        elVal.textContent = Math.round(load) + 'u';
      }
    });
  }

  missBtn.addEventListener('click', function () {
    var remainingWorkload = 80; // units still owed across Wed-Fri after Tuesday is missed
    var remainingDays = 3;
    var newLoad = remainingWorkload / remainingDays;
    setBars(newLoad, 0);
    note.textContent = 'Tuesday missed. 80 units remain across Wed\u2013Fri \u2014 recalculated to ' + newLoad.toFixed(1) + ' units a day.';
    missBtn.style.display = 'none';
    resetBtn.style.display = 'inline-block';
  });

  resetBtn.addEventListener('click', function () {
    setBars(20, -1);
    note.textContent = "Monday's block is done. 80 units remain across Tuesday\u2013Friday \u2014 20 units a day.";
    missBtn.style.display = 'inline-block';
    resetBtn.style.display = 'none';
  });
}
// ---------- Add Course Form Logic (PHP Backend) ----------
document.addEventListener('DOMContentLoaded', function () {
  var courseForm = document.getElementById('courseForm');
  var formSuccess = document.getElementById('formSuccess');

  if (courseForm) {
    courseForm.addEventListener('submit', function (e) {
      e.preventDefault(); // Stop the page from reloading
      
      // Package the form data
      var formData = new FormData();
      formData.append('courseName', document.getElementById('courseName').value);
      formData.append('courseLoad', document.getElementById('courseLoad').value);
      formData.append('examDate', document.getElementById('examDate').value);
      
      // Send it silently to the PHP file
      fetch('backend/save_course.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if(data.status === 'success') {
            // Show success message and clear form
            formSuccess.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i> Course saved to database!';
            formSuccess.classList.remove('d-none');
            courseForm.reset();
            
            setTimeout(function() {
              formSuccess.classList.add('d-none');
            }, 3000);
        } else {
            alert("Error saving course: " + data.message);
        }
      })
      .catch(error => console.error('Error:', error));
    });
  }
});
// ---------- Fetch Courses for Dashboard (PHP Backend) ----------
console.log("Dashboard script is alive!"); // Add this line

document.addEventListener('DOMContentLoaded', function () {
  console.log("DOM loaded successfully!"); // Add this line
  var courseList = document.getElementById('dynamicCourseList');
  console.log("Course list element found:", courseList); // Add this line

  if (courseList) {
    fetch('backend/get_courses.php')
      .then(response => response.json())
      .then(data => {
        console.log("Data fetched:", data); // Add this line
        courseList.innerHTML = '';
        
        if (data.length > 0) {
          data.forEach(course => {
            var examDate = new Date(course.exam_date).getTime();
            var now = new Date().getTime();
            var diff = examDate - now;
            var daysLeft = Math.max(0, Math.ceil(diff / (1000 * 60 * 60 * 24)));

            var itemHtml = `
              <div class="mini-item">
                <span class="text-capitalize">${course.course_name}</span>
                <span class="m-count mono">${daysLeft}d left</span>
              </div>
            `;
            courseList.innerHTML += itemHtml;
          });
        } else {
          courseList.innerHTML = '<div class="mini-item"><span style="color:var(--muted);">No courses found.</span></div>';
        }
      })
      .catch(error => {
        console.error('Error fetching courses:', error);
        courseList.innerHTML = '<div class="mini-item"><span style="color:var(--signal);">Connection error.</span></div>';
      });
  }
});