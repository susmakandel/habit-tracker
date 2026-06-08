<?php
require_once 'config.php';

$result = mysqli_query($mysqli, "SELECT * FROM habits");
$habits = mysqli_fetch_all($result, MYSQLI_ASSOC);

$totalHabits = count($habits);
$currentStreak = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Habit Tracker Dashboard</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="style.css">
</head>
<body>
  
    <body>

<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="brand-icon">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </div>
    <span class="brand-name">Habit Tracker</span>
  </div>
  <nav class="sidebar-nav">
    <a href="#" class="nav-item active" data-page="dashboard">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/></svg>
      Dashboard
    </a>
    <a href="#" class="nav-item" data-page="habits">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><path d="M9 11l3 3L22 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
      Habits
    </a>
    <a href="#" class="nav-item" data-page="calendar">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      Calendar
    </a>
    <a href="#" class="nav-item" data-page="progress">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><path d="M18 20V10M12 20V4M6 20v-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      Progress
    </a>
    <a href="#" class="nav-item" data-page="statistics">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><path d="M3 3v18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M7 16l4-4 4 4 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
      Statistics
    </a>
    <a href="#" class="nav-item" data-page="profile">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      Profile
    </a>
  </nav>
  <div class="sidebar-logout">
    <a href="#" class="nav-item" id="logoutBtn">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
      Logout
    </a>
  </div>
  <div class="sidebar-user">
    <div class="user-avatar">JD</div>
    <div class="user-info"><div class="user-name">John Doe</div></div>
  </div>
</aside>

<main class="main">
  <header class="topbar">
    <div class="topbar-greeting">
      <h1 class="greeting-text">Hello, John 👋</h1>
      <p class="greeting-sub">Keep going! You're doing great.</p>
    </div>
    <div class="topbar-actions">
      <button class="btn-icon" aria-label="Notifications">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span class="notif-dot"></span>
      </button>
    </div>
  </header>

  <section class="stats-grid">
    <div class="stat-card">
      <div class="stat-info"><div class="stat-value" id="totalHabits">7</div><div class="stat-label">Total Habits</div></div>
      <div class="stat-icon purple"><svg viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></div>
    </div>
    <div class="stat-card">
      <div class="stat-info"><div class="stat-value" id="completedToday">5</div><div class="stat-label">Completed Today</div></div>
      <div class="stat-icon green"><svg viewBox="0 0 24 24" fill="none"><path d="M22 11.08V12a10 10 0 11-5.93-9.14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M22 4L12 14.01l-3-3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>
    <div class="stat-card">
      <div class="stat-info"><div class="stat-value" id="currentStreak">12</div><div class="stat-label">Current Streak</div></div>
      <div class="stat-icon orange"><svg viewBox="0 0 24 24" fill="none"><path d="M12 2c0 6-6 8-6 14a6 6 0 0012 0c0-6-6-8-6-14z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
    </div>
    <div class="stat-card">
      <div class="stat-info"><div class="stat-value" id="successRate">85%</div><div class="stat-label">Success Rate</div></div>
      <div class="stat-icon blue"><svg viewBox="0 0 24 24" fill="none"><path d="M18 20V10M12 20V4M6 20v-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></div>
    </div>
  </section>

  <section class="content-grid">
    <div class="card">
      <div class="card-header">
        <h2 class="card-title">Today's Habits</h2>
        <button class="btn-add" id="openAddModal">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
          Add Habit
        </button>
      </div>
      <ul class="habits-list" id="habitsList"></ul>
    </div>

    <div class="card" style="text-align:center">
      <div class="card-header"><h2 class="card-title">Weekly Progress</h2></div>
      <div class="donut-wrap">
        <svg class="donut" viewBox="0 0 120 120">
          <circle class="donut-bg" cx="60" cy="60" r="50"/>
          <circle class="donut-fill" id="donutFill" cx="60" cy="60" r="50" stroke-dasharray="0 314" stroke-dashoffset="78.5"/>
        </svg>
        <div class="donut-label">
          <span class="donut-pct" id="donutPct">85%</span>
          <span class="donut-sub">Completed</span>
        </div>
      </div>
      <div class="weekly-bar-chart" id="weeklyBarChart"></div>
      <div class="progress-footer">
        <span id="weeklyFraction">5/7</span>
        <span class="progress-footer-label">Habits Completed</span>
      </div>
    </div>
  </section>
</main>

<div class="modal-overlay" id="modalOverlay">
  <div class="modal">
    <div class="modal-header">
      <h3>Add New Habit</h3>
      <button class="btn-close" id="closeModal">✕</button>
    </div>
    <div class="modal-body">
      <label class="form-label">Habit Name</label>
      <input type="text" class="form-input" id="habitName" placeholder="e.g. Drink 8 glasses of water">
      <label class="form-label">Frequency</label>
      <select class="form-input" id="habitFrequency">
        <option value="daily">Daily</option>
        <option value="weekly">Weekly</option>
        <option value="monthly">Monthly</option>
      </select>
      <label class="form-label">Color</label>
      <div class="color-picker" id="colorPicker">
        <span class="color-dot selected" data-color="#7C3AED" style="background:#7C3AED"></span>
        <span class="color-dot" data-color="#3B82F6" style="background:#3B82F6"></span>
        <span class="color-dot" data-color="#10B981" style="background:#10B981"></span>
        <span class="color-dot" data-color="#F59E0B" style="background:#F59E0B"></span>
        <span class="color-dot" data-color="#EF4444" style="background:#EF4444"></span>
        <span class="color-dot" data-color="#EC4899" style="background:#EC4899"></span>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-secondary" id="cancelModal">Cancel</button>
      <button class="btn-primary" id="saveHabit">Save Habit</button>
    </div>
  </div>
</div>

        <script src="script.js"></script>
</body>
</html>
