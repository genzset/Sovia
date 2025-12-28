<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: users/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SOVIA - Social Platform</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        :root {
            --yellow: #FFF345;
            --orange: #ffb347;
            --dark: #0a0a0a;
            --text: #0c0c0c;
            --accent: #0f0;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Inter", Arial, sans-serif;
            /* background: linear-gradient(145deg, var(--yellow), var(--orange)); */
            background-image: url('uploads/wcbg.png');
            background-position: center -100px;
background-size: cover;
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .hero {
            background: rgba(255,255,255,0.82);
            border: 1px solid rgba(0,0,0,0.05);
            border-radius: 24px;
            width: 100%;
            max-width: 1100px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.12);
            position: relative;
            overflow: hidden;
        }
        .hero::after {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 25% 20%, rgba(255,255,255,0.35), transparent 30%),
                        radial-gradient(circle at 80% 10%, rgba(255,255,255,0.22), transparent 28%);
            pointer-events: none;
        }
        .content {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 40px;
            padding: 60px 70px;
        }
        h1 {
            font-size: clamp(32px, 5vw, 50px);
            line-height: 1.05;
            margin: 0 0 20px;
            letter-spacing: -1px;
        }
        .sub {
            font-size: 18px;
            color: #333;
            margin-bottom: 30px;
        }
        .cta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        .btn {
            border: none;
            border-radius: 999px;
            padding: 14px 22px;
            font-weight: 700;
            cursor: pointer;
            font-size: 15px;
            text-decoration: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.2s ease;
        }
        .btn-primary {
            background: #111;
            color: #fff;
            box-shadow: 0 10px 24px rgba(0,0,0,0.25);
        }
        .btn-primary:hover { transform: translateY(-2px); }
        .btn-ghost {
            background: #fff;
            color: #111;
            border: 1px solid #ddd;
        }
        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 18px;
        }
        .tag {
            background: #111;
            color: #fff;
            border-radius: 10px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 600;
        }
        .illustration {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #111;
            color: #fff;
            border-radius: 18px;
            padding: 24px;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 16px 50px rgba(0,0,0,0.3);
        }
        .card h3 {
            margin: 0 0 10px;
            font-size: 18px;
        }
        .card p { margin: 0; color: #bbb; line-height: 1.5; font-size: 14px; }
        .floating {
            position: absolute;
            background: #fff;
            color: #000;
            padding: 12px 14px;
            border-radius: 14px;
            font-weight: 700;
            box-shadow: 0 12px 30px rgba(0,0,0,0.18);
        }
        .f1 { top: 12%; right: -8%; }
        .f2 { bottom: 10%; left: -6%; }
        .logo {
            font-weight: 800;
            font-size: 20px;
            letter-spacing: -0.5px;
            margin-bottom: 16px;
        }
        .loginbtn {
            margin-top: 40px;
            position: relative;
            left: 1000px;
        }
        .loginbtn a {
            text-decoration: none;
            color: #000;
            font-weight: bold;
        }
        @media (max-width: 900px) {
            .content { grid-template-columns: 1fr; padding: 40px 26px; }
            .illustration { order: -1; }
            .floating { display: none; }
        }
    </style>
</head>
<body>
    <div class="hero">
    <div class="loginbtn">
            <a href="auth/login.php">Login</a>
        </div>
        <div class="content">
       
            <div>

            <div class="login-header">
            <img src="uploads/logo.png" alt="Logo" class="login-logo">
            <div>
                <h2 class="app-name" style="margin-top: 7px;">SOVIA</h2>
                <p class="app-subtitle" style="margin-top: 5px;">Social Sharing Platform</p>
            </div>
        </div>
                <h1>Socialize, build tribes, sharing together.</h1>
                <div class="sub">Platform sosial untuk bikin komunitas, berbagi momen, dan bangun tribes bareng teman-teman kamu.</div>
                <div class="cta">
                    <a class="btn btn-primary" href="auth/login.php">Login</a>
                    <a class="btn btn-ghost" href="auth/register.php">Create Account</a>
                </div>
                <div class="tags">
                    <span class="tag">Create Tribes</span>
                    <span class="tag">Share Status</span>
                    <span class="tag">Join Communities</span>
                </div>
            </div>
            <div class="illustration">
                <div class="card">
                    <h3>Welcome to SOVIA</h3>
                    <p>Buat tribe kamu, undang teman, dan mulai berbagi cerita dalam hitungan detik.</p>
                </div>
                <div class="floating f1">+11k pengguna</div>
                <div class="floating f2">Trending Tribes</div>
            </div>
        </div>
        
    </div>
</body>
</html>





