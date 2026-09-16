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
    if (!dEl  !hEl  !mEl || !sEl) return;

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
  if (!missBtn  !resetBtn  !note) return;

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