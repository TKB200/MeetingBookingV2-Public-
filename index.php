<?php
require_once 'includes/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        if (login($username, $password)) {
            if (isAdmin()) {
                header("Location: admin/index.php");
            } else {
                header("Location: user/index.php");
            }
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } elseif (isset($_POST['register'])) {
        $username = $_POST['reg_username'];
        $password = $_POST['reg_password'];
        $confirm_password = $_POST['reg_confirm_password'];
        $email = $_POST['reg_email'] ?? null;
        $contact = $_POST['reg_contact'] ?? null;

        if ($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            if (register($username, $password, $email, $contact)) {
                $success = "Registration successful! Please login.";
            } else {
                $error = "Username already exists.";
            }
        }
    }

}


?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting Booking.Deyvesta.com - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: linear-gradient(45deg, #8f2edf6c, #36ff7967);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Outfit', sans-serif;
        }

        .container {
            background: #ffffff;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-container img {
            max-width: 150px;
            height: auto;
            filter: drop-shadow(6px 6px 10px rgba(0, 0, 0, 0.25));
            -webkit-filter: drop-shadow(6px 6px 10px rgba(0, 0, 0, 0.25));
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 2rem;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 400;
        }

        input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            width: 100%;
            padding: 1rem;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #5a6fd6;
        }

        .toggle-form {
            text-align: center;
            margin-top: 1rem;
            color: #666;
            cursor: pointer;
        }

        .toggle-form span {
            color: #667eea;
            font-weight: 600;
        }

        .alert {
            padding: 0.8rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            text-align: center;
            font-size: 0.9rem;
        }

        .alert-error {
            background: #ffe5e5;
            color: #d9534f;
        }

        .alert-success {
            background: #e5ffe5;
            color: #5cb85c;
        }

        #register-form {
            display: none;
        }

        /* removed top-right button styles */
        /* Loading overlay and progress bar */
        .loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.35);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999
        }

        .progress-wrap {
            width: 80%;
            max-width: 560px;
            background: rgba(255, 255, 255, 0.95);
            padding: 18px;
            border-radius: 10px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            text-align: left
        }

        .progress-label {
            font-size: 0.95rem;
            color: #333;
            margin-bottom: 8px
        }

        .progress-track {
            background: #eee;
            height: 10px;
            border-radius: 10px;
            overflow: hidden
        }

        .progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #667eea, #43e97b);
            transition: width 0.2s ease
        }
    </style>

    <!-- EmailJS Configuration -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js">
    </script>
    <script type="text/javascript">
        (function () {
            emailjs.init({
                publicKey: "2v-ZV-reQ1aX-0GRH",
            });
        })();
    </script>
</head>

<body>
    <!-- User Dashboard button removed -->
    <!-- Loading overlay shown on form submit -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="progress-wrap">
            <div class="progress-label">Processing — please wait...</div>
            <div class="progress-track">
                <div id="progressBar" class="progress-bar"></div>
            </div>
        </div>
    </div>
    <div class="container">
        <!-- Login Form -->
        <div id="login-form-container">
            <div class="logo-container">
                <img src="assets/images/logo.png" alt="Logo" width="80" height="80">
            </div>
            <h3>
                <center>Meeting Booking</center>
            </h3>
            <?php if ($error && !isset($_POST['register']))
                echo "<div class='alert alert-error'>$error</div>"; ?>
            <?php if ($success)
                echo "<div class='alert alert-success'>$success</div>"; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="Enter your username">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Enter your password">
                </div>
                <button type="submit" name="login" class="btn">Login</button>
            </form>
            <div class="toggle-form" onclick="toggleForm('register')">
                Don't have an account? <span>Register here</span>
            </div>
        </div>

        <!-- Register Form -->
        <div id="register-form-container" style="display: none;">
            <div class="logo-container">
                <img src="assets/images/logo.png" alt="Logo" width="80" height="80">
            </div>
            <h3>
                <center>Create Account</center>
            </h3>
            <?php if ($error && isset($_POST['register']))
                echo "<div class='alert alert-error'>$error</div>"; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="reg_username" required placeholder="Choose a username">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="reg_email" required placeholder="Enter your email">
                </div>
                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text" name="reg_contact" placeholder="Enter contact number">
                </div>
                <div class="form-group">
                    <label></label>Password</label>
                    <input type="password" name="reg_password" required placeholder="Create a password">
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="reg_confirm_password" required placeholder="Confirm your password">
                </div>
                <button type="submit" name="register" class="btn">Register</button>
            </form>
            <div class="toggle-form" onclick="toggleForm('login')">
                Already have an account? <span>Login here</span>
            </div>
        </div>
    </div>

    <script>
        function toggleForm(form) {
            const loginForm = document.getElementById('login-form-container');
            const registerForm = document.getElementById('register-form-container');

            if (form === 'register') {
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
            } else {
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
            }
        }

        (function () {
            var forms = document.querySelectorAll('form');
            var overlay = document.getElementById('loadingOverlay');
            var bar = document.getElementById('progressBar');
            var timer;

            function startProgress() {
                overlay.style.display = 'flex';
                bar.style.width = '6%';
                var pct = 6;
                timer = setInterval(function () {
                    pct += Math.random() * 8;
                    if (pct > 88) pct = 88;
                    bar.style.width = pct + '%';
                }, 400);
                window.addEventListener('beforeunload', function () {
                    bar.style.width = '100%';
                });
            }

            forms.forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    startProgress();
                });
            });

            // Re-show register form if there was a registration error
            <?php if (isset($_POST['register']) && $error): ?>
                toggleForm('register');
            <?php endif; ?>
        })();
    </script>


    <!-- EmailJS Configuration -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            emailjs.send("service_3g4opoh", "template_gedc24v", {
                username: "<?php echo $username; ?>",
                email: "<?php echo $email; ?>",
                contact: "<?php echo $contact; ?>"
            }).then(function (response) {
                console.log("Email sent!", response.status);
            }, function (error) {
                console.log("Email failed:", error);
            });

        });
    </script>
</body>

</html>