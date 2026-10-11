MOVIE ONE — PHP WEBSITE

FILES
- index.php: home page, PHP login/session handling, and protected video player
- users.php: server-side demo credentials (password is hashed in PHP)
- montecristo.jpg: add your movie poster image here
- montecristo.mp4: add your movie video here
- .htaccess: optional Apache rule to block direct web access to users.php

DEMO LOGIN
Username: aaguilar
Password: Montecristo.2026

HOW TO RUN LOCALLY
1. Install PHP (or XAMPP/WAMP/MAMP).
2. Copy this folder into your web server's document root (for XAMPP, usually htdocs).
3. Add your own image named montecristo.jpg and video named montecristo.mp4.
4. Start Apache.
5. Open http://localhost/movie_one_php/ (or the folder name you used).

If PHP's built-in server is installed, open a terminal in this folder and run:
    php -S localhost:8000
Then visit:
    http://localhost:8000

VIDEO FORMAT
MP4 with H.264 video and AAC audio is broadly supported by browsers. WAV is audio-only, not a movie video format.

SECURITY
This is a starter/demo. Use HTTPS on a public server, change the demo password, and store account hashes in a database or protected configuration. Keep users.php outside the public web root when possible. The included .htaccess works only on Apache and may need adaptation for other servers.
