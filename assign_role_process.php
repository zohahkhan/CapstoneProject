<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
require_once './include/db_connect.php';
if (!isset($_SESSION['user'])) 
{
    header('Location: ./loginpages/login.php');
    exit();
}
$admin_id = $_SESSION['user']['user_id'];
$queryCheckAdmin = 'SELECT COUNT(*) FROM UserRole ur
                    JOIN Role r ON ur.role_id = r.role_id
                    WHERE ur.user_id = :user_id AND r.role_name = "Admin"';
$stmt = $db->prepare($queryCheckAdmin);
$stmt->bindParam(':user_id', $admin_id);
$stmt->execute();
$isAdmin = $stmt->fetchColumn() > 0;
if (!$isAdmin) 
{
    header('Location: ./loginpages/index.php');
    exit();
}
$assign_role = filter_input(INPUT_POST, 'assign_role');
if (isset($assign_role)) 
{
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $new_role_ids = $_POST['role_ids'] ?? [];
    $new_role_ids = array_map('intval', $new_role_ids);
    $new_role_ids = array_filter($new_role_ids);

    if (!$user_id || empty($new_role_ids)) 
    {
        header('Location: manage_roles.php?error=invalid_input');
        exit();
    }

    // Get old roles for logging
    $queryGetOldRoles = 'SELECT role_id FROM UserRole WHERE user_id = :user_id';
    $stmtOldRoles = $db->prepare($queryGetOldRoles);
    $stmtOldRoles->bindParam(':user_id', $user_id);
    $stmtOldRoles->execute();
    $oldRoleIds = array_column($stmtOldRoles->fetchAll(), 'role_id');

    // Delete all existing roles for user
    $queryDelete = 'DELETE FROM UserRole WHERE user_id = :user_id';
    $stmtDelete = $db->prepare($queryDelete);
    $stmtDelete->bindParam(':user_id', $user_id);
    $stmtDelete->execute();

    // Insert each selected role
    foreach ($new_role_ids as $role_id) 
    {
        $queryInsert = 'INSERT INTO UserRole (user_id, role_id) VALUES (:user_id, :role_id)';
        $stmtInsert = $db->prepare($queryInsert);
        $stmtInsert->bindParam(':user_id', $user_id);
        $stmtInsert->bindParam(':role_id', $role_id);
        $stmtInsert->execute();
    }

    // Log each new role assigned
    foreach ($new_role_ids as $new_role_id) 
    {
        $old_role_id = !empty($oldRoleIds) ? $oldRoleIds[0] : null;
        $queryLog = 'INSERT INTO RoleChangeLog (user_id, admin_id, old_role_id, new_role_id) 
                     VALUES (:user_id, :admin_id, :old_role_id, :new_role_id)';
        $stmtLog = $db->prepare($queryLog);
        $stmtLog->bindParam(':user_id', $user_id);
        $stmtLog->bindParam(':admin_id', $admin_id);
        $stmtLog->bindParam(':old_role_id', $old_role_id);
        $stmtLog->bindParam(':new_role_id', $new_role_id);
        $stmtLog->execute();
    }

    header('Location: manage_roles.php?success=1');
    exit();
}
header('Location: manage_roles.php');
exit();
?>