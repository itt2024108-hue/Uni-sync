// ---------- mobile nav ----------
document.addEventListener('DOMContentLoaded', function () {
  var navToggle = document.getElementById('navToggle');
  var mobileMenu = document.getElementById('mobileMenu');
  if (navToggle && mobileMenu) {
    navToggle.addEventListener('click', function () {
      mobileMenu.classList.toggle('d-none');
    });
  }

  initEngineDemo();
});

// ---------- 1. Fully Dynamic Countdowns (Home & Planner) ----------
document.addEventListener('DOMContentLoaded', function () {
  // Main hero countdown IDs (Home)
  const titleEl = document.querySelector('.console-course');
  const daysEl = document.getElementById('hd');
  const hrsEl = document.getElementById('hh');
  const minsEl = document.getElementById('hm');
  const secsEl = document.getElementById('hs');
  
  // Secondary promo countdown IDs (Planner/Home)
  const promoTitleEl = document.getElementById('promoCourseName');
  const promoDaysEl = document.getElementById('promoHd');
  const promoHrsEl = document.getElementById('promoHh');
  const promoMinsEl = document.getElementById('promoHm');
  const promoSecsEl = document.getElementById('promoHs');

  const homeListEl = document.getElementById('homeCourseList');
  const coverageEl = document.getElementById('homeSyllabusCoverage'); 

  // Safely check if ANY countdown element exists on this page before connecting
  if (!titleEl && !promoTitleEl && !homeListEl) return;

  if (titleEl) titleEl.innerText = "Connecting to database...";
  if (promoTitleEl) promoTitleEl.innerText = "Connecting...";

  fetch('backend/get_courses.php', { cache: 'no-store' })
    .then(response => response.text()) 
    .then(text => {
      try {
        const data = JSON.parse(text);
        
        if (data && data.length > 0) {
         // Calculate and display percentage for the closest exam
            if (coverageEl) {
                let urgentCourse = data[0];
                let wl = parseInt(urgentCourse.workload) || 0;
                let comp = parseInt(urgentCourse.completed_workload) || 0;
                let pct = (wl > 0) ? Math.round((comp / wl) * 100) : 0;
                coverageEl.innerHTML = pct + "% on schedule";
            } 
          // --- A. Populate the List Section ---
          if (homeListEl) {
            homeListEl.innerHTML = ''; 
            data.slice(0, 4).forEach(course => {
              const examTime = new Date(course.exam_date).getTime();
              const now = new Date().getTime();
              const diff = examTime - now;
              let daysLeft = Math.ceil(diff / (1000 * 60 * 60 * 24));
              
              let timeText = daysLeft < 0 
                ? `<span style="color: var(--signal);">Expired</span>` 
                : `<span style="color: var(--signal);">${daysLeft}d left</span>`;

              homeListEl.innerHTML += `
                <div class="mini-item">
                  <span>${course.course_name}</span>
                  <span class="m-count mono">${timeText}</span>
                </div>
              `;
            });
          }

          // --- B. Run the Countdowns ---
          const futureCourses = data.filter(c => new Date(c.exam_date).getTime() > new Date().getTime());
          
          if (futureCourses.length > 0) {
            const nextCourse = futureCourses[0]; 
            
            // Update both titles safely
            if (titleEl) titleEl.innerText = `${nextCourse.course_name} — Deadline`;
            if (promoTitleEl) promoTitleEl.innerText = `${nextCourse.course_name} — Live Countdown`;

            // --- C. DYNAMIC SYLLABUS COVERAGE ---
            if (coverageEl) {
              let workload = parseInt(nextCourse.workload) || 0;
              let completed = parseInt(nextCourse.completed_workload) || 0;
              let percentage = 0;
              
              if (workload > 0) {
                  percentage = Math.round((completed / workload) * 100);
                  if (percentage > 100) percentage = 100;
              }
              coverageEl.innerText = `${percentage}% on schedule`;
            }

            // Start the synchronized clock
            const targetTime = new Date(nextCourse.exam_date).getTime();
            setInterval(() => {
              const now = new Date().getTime();
              const distance = targetTime - now;

              if (distance < 0) return; 

              const days = Math.floor(distance / (1000 * 60 * 60 * 24));
              const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
              const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
              const seconds = Math.floor((distance % (1000 * 60)) / 1000);

              const dStr = String(days).padStart(2, '0');
              const hStr = String(hours).padStart(2, '0');
              const mStr = String(minutes).padStart(2, '0');
              const sStr = String(seconds).padStart(2, '0');

              // Update main countdown if it exists on page
              if(daysEl) daysEl.innerText = dStr;
              if(hrsEl) hrsEl.innerText = hStr;
              if(minsEl) minsEl.innerText = mStr;
              if(secsEl) secsEl.innerText = sStr;
              
              // Update secondary promo countdown if it exists on page
              if(promoDaysEl) promoDaysEl.innerText = dStr;
              if(promoHrsEl) promoHrsEl.innerText = hStr;
              if(promoMinsEl) promoMinsEl.innerText = mStr;
              if(promoSecsEl) promoSecsEl.innerText = sStr;

            }, 1000);
          } else {
             if(titleEl) titleEl.innerText = "No upcoming future deadlines";
             if(promoTitleEl) promoTitleEl.innerText = "No upcoming deadlines";
             if (coverageEl) coverageEl.innerText = "0% on schedule";
          }
        } else {
           if(titleEl) titleEl.innerText = "No courses in database";
           if(promoTitleEl) promoTitleEl.innerText = "No courses in database";
           if (coverageEl) coverageEl.innerText = "0% on schedule";
        }
      } catch(e) {
        if(titleEl) titleEl.innerText = "Database Error!";
        if(promoTitleEl) promoTitleEl.innerText = "Database Error!";
        console.error("PHP returned:", text);
      }
    })
    .catch(err => {
      if(titleEl) titleEl.innerText = "Failed to connect to backend";
      if(promoTitleEl) promoTitleEl.innerText = "Connection failed";
    });
});
// ---------- 2. Dynamic Dashboard Course List ----------
document.addEventListener('DOMContentLoaded', function () {
  const courseList = document.getElementById('dynamicCourseList');

  if (courseList) {
    courseList.innerHTML = '<div class="mini-item">Fetching data...</div>';
    
    fetch('backend/get_upcoming.php', { cache: 'no-store' })
      .then(response => response.text()) 
      .then(text => {
        try {
          const data = JSON.parse(text);
          
          if (data && data.length > 0) {
            courseList.innerHTML = ''; 
            data.forEach(course => {
              const examTime = new Date(course.exam_date).getTime();
              const now = new Date().getTime();
              const diff = examTime - now;
              let daysLeft = Math.ceil(diff / (1000 * 60 * 60 * 24));
              let timeDisplay = daysLeft < 0 ? `<span style="color: var(--signal);">Expired</span>` : `${daysLeft}d left`;

              courseList.innerHTML += `
                <div class="mini-item d-flex justify-content-between py-2 border-bottom">
                  <span class="text-capitalize" style="font-weight: bold;">${course.course_name}</span>
                  <span class="mono">${timeDisplay}</span>
                </div>
              `;
            });
          } else {
            courseList.innerHTML = `<div class="mini-item" style="color:var(--signal);">PHP connected, but returned: ${text}</div>`;
          }
        } catch(e) {
          courseList.innerHTML = `<div class="mini-item" style="color:var(--signal);">JSON Error! PHP output: ${text.substring(0, 100)}...</div>`;
        }
      })
      .catch(error => {
        courseList.innerHTML = `<div class="mini-item" style="color:var(--signal);">Fetch Error: ${error.message}</div>`;
      });
  }
});
 
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
    var remainingWorkload = 80; 
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

// ---------- Add Course Form Logic ----------
document.addEventListener('DOMContentLoaded', function () {
  var courseForm = document.getElementById('courseForm');
  var formSuccess = document.getElementById('formSuccess');

  if (courseForm) {
    courseForm.addEventListener('submit', function (e) {
      e.preventDefault(); 
      
      // 1. Grab the elements safely
      var courseNameEl = document.getElementById('courseName');
      var examDateEl = document.getElementById('examDate');
      var courseLoadEl = document.getElementById('courseLoad');
      
      // 2. Check if the required fields actually exist in the HTML
      if (!courseNameEl || !examDateEl) {
         alert("Error: Missing input IDs in HTML. Make sure your inputs have id='courseName' and id='examDate'.");
         return;
      }

      // 3. Package the data (defaulting workload to 0 if the field isn't on this page)
      var formData = new FormData();
      formData.append('courseName', courseNameEl.value);
      formData.append('examDate', examDateEl.value);
      formData.append('courseLoad', courseLoadEl ? courseLoadEl.value : '0'); 
      
      fetch('backend/save_course.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if(data.status === 'success') {
            if(formSuccess) {
                formSuccess.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i> Course saved to database!';
                formSuccess.classList.remove('d-none');
                setTimeout(function() {
                  formSuccess.classList.add('d-none');
                }, 3000);
            } else {
                alert("Course saved successfully!");
            }
            courseForm.reset();
        } else {
            alert("Database Error: " + data.message);
        }
      })
      .catch(error => {
          console.error('Fetch Error:', error);
          alert("Server error. Check the console for details.");
      });
    });
  }
});

// ---------- Study Planner Engine Demo Simulation & Reset ----------
document.addEventListener('DOMContentLoaded', function () {
    const engineBars = document.getElementById('engineBars');
    const engineNote = document.getElementById('engineNote');
    
    if (!engineBars) return; // Only run on planner page

    // Fetch live course data to power the simulation
    fetch('backend/get_courses.php', { cache: 'no-store' })
        .then(response => response.json())
        .then(data => {
            if (!data || data.length === 0) {
                if (engineNote) engineNote.innerHTML = "No active courses found. Please add a course first!";
                return;
            }

            const nowTime = new Date().getTime();
            let futureCourses = data.filter(c => new Date(c.exam_date).getTime() > nowTime);
            
            if (futureCourses.length === 0) {
                if (engineNote) engineNote.innerHTML = "All deadlines have passed!";
                return;
            }
// 1. Create a text breakdown of units for EVERY course (Vertical List)
            let courseBreakdown = futureCourses.map(c => {
                let wl = parseInt(c.workload) || 0;
                let comp = parseInt(c.completed_workload) || 0;
                let remain = Math.max(wl - comp, 0);
                return `<div style="margin-bottom: 4px;"><span style="color:var(--signal); margin-right:6px;">•</span><strong>${c.course_name}</strong>: ${remain}u left</div>`;
            }).join('');
            
            // 2. Sum up ALL remaining workloads across ALL courses combined
            let totalRemainingWorkload = futureCourses.reduce((sum, c) => {
                let wl = parseInt(c.workload) || 0;
                let comp = parseInt(c.completed_workload) || 0;
                return sum + Math.max(wl - comp, 0);
            }, 0);

            // 3. Find days left based on the closest urgent deadline
            let closestDate = futureCourses[0].exam_date;
            let targetTime = new Date(closestDate).getTime();
            let totalDaysLeft = Math.ceil((targetTime - nowTime) / (1000 * 60 * 60 * 24));
            if (totalDaysLeft < 1) totalDaysLeft = 1;

            let missedDays = new Set();
            const dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

            function renderSimulation() {
                if (totalRemainingWorkload === 0) {
                    if (engineNote) engineNote.innerHTML = `All courses are fully completed! 🎉`;
                    engineBars.innerHTML = '';
                    return;
                }

                let activeDaysCount = totalDaysLeft - missedDays.size;
                if (activeDaysCount <= 0) activeDaysCount = 1; 

                let dailyLoad = (totalRemainingWorkload / activeDaysCount).toFixed(1);
                if (dailyLoad.endsWith('.0')) dailyLoad = parseInt(dailyLoad);
                
                let heightPct = 40 + (missedDays.size * 10); 
                if (heightPct > 100) heightPct = 100;

                let barsHTML = '';
                dayNames.forEach((day, index) => {
                    let isMissed = missedDays.has(index);
                    if (isMissed) {
                        barsHTML += `
                           <div class="bar-col missed-col" style="flex: 1; background: var(--card-bg); padding: 10px 6px; border-radius: 8px; text-align: center; border: 1px dashed var(--signal);" data-day="${index}">
                               <div class="bar" style="height: 20px; background: rgba(255,255,255,0.05); border-radius: 4px; margin-bottom: 6px;"></div>
                               <div class="bar-day" style="text-decoration: line-through; color: var(--muted); font-size: 0.75rem; margin-bottom: 6px;">${day}</div>
                               <button class="sim-toggle-btn" data-day="${index}" style="background: var(--signal); color: #000; border: none; padding: 3px 8px; font-size: 0.65rem; border-radius: 4px; cursor: pointer; font-weight: bold;">Restore</button>
                           </div>
                        `;
                    } else {
                        barsHTML += `
                           <div class="bar-col active-col" style="flex: 1; background: var(--card-bg); padding: 10px 6px; border-radius: 8px; text-align: center; border: 1px solid rgba(255,255,255,0.1);" data-day="${index}">
                               <div class="bar" style="height: ${heightPct}px; background: var(--text-main); border-radius: 4px; margin-bottom: 6px; transition: height 0.3s ease;"></div>
                               <div class="bar-day" style="font-size: 0.75rem; color: var(--text-main); margin-bottom: 4px;">${day}<br><span style="color:var(--signal); font-weight:bold;">${dailyLoad}u</span></div>
                               <button class="sim-toggle-btn" data-day="${index}" style="background: rgba(255,255,255,0.1); color: var(--text-main); border: 1px solid rgba(255,255,255,0.2); padding: 3px 8px; font-size: 0.65rem; border-radius: 4px; cursor: pointer;">Skip</button>
                           </div>
                        `;
                    }
                });

                engineBars.style.display = 'flex';
                engineBars.style.flexDirection = 'row';
                engineBars.style.gap = '10px';
                engineBars.style.justifyContent = 'space-between';
                engineBars.style.alignItems = 'stretch';
                engineBars.innerHTML = barsHTML;

                document.querySelectorAll('.bar-col, .sim-toggle-btn').forEach(element => {
                    element.addEventListener('click', function(e) {
                        e.stopPropagation();
                        let d = parseInt(this.getAttribute('data-day'));
                        if (missedDays.has(d)) {
                            missedDays.delete(d); 
                        } else {
                            missedDays.add(d); 
                        }
                        renderSimulation(); 
                    });
                });

                // Update text description with full multi-course breakdown
               
                if (engineNote) {
                    if (missedDays.size === 0) {
                        engineNote.innerHTML = `
                            <div style="margin-bottom: 12px; font-size: 0.95rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px;">${courseBreakdown}</div>
                            <div><strong>${totalRemainingWorkload} combined units</strong> remain across <strong>${totalDaysLeft} days</strong> (closest exam).</div>
                            <div style="margin-top: 6px;">Requires <strong style="color:var(--signal);">${dailyLoad} units/day</strong> total.</div>
                        `;
                    } else {
                        let missedNames = Array.from(missedDays).map(d => dayNames[d]).join(', ');
                        engineNote.innerHTML = `
                            <div style="margin-bottom: 12px; font-size: 0.95rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px;">${courseBreakdown}</div>
                            <div>Skipped: <strong>${missedNames}</strong>. Workload redistributed across <strong>${activeDaysCount} active days</strong>.</div>
                            <div style="margin-top: 6px;">Now requires <strong style="color:var(--signal);">${dailyLoad} units/day</strong> total.</div>
                        `;
                    }
                }
            }

            renderSimulation();
        })
        .catch(err => console.error("Error loading simulator data:", err));
});

// --- WORKLOAD ASSIGNMENT LOGIC ---
document.addEventListener('DOMContentLoaded', () => {
    const wlCourseSelect = document.getElementById('wlCourse');
    const workloadForm = document.getElementById('workloadForm');
    const successMsg = document.getElementById('wlSuccessMsg');

    if (wlCourseSelect) {
        fetch('backend/get_courses.php')
            .then(response => response.json())
            .then(data => {
                wlCourseSelect.innerHTML = '<option value="" disabled selected>Select a course...</option>';
                data.forEach(course => {
                    wlCourseSelect.innerHTML += `<option value="${course.course_name}">${course.course_name}</option>`;
                });
            })
            .catch(error => console.error('Error fetching courses:', error));
    }

    if (workloadForm) {
        workloadForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData();
            formData.append('courseName', document.getElementById('wlCourse').value);
            formData.append('newWorkload', document.getElementById('wlUnits').value);

            fetch('backend/update_workload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    successMsg.classList.remove('d-none');
                    setTimeout(() => {
                        successMsg.classList.add('d-none');
                        workloadForm.reset();
                    }, 3000);
                } else {
                    console.error('Database Error:', data.message);
                }
            })
            .catch(error => console.error('Error saving workload:', error));
        });
    }
});

// --- FORCE OVERRIDE LIGHT THEME TOGGLE (ALL PANELS & FRAMES) ---
document.addEventListener('DOMContentLoaded', () => {
    const themeBtn = document.getElementById('themeToggleBtn');
    const body = document.body;
    const themeIcon = themeBtn ? themeBtn.querySelector('i') : null;

    function applyTheme(isLight) {
        const darkElements = document.querySelectorAll('.console, .card-mini, .browser-frame, .engine-panel, .strip, .step, .card-mini-wide, div[style*="background"]');
        
        if (isLight) {
            body.classList.add('light-theme');
            if (themeIcon) themeIcon.classList.replace('bi-moon-stars', 'bi-sun');
            localStorage.setItem('uniSyncTheme', 'light');

            darkElements.forEach(el => {
                el.style.setProperty('background-color', '#ffffff', 'important');
                el.style.setProperty('background', '#ffffff', 'important');
                el.style.setProperty('color', '#1a261f', 'important');
                el.style.setProperty('border-color', '#dbe3dd', 'important');
            });
        } else {
            body.classList.remove('light-theme');
            if (themeIcon) themeIcon.classList.replace('bi-sun', 'bi-moon-stars');
            localStorage.setItem('uniSyncTheme', 'dark');

            darkElements.forEach(el => {
                el.style.removeProperty('background-color');
                el.style.removeProperty('background');
                el.style.removeProperty('color');
                el.style.removeProperty('border-color');
            });
        }
    }

    if (localStorage.getItem('uniSyncTheme') === 'light') {
        applyTheme(true);
    }

    if (themeBtn) {
        themeBtn.addEventListener('click', () => {
            const isLight = !body.classList.contains('light-theme');
            applyTheme(isLight);
        });
    }
});
// ============================================================================
// DASHBOARD DYNAMIC POPULATION (Dropdown, Table & Live Countdown)
// ============================================================================
document.addEventListener('DOMContentLoaded', function () {
    const courseSelect = document.getElementById('courseSelect') || document.querySelector('select[name="course_id"]') || document.querySelector('.course-dropdown');
    const tableBody = document.getElementById('dynamicCourseTableBody');
    const dashTitleEl = document.getElementById('dashCountdownTitle');

    // Only run this specific fetch if we are actually on the dashboard
    if (!courseSelect && !tableBody && !dashTitleEl) return;

    fetch('backend/get_courses.php', { cache: 'no-store' })
        .then(response => response.json())
        .then(data => {
            if (!data || data.length === 0) return;

            const now = new Date().getTime();
            const futureCourses = data.filter(c => new Date(c.exam_date).getTime() > now);

            // 1. Populate "Select Course" Dropdown
            if (courseSelect) {
                courseSelect.innerHTML = `<option value="" disabled selected>Select a course...</option>`;
                data.forEach(course => {
                    let option = document.createElement('option');
                    option.value = course.id || course.course_name;
                    option.textContent = course.course_name;
                    courseSelect.appendChild(option);
                });
            }

            // 2. Populate Dashboard Course Table
            if (tableBody) {
                tableBody.innerHTML = '';
                data.forEach(course => {
                    const examTime = new Date(course.exam_date).getTime();
                    const diff = examTime - new Date().getTime();
                    let daysLeft = Math.ceil(diff / (1000 * 60 * 60 * 24));
                    let timeDisplay = daysLeft < 0 ? `Expired` : `In ${daysLeft} days`;
                    
                    let wl = parseInt(course.workload) || 0;
                    let comp = parseInt(course.completed_workload) || 0;
                    let badgeHTML = '';
                    
                    if (daysLeft < 0) {
                        badgeHTML = `<span class="status-pill" style="background:var(--signal); color:#000;">Past Due</span>`;
                    } else if (wl > 0 && comp > 0) {
                        let pct = (comp / wl) * 100;
                        if (pct >= 50) badgeHTML = `<span class="status-pill ontrack">On track</span>`;
                        else badgeHTML = `<span class="status-pill p-blue">In progress</span>`;
                    } else {
                        badgeHTML = `<span class="status-pill later">Not started</span>`;
                    }

                    tableBody.innerHTML += `
                      <tr>
                        <td style="padding: 10px 0; font-weight: 600; color: var(--text-main);">${course.course_name}</td>
                        <td class="mono" style="padding: 10px 0; color: var(--muted);">${timeDisplay}</td>
                        <td style="padding: 10px 0; text-align: right;">${badgeHTML}</td>
                      </tr>
                    `;
                });
            }

            // 3. Live Countdown for Dashboard
            const dashDaysEl = document.getElementById('dashHd');
            const dashHrsEl = document.getElementById('dashHh');
            const dashMinsEl = document.getElementById('dashHm');
            const dashSecsEl = document.getElementById('dashHs');

            if (dashTitleEl && futureCourses.length > 0) {
                dashTitleEl.innerText = `${futureCourses[0].course_name} — Live Countdown`;
                const targetTime = new Date(futureCourses[0].exam_date).getTime();
                
                setInterval(() => {
                    const distance = targetTime - new Date().getTime();
                    if (distance < 0) return;
                    if (dashDaysEl) dashDaysEl.innerText = String(Math.floor(distance / (1000 * 60 * 60 * 24))).padStart(2, '0');
                    if (dashHrsEl) dashHrsEl.innerText = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                    if (dashMinsEl) dashMinsEl.innerText = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                    if (dashSecsEl) dashSecsEl.innerText = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');
                }, 1000);
            }
        })
        .catch(err => console.error("Dashboard error:", err));
});
// ============================================================================
// HOME PAGE DYNAMIC CALENDAR (Highlights Deadlines)
// ============================================================================
document.addEventListener('DOMContentLoaded', function () {
    const calGrid = document.getElementById('calendarGrid');
    const calHeader = document.getElementById('calendarHeader');
    
    // Only run if the calendar exists on the current page (index.php)
    if (!calGrid || !calHeader) return;

    fetch('backend/get_courses.php', { cache: 'no-store' })
        .then(response => response.json())
        .then(data => {
            const today = new Date();
            const year = today.getFullYear();
            const month = today.getMonth(); // 0 = Jan, 11 = Dec

            const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            calHeader.innerHTML = `<span>${monthNames[month]} ${year}</span>`;

            // Calculate calendar grid days
            const firstDay = new Date(year, month, 1).getDay(); // 0 (Sun) to 6 (Sat)
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            let html = '';
            
            // Add Day Labels
            const dayLabels = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
            dayLabels.forEach(d => {
                html += `<div style="font-weight: 600; color: var(--text-main); padding-bottom: 5px;">${d}</div>`;
            });

            // Add empty slots for days before the 1st of the month
            for (let i = 0; i < firstDay; i++) {
                html += `<div></div>`;
            }

            // Loop through all days of the month
            for (let d = 1; d <= daysInMonth; d++) {
                // Format current grid date as YYYY-MM-DD to match database
                let dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                
                // Check if this date exists in the user's exam dates
                let matchedCourses = data.filter(c => c.exam_date && c.exam_date.startsWith(dateStr));
                let isDeadline = matchedCourses.length > 0;
                let isToday = (d === today.getDate() && month === today.getMonth() && year === today.getFullYear());

                let baseStyle = `padding: 6px 0; border-radius: 4px; display: flex; justify-content: center; align-items: center; transition: 0.2s;`;
                
                if (isDeadline) {
                    // Highlight Deadlines in your signal color
                    let courseNames = matchedCourses.map(c => c.course_name).join(' & ');
                    html += `<div style="${baseStyle} background: var(--signal); color: #000; font-weight: bold; cursor: help; box-shadow: 0 0 8px rgba(255,165,0,0.4);" title="Deadline: ${courseNames}">${d}</div>`;
                } else if (isToday) {
                    // Outline Today's date
                    html += `<div style="${baseStyle} border: 1px solid var(--signal); color: var(--text-main); font-weight: bold;">${d}</div>`;
                } else {
                    // Standard day
                    html += `<div style="${baseStyle}">${d}</div>`;
                }
            }
            
            calGrid.innerHTML = html;
        })
        .catch(err => console.error("Error loading calendar data:", err));
});