// ── Default habit data (fallback when PHP is unavailable) ──
const DEFAULT_HABITS = [
  { id: 1, name: "Drink Water",         emoji: "💧", bg: "bg-blue",   done: true  },
  { id: 2, name: "Morning Exercise",    emoji: "🏃", bg: "bg-orange", done: true  },
  { id: 3, name: "Read Book",           emoji: "📗", bg: "bg-green",  done: true  },
  { id: 4, name: "Learn Something New", emoji: "💡", bg: "bg-pink",   done: true  },
  { id: 5, name: "Meditation",          emoji: "🧘", bg: "bg-purple", done: false },
];

// ── State ──
const today   = new Date();
let viewYear  = today.getFullYear();
let viewMonth = today.getMonth();
let selected  = { year: today.getFullYear(), month: today.getMonth(), day: today.getDate() };

// ── DOM refs ──
const monthTitle        = document.getElementById("monthTitle");
const calendarBody      = document.getElementById("calendarBody");
const selectedDateTitle = document.getElementById("selectedDateTitle");
const habitList         = document.getElementById("habitList");
const prevBtn           = document.getElementById("prevMonth");
const nextBtn           = document.getElementById("nextMonth");
const todayBtn          = document.getElementById("todayBtn");

const MONTHS = ["January","February","March","April","May","June",
                "July","August","September","October","November","December"];

// ── Build Calendar Grid ──
function buildCalendar(year, month) {
  monthTitle.textContent = `${MONTHS[month]} ${year}`;
  calendarBody.innerHTML = "";

  const firstDay    = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();
  const daysInPrev  = new Date(year, month, 0).getDate();

  let cells = [];

  for (let i = firstDay - 1; i >= 0; i--)
    cells.push({ day: daysInPrev - i, faded: true, year: month===0?year-1:year, month: month===0?11:month-1 });

  for (let d = 1; d <= daysInMonth; d++)
    cells.push({ day: d, faded: false, year, month });

  const remaining = 42 - cells.length;
  for (let d = 1; d <= remaining; d++)
    cells.push({ day: d, faded: true, year: month===11?year+1:year, month: month===11?0:month+1 });

  for (let row = 0; row < 6; row++) {
    const tr = document.createElement("tr");
    for (let col = 0; col < 7; col++) {
      const cell = cells[row * 7 + col];
      const td   = document.createElement("td");

      const isToday    = !cell.faded && cell.day===today.getDate() && cell.month===today.getMonth() && cell.year===today.getFullYear();
      const isSelected = cell.day===selected.day && cell.month===selected.month && cell.year===selected.year;

      let cls = "day-cell";
      if (cell.faded)  cls += " faded";
      if (isToday)     cls += " today";
      if (isSelected)  cls += " selected";

      td.innerHTML = `<span class="${cls}" data-year="${cell.year}" data-month="${cell.month}" data-day="${cell.day}">${cell.day}</span>`;

      td.querySelector(".day-cell").addEventListener("click", function () {
        selected = { year: +this.dataset.year, month: +this.dataset.month, day: +this.dataset.day };
        buildCalendar(viewYear, viewMonth);
        loadHabits(selected);
      });

      tr.appendChild(td);
    }
    calendarBody.appendChild(tr);
  }
}

// ── Load Habits (PHP → fallback) ──
function loadHabits(dateObj) {
  const dateStr  = `${dateObj.year}-${String(dateObj.month+1).padStart(2,"0")}-${String(dateObj.day).padStart(2,"0")}`;
  const monthStr = MONTHS[dateObj.month];
  selectedDateTitle.textContent = `${monthStr} ${dateObj.day}, ${dateObj.year}`;

  fetch(`habits.php?date=${dateStr}`)
    .then(r => { if (!r.ok) throw new Error(); return r.json(); })
    .then(habits => renderHabits(habits))
    .catch(() => renderHabits(DEFAULT_HABITS));
}

// ── Render Habits ──
function renderHabits(habits) {
  habitList.innerHTML = "";
  if (!habits.length) {
    habitList.innerHTML = `<p style="color:var(--text-muted);font-size:0.85rem;text-align:center;padding:20px 0;">No habits for this day.</p>`;
    return;
  }
  habits.forEach(h => {
    const div = document.createElement("div");
    div.className = "habit-item";
    div.innerHTML = `
      <div class="habit-emoji ${h.bg}">${h.emoji}</div>
      <span class="habit-name">${h.name}</span>
      <button class="habit-check ${h.done ? 'done' : 'pending'}" data-id="${h.id}">
        ${h.done ? "✓" : "○"}
      </button>`;
    div.querySelector(".habit-check").addEventListener("click", () => toggleHabit(h.id, !h.done, habits));
    habitList.appendChild(div);
  });
}

// ── Toggle Habit ──
function toggleHabit(id, newState, habits) {
  const h = habits.find(x => x.id === id);
  if (!h) return;
  h.done = newState;
  renderHabits(habits);

  const dateStr = `${selected.year}-${String(selected.month+1).padStart(2,"0")}-${String(selected.day).padStart(2,"0")}`;
  fetch("habits.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id, done: newState, date: dateStr })
  }).catch(() => {});
}

// ── Navigation ──
prevBtn.addEventListener("click", () => {
  if (--viewMonth < 0) { viewMonth = 11; viewYear--; }
  buildCalendar(viewYear, viewMonth);
});
nextBtn.addEventListener("click", () => {
  if (++viewMonth > 11) { viewMonth = 0; viewYear++; }
  buildCalendar(viewYear, viewMonth);
});
todayBtn.addEventListener("click", () => {
  viewYear = today.getFullYear(); viewMonth = today.getMonth();
  selected = { year: today.getFullYear(), month: today.getMonth(), day: today.getDate() };
  buildCalendar(viewYear, viewMonth);
  loadHabits(selected);
});

// ── Init ──
buildCalendar(viewYear, viewMonth);
loadHabits(selected);
