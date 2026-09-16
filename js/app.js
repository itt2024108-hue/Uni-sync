// Load data from localStorage
let courses = JSON.parse(localStorage.getItem('uniSyncCourses')) || [];

function saveData() {
    localStorage.setItem('uniSyncCourses', JSON.stringify(courses));
}

function getRemainingDays(examDate) {
    const today = new Date();
    const exam = new Date(examDate);
    const diffTime = Math.abs(exam - today);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
    return diffDays > 0 ? diffDays : 1; 
}

// ----------------------------------------------------
// PAGE 1: DATA ENTRY LOGIC (Only runs on input.html)
// ----------------------------------------------------
const courseForm = document.getElementById('courseForm');
if (courseForm) {
    courseForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const newCourse = {
            id: Date.now(),
            name: document.getElementById('courseName').value,
            workload: parseInt(document.getElementById('totalWorkload').value),
            examDate: document.getElementById('examDate').value
        };
        courses.push(newCourse);
        saveData();
        this.reset();
        alert("Success! Course saved. Navigate to the Dashboard or Planner to view.");
    });
}

// ----------------------------------------------------
// PAGE 2: DASHBOARD LOGIC (Only runs on index.html)
// ----------------------------------------------------
const dashboard = document.getElementById('countdown-container');
if (dashboard) {
    if (courses.length === 0) {
        dashboard.innerHTML = '<p class="text-muted">No exams added yet. Head over to Data Entry.</p>';
    } else {
        courses.forEach(course => {
            const daysLeft = getRemainingDays(course.examDate);
            dashboard.innerHTML += `
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm countdown-card">
                        <div class="card-body text-center">
                            <h5 class="card-title">${course.name}</h5>
                            <div class="timer-text">${daysLeft} Days</div>
                            <p class="text-muted mb-0">Until Exam</p>
                        </div>
                    </div>
                </div>
            `;
        });
    }
}

// ----------------------------------------------------
// PAGE 3: PLANNER LOGIC (Only runs on planner.html)
// ----------------------------------------------------
const planner = document.getElementById('planner-body');
if (planner) {
    if (courses.length === 0) {
        planner.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No data available.</td></tr>';
    } else {
        courses.forEach(course => {
            const daysLeft = getRemainingDays(course.examDate);
            // Core Engine Logic: New Daily Load = Remaining Workload / Remaining Days
            const dailyLoad = (course.workload / daysLeft).toFixed(2);
            
            planner.innerHTML += `
                <tr>
                    <td class="fw-bold">${course.name}</td>
                    <td>${course.examDate}</td>
                    <td>${daysLeft}</td>
                    <td>${course.workload} units</td>
                    <td class="text-success fw-bold">${dailyLoad} units/day</td>
                    <td>
                        <button class="btn btn-sm btn-outline-danger" onclick="missSession()">Missed Session</button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="deleteCourse(${course.id})">Remove</button>
                    </td>
                </tr>
            `;
        });
    }
}

// Global Actions for Planner Page
window.missSession = function() {
    alert("Session missed. The rule-based engine will automatically divide the remaining workload by the remaining days tomorrow.");
}

window.deleteCourse = function(id) {
    courses = courses.filter(c => c.id !== id);
    saveData();
    location.reload(); // Refresh the page to show the item was deleted
}