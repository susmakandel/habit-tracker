
include_once("config.php")

<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") { http_response_code(200); exit; }

define("DATA_FILE", __DIR__ . "/habits_data.json");

function initData(): array {
    return [
        "habits" => [
            ["id"=>1,"name"=>"Drink Water",         "emoji"=>"💧","bg"=>"bg-blue"],
            ["id"=>2,"name"=>"Morning Exercise",    "emoji"=>"🏃","bg"=>"bg-orange"],
            ["id"=>3,"name"=>"Read Book",           "emoji"=>"📗","bg"=>"bg-green"],
            ["id"=>4,"name"=>"Learn Something New", "emoji"=>"💡","bg"=>"bg-pink"],
            ["id"=>5,"name"=>"Meditation",          "emoji"=>"🧘","bg"=>"bg-purple"],
        ],
        "completions" => new stdClass()
    ];
}

function loadData(): array {
    if (!file_exists(DATA_FILE)) { $d = initData(); saveData($d); return $d; }
    $decoded = json_decode(file_get_contents(DATA_FILE), true);
    return $decoded ?: initData();
}

function saveData(array $data): void {
    file_put_contents(DATA_FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function validDate(string $d): bool {
    return (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $d);
}

// ── GET: return habits with done status ──
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $date = trim($_GET["date"] ?? "");
    if (!$date || !validDate($date)) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid date. Use YYYY-MM-DD."]); exit;
    }
    $data    = loadData();
    $doneIds = $data["completions"][$date] ?? [];
    $result  = array_map(fn($h) => [...$h, "done" => in_array($h["id"], $doneIds, true)], $data["habits"]);
    echo json_encode($result); exit;
}

// ── POST: toggle habit completion ──
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $body = json_decode(file_get_contents("php://input"), true);
    $id   = isset($body["id"])   ? (int)  $body["id"]   : null;
    $done = isset($body["done"]) ? (bool) $body["done"] : null;
    $date = isset($body["date"]) ? trim($body["date"])   : null;

    if ($id===null || $done===null || !$date || !validDate($date)) {
        http_response_code(400);
        echo json_encode(["error" => "Required: id (int), done (bool), date (YYYY-MM-DD)."]); exit;
    }

    $data = loadData();
    if (!isset($data["completions"][$date])) $data["completions"][$date] = [];
    $list = &$data["completions"][$date];

    if ($done) { if (!in_array($id,$list,true)) $list[] = $id; }
    else       { $list = array_values(array_filter($list, fn($x) => $x !== $id)); }

    saveData($data);
    echo json_encode(["success"=>true,"id"=>$id,"done"=>$done,"date"=>$date]); exit;
}

http_response_code(405);
echo json_encode(["error" => "Method not allowed."]);
<?php
require_once 'config.php';

$result = mysqli_query($mysqli, "SELECT * FROM habits");
$habits = mysqli_fetch_all($result, MYSQLI_ASSOC);

$totalHabits = count($habits);
$currentStreak = 0;
?>






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

    <div class="sidebar">
        <div>
            <div class="logo-section">
                <i class="fa-solid fa-clipboard-list"></i>
                <span>Habit Tracker</span>
            </div>
            <ul class="menu-links">
                <li class="active"><a href="#"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                <li><a href="#"><i class="fa-solid fa-calendar-check"></i> Habits</a></li>
                <li><a href="#"><i class="fa-solid fa-calendar-days"></i> Calendar</a></li>
                <li><a href="#"><i class="fa-solid fa-chart-line"></i> Progress</a></li>
                <li><a href="#"><i class="fa-solid fa-chart-pie"></i> Statistics</a></li>
                <li><a href="#"><i class="fa-solid fa-user"></i> Profile</a></li>
                <li><a href="#"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
            </ul>
        </div>
        <div class="user-profile">
            <img src="https://i.pravatar.cc/150?img=33" alt="Avatar image placeholder">
            <div>
                <h4 style="font-size:14px; font-weight:600;">John Doe</h4>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <div>
                <h1>Hello, John 👋</h1>
                <p>Keep going! You're doing great.</p>
            </div>
            <div class="bell-icon">
                <i class="fa-regular fa-bell"></i>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div>
                    <h3 id="stat-total"><?= $totalHabits ?></h3>
                    <p>Total Habits</p>
                </div>
                <div class="stat-icon icon-blue"><i class="fa-solid fa-calendar-days"></i></div>
            </div>
            <div class="stat-card">
                <div>
                    <h3 id="stat-completed">0</h3>
                    <p>Completed Today</p>
                </div>
                <div class="stat-icon icon-green"><i class="fa-solid fa-circle-check"></i></div>
            </div>
            <div class="stat-card">
                <div>
                    <h3><?= $currentStreak ?></h3>
                    <p>Current Streak</p>
                </div>
                <div class="stat-icon icon-orange"><i class="fa-solid fa-fire"></i></div>
            </div>
            <div class="stat-card">
                <div>
                    <h3 id="stat-rate">0%</h3>
                    <p>Success Rate</p>
                </div>
                <div class="stat-icon icon-purple"><i class="fa-solid fa-chart-bar"></i></div>
            </div>
        </div>

        <div class="dashboard-body">
            <div class="card-panel">
                <h2>Today's Habits</h2>
                <div class="habit-list">
                    <?php 
                    $colors = ['#e6f4ff', '#fff1e6', '#e6fff0', '#f3efff', '#ffe6eb'];
                    $textColors = ['#0958d9', '#d46b08', '#389e0d', '#531dab', '#c41d7f'];
                    $icons = ['fa-faucet-drip', 'fa-running', 'fa-book-open', 'fa-brain', 'fa-spa'];

                    foreach ($habits as $index => $habit): 
                        $colorIndex = $index % count($colors);
                    ?>
                        <div class="habit-item">
                            <div class="habit-left">
                                <div class="habit-avatar" style="background: <?= $colors[$colorIndex] ?>; color: <?= $textColors[$colorIndex] ?>;">
                                    <i class="fa-solid <?= $icons[$colorIndex] ?>"></i>
                                </div>
                                <div class="habit-info">
                                    <h4><?= htmlspecialchars($habit['title']) ?></h4>
                                    <p><?= $habit['frequency'] ?> • Streak <?= $habit['streak'] ?></p>
                                </div>
                            </div>
                            <label class="checkbox-container">
                                <input type="checkbox" class="habit-checkbox" <?= $habit['completed'] ? 'checked' : '' ?> onchange="updateProgress()">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card-panel">
                <h2>Weekly Progress</h2>
                <div class="chart-box">
                    <div class="progress-ring-container">
                        <svg class="progress-ring" width="160" height="160">
                            <circle class="progress-ring__background" stroke="#f0eeff" stroke-width="12" fill="transparent" r="70" cx="80" cy="80"/>
                            <circle class="progress-ring__circle" stroke-width="12" stroke-linecap="round" fill="transparent" r="70" cx="80" cy="80"/>
                        </svg>
                        <div class="progress-ring-text">
                            <span id="chart-percentage">0%</span>
                            <p>Completed</p>
                        </div>
                    </div>
                    <div class="chart-footer-text">
                        <span id="chart-fraction">0/0</span>
                        <p>Habits Completed</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
