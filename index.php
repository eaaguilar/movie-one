<?php
session_start();

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$error = '';
$success = '';

if (isset($_GET['logout'])) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    // Credentials are checked on the server, not in browser JavaScript.
    require __DIR__ . '/users.php';
    if (isset($users[$username]) && password_verify($password, $users[$username])) {
        session_regenerate_id(true);
        $_SESSION['username'] = $username;
        header('Location: index.php');
        exit;
    }
    $error = 'Invalid username or password.';
}

$isLoggedIn = isset($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Movie One</title>
<style>
:root{--blue:#2878c8;--blue-dark:#1f65aa;--ink:#263445;--muted:#64748b;--panel:#fff;--pale:#eaf2fa}
*{box-sizing:border-box}
body{margin:0;font-family:Arial,Helvetica,sans-serif;background:#f4f7fb;color:var(--ink)}
header{display:flex;justify-content:space-between;align-items:center;gap:16px;padding:18px clamp(18px,5vw,60px);background:#fff;box-shadow:0 2px 9px #1c355018}
.brand{margin:0;color:var(--blue);font-size:30px}
nav a{color:#4b5b6c;text-decoration:none;font-weight:bold;margin-left:22px}
nav a:hover{color:var(--blue)}
.hero{padding:48px 18px;background:linear-gradient(135deg,#e5f1fc,#f7faff);text-align:center}
.movie-card{max-width:900px;margin:auto;padding:30px;background:#fff;border:1px solid #e2eaf2;border-radius:18px;box-shadow:0 8px 28px #25364a12}
.poster{width:min(280px,100%);height:390px;object-fit:cover;border-radius:12px;background:#e5edf5;box-shadow:0 6px 18px #182b4030}
h2{color:var(--blue);margin:20px 0 10px}
p{line-height:1.65;color:var(--muted)}
.section{padding:42px 18px}
.panel{max-width:960px;margin:auto;background:#fff;padding:28px;border:1px solid #e1e8ef;border-radius:16px;box-shadow:0 5px 20px #25364a0c}
.login-panel{max-width:460px;text-align:center}
input{display:block;width:100%;padding:13px 14px;margin:12px 0;border:1px solid #cbd5e1;border-radius:8px;background:#fbfdff;font-size:16px}
input:focus{outline:2px solid #b7d8fa;border-color:var(--blue)}
button,.button{display:inline-block;border:0;border-radius:8px;background:var(--blue);color:white;padding:13px 18px;font-size:16px;font-weight:bold;cursor:pointer;text-decoration:none}
button:hover,.button:hover{background:var(--blue-dark)}
.error{color:#b42318;font-weight:bold}.welcome{color:#247348;font-weight:bold}
.player-shell{background:#e7edf3;border:1px solid #ccd7e2;border-radius:12px;padding:12px}
video{display:block;width:100%;max-height:70vh;aspect-ratio:16/9;background:#050505;border-radius:7px}
.player-meta{display:flex;align-items:center;gap:16px;margin-top:18px;padding:15px;background:#f7fafc;border-radius:10px}
.disc{width:62px;height:62px;flex:0 0 62px;border-radius:50%;border:1px solid #b6c6d5;background:radial-gradient(circle,#657789 0 5px,#d9e4ed 6px 15px,#a8c1d5 16px,#f4f8fb 27px,#a7bdcf 28px)}
footer{text-align:center;padding:24px;background:#fff;border-top:1px solid #e4eaf0;color:#7b8794}
.small{font-size:13px;color:#7b8794}
@media(max-width:600px){header{padding:16px;flex-wrap:wrap}.brand{font-size:26px}nav a{margin:0 14px 0 0}.movie-card,.panel{padding:20px}.poster{height:340px}.player-meta{align-items:flex-start}}
</style>
</head>
<body>
<header>
  <h1 class="brand">Movie One</h1>
  <nav>
    <a href="index.php">Home</a>
    <?php if ($isLoggedIn): ?>
      <a href="#player">Movie player</a>
      <a href="index.php?logout=1">Close session</a>
    <?php else: ?>
      <a href="#login">Begin session</a>
    <?php endif; ?>
  </nav>
</header>

<main>
<section class="hero">
  <div class="movie-card">
    <img class="poster" src="montecristo.jpg" alt="The Count of Monte Cristo poster">
    <h2>The Count of Monte Cristo</h2>
    <p>Welcome to <strong>Movie One</strong>. Enter your account to access the movie player and enjoy this classic tale of adventure, betrayal, justice, and revenge.</p>
  </div>
</section>

<?php if (!$isLoggedIn): ?>
<section class="section" id="login">
  <div class="panel login-panel">
    <h2>Begin Session</h2>
    <?php if ($error !== ''): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
    <form method="post" action="index.php" autocomplete="on">
      <input type="hidden" name="action" value="login">
      <label for="username">Username</label>
      <input id="username" name="username" type="text" autocomplete="username" required>
      <label for="password">Password</label>
      <input id="password" name="password" type="password" autocomplete="current-password" required>
      <button type="submit">Begin Session</button>
    </form>
    <p class="small">Sign-in is checked by PHP on the server.</p>
  </div>
</section>
<?php else: ?>
<section class="section" id="player">
  <div class="panel">
    <p class="welcome">Session started. Welcome, <?= e((string)$_SESSION['username']) ?>.</p>
    <h2>🎬 The Count of Monte Cristo</h2>
    <div class="player-shell">
      <video controls preload="metadata" poster="montecristo.jpg">
        <source src="montecristo.mp4" type="video/mp4">
        Your browser cannot play this video. Please check the MP4 file and codec.
      </video>
    </div>
    <div class="player-meta">
      <div class="disc" aria-hidden="true"></div>
      <div><strong>Movie One</strong><br>The Count of Monte Cristo<br><span class="small">Video file: MP4 (H.264 recommended)</span></div>
    </div>
    <p>Use the player controls to play, pause, change volume, seek, or enter fullscreen.</p>
    <a class="button" href="index.php?logout=1">Close Session</a>
  </div>
</section>
<?php endif; ?>
</main>
<footer>&copy; <?= date('Y') ?> Movie One</footer>
</body>
</html>
