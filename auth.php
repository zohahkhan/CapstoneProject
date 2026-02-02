<?php
// auth.php — authentication + role authorization to meet #12
require_once "db_connect.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Ensure user is logged in via DB-backed session token.
 * Returns user_id if valid.
 */
function is_https(): bool {
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
}

/**
 * Clears the DB session cookie.
 */
function clear_session_cookie(): void {
    // Delete cookie in browser
    setcookie("session", "", [
        "expires"  => time() - 3600,
        "path"     => "/",
        "secure"   => is_https(),
        "httponly" => true,
        "samesite" => "Lax"
    ]);
}

/**
 * Authenticate the request using the DB-backed session token cookie.
 * - Redirects to login.html if not authenticated.
 * - Returns the authenticated user_id as int.
 */
function require_auth(): int {
    global $db;

    $token = $_COOKIE["session"] ?? "";
    if ($token === "") {
        header("Location: login.html");
        exit;
    }

    // Validate token: exists, not revoked, not expired
    $stmt = $db->prepare("
        SELECT s.user_id
        FROM `Session` s
        WHERE s.session_id = ?
          AND s.revoked_at IS NULL
          AND s.expires_at > NOW()
        LIMIT 1
    ");
    $stmt->execute([$token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        // Token invalid/expired/revoked -> remove cookie and redirect to login
        clear_session_cookie();
        header("Location: login.html");
        exit;
    }

    // Refresh session activity timestamp for audit
    try {
        $db->prepare("
            UPDATE `Session`
            SET last_seen_at = NOW()
            WHERE session_id = ?
        ")->execute([$token]);
    } catch (PDOException $e) {
        // If last_seen_at doesn't exist in some dev schema, fail silently
    }

    $userId = (int)$row["user_id"];
    $_SESSION["user_id"] = $userId; // convenience for other pages
    return $userId;
}

/**
 * Get role names for a user 
 * Returns an array of strings, e.g. ["Member", "President"]
 */
function get_user_roles(int $userId): array {
    global $db;

    $stmt = $db->prepare("
        SELECT r.role_name
        FROM Role r
        INNER JOIN UserRole ur ON ur.role_id = r.role_id
        WHERE ur.user_id = :uid
    ");
    $stmt->execute([":uid" => $userId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
}

/**
 * Require that the user has at least one roles. If not, responds with 403 Forbidden.
 * Usage:
 *   require_once 'auth.php';
 *   require_role(['Admin/Maintenance']);
 */
function require_role(array $allowedRoles): void {
    $userId = require_auth();
    $roles = get_user_roles($userId);

    foreach ($roles as $role) {
        if (in_array($role, $allowedRoles, true)) {
            return; // access granted
        }
    }

    http_response_code(403);
    exit("Forbidden");
}

/**
 * just check role without blocking.
 */
function has_role(string $roleName): bool {
    $userId = require_auth();
    $roles = get_user_roles($userId);
    return in_array($roleName, $roles, true);
}
