<!--logout.php the user exit page-->
<?php
require_once './include/db_connect.php';
// start the session
session_start();
// If a DB-backed session cookie exists, revoke it
if (isset($_COOKIE['session'])) {
    try {
        // Remove or revoke the session token in the database
        $stmt = $db->prepare(
            'UPDATE `Session`
             SET revoked_at = NOW()
             WHERE session_id = :session_id'
        );
        $stmt->execute([
            ':session_id' => $_COOKIE['session']
        ]);
    } catch (PDOException $e) {
        // Fail silently avoid breaking logout
    }

    // Delete the session cookie from the browser
    setcookie('session', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
        ),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

// CHANGE: Clear all PHP session variables
$_SESSION = [];
// destroy the session
session_destroy();

// redirect the user to the login page
header("Location: index.php");
exit();
?>
