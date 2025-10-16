<?php
// config.php
// Put this outside webroot in production

// DB settings
// define('DB_HOST', '127.0.0.1');
define('DB_HOST', 'localhost');
define('DB_NAME', 'busly');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP default blank. Change for production.
define('DB_PORT', 3307); // Chagne it if XAMPP MySql db is configured in diffrent port.

// Session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

// CORS allowed origins - adjust for your frontend origin
define('ALLOWED_ORIGINS', '*'); // set to 'http://localhost:3000' for production
