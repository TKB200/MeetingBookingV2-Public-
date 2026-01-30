<?php
require_once 'includes/auth.php'; // starts session and provides login(), isAdmin()

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (login($username, $password)) {
        if (isAdmin()) {
            header('Location: admin/index.php');
        } else {
            header('Location: user/index.php');
        }
        exit();
    } else {
        $error = 'Invalid username or password.';
    }
}

// Fetch users to show (exclude password)
try {
    $stmt = $pdo->query("SELECT id, username, role, created_at FROM users ORDER BY id ASC");
    $users = $stmt->fetchAll();
} catch (Exception $e) {
    $users = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Guest Login & Users</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body{font-family:Arial,Helvetica,sans-serif;background:#f7f7fb;padding:20px}
        .box{max-width:900px;margin:24px auto;padding:20px;background:#fff;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.06)}
        .flex{display:flex;gap:20px;align-items:flex-start}
        .login{flex:1;min-width:260px}
        .users{flex:2;overflow:auto}
        table{width:100%;border-collapse:collapse}
        th,td{padding:8px 10px;border-bottom:1px solid #eee;text-align:left}
        th{background:#fafafa}
        .small{font-size:0.9rem;color:#666}
        .btn{background:#667eea;color:#fff;border:none;padding:10px 14px;border-radius:8px;cursor:pointer}
        /* Loading overlay and progress bar */
        .loading-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.35);display:none;align-items:center;justify-content:center;z-index:9999}
        .progress-wrap{width:80%;max-width:560px;background:rgba(255,255,255,0.95);padding:18px;border-radius:10px;box-shadow:0 8px 30px rgba(0,0,0,0.12);text-align:left}
        .progress-label{font-size:0.95rem;color:#333;margin-bottom:8px}
        .progress-track{background:#eee;height:10px;border-radius:10px;overflow:hidden}
        .progress-bar{height:100%;width:0%;background:linear-gradient(90deg,#667eea,#43e97b);transition:width 0.2s ease}
    </style>
</head>
<body>
    <!-- Loading overlay shown on form submit -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="progress-wrap">
            <div class="progress-label">Logging in — please wait...</div>
            <div class="progress-track"><div id="progressBar" class="progress-bar"></div></div>
        </div>
    </div>
    <div class="box">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <h2 style="margin:0">Guest Login & Users</h2>
            <div><a href="index.php" style="color:#667eea;text-decoration:none">Back to Home</a></div>
        </div>
        <div class="flex">
            <div class="login">
                <?php if ($error): ?>
                    <div style="background:#ffe5e5;padding:10px;border-radius:8px;margin-bottom:10px;color:#d9534f"><?=htmlspecialchars($error)?></div>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="login" value="1">
                    <div style="margin-bottom:8px">
                        <label class="small">Username</label>
                        <input name="username" required style="width:100%;padding:10px;border-radius:8px;border:1px solid #ddd">
                    </div>
                    <div style="margin-bottom:8px">
                        <label class="small">Password</label>
                        <input type="password" name="password" required style="width:100%;padding:10px;border-radius:8px;border:1px solid #ddd">
                    </div>
                    <button class="btn" type="submit">Login</button>
                </form>
            </div>
            <div class="users">
                <h3 style="margin-top:0">All Users</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach($users as $u): ?>
                                <tr>
                                    <td><?=htmlspecialchars($u['id'])?></td>
                                    <td><?=htmlspecialchars($u['username'])?></td>
                                    <td><?=htmlspecialchars($u['role'])?></td>
                                    <td><?=htmlspecialchars($u['created_at'])?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="small">No users found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        (function(){
            var form = document.querySelector('form');
            var overlay = document.getElementById('loadingOverlay');
            var bar = document.getElementById('progressBar');
            var timer;

            function startProgress(){
                overlay.style.display = 'flex';
                bar.style.width = '6%';
                var pct = 6;
                timer = setInterval(function(){
                    // advance but never reach 100% (server/redirect will finish)
                    pct += Math.random()*8;
                    if(pct > 88) pct = 88;
                    bar.style.width = pct + '%';
                }, 400);
                // in case of very slow connections, keep subtle animation
                window.addEventListener('beforeunload', function(){
                    bar.style.width = '100%';
                });
            }

            if(form){
                form.addEventListener('submit', function(e){
                    // show overlay immediately and allow submit to continue
                    startProgress();
                });
            }
        })();
    </script>
</body>
</html>
