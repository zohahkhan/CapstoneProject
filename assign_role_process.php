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
    $new_role_id = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);

    if (!$user_id || !$new_role_id) 
    {
        header('Location: manage_roles.php?error=invalid_input');
        exit();
    }

    $queryGetOldRole = 'SELECT role_id FROM UserRole WHERE user_id = :user_id LIMIT 1';
    $stmtOldRole = $db->prepare($queryGetOldRole);
    $stmtOldRole->bindParam(':user_id', $user_id);
    $stmtOldRole->execute();
    $oldRoleData = $stmtOldRole->fetch();
    $old_role_id = $oldRoleData ? $oldRoleData['role_id'] : null;

    $queryCheckExisting = 'SELECT user_role_id FROM UserRole 
                          WHERE user_id = :user_id LIMIT 1';
    $stmtCheck = $db->prepare($queryCheckExisting);
    $stmtCheck->bindParam(':user_id', $user_id);
    $stmtCheck->execute();
    $existingRole = $stmtCheck->fetch();

    if ($existingRole) 
    {
        $queryUpdateRole = 'UPDATE UserRole SET role_id = :role_id WHERE user_id = :user_id';
        $stmtUpdate = $db->prepare($queryUpdateRole);
        $stmtUpdate->bindParam(':role_id', $new_role_id);
        $stmtUpdate->bindParam(':user_id', $user_id);
        $stmtUpdate->execute();
    } 
    else 
    {
        $queryAssignRole = 'INSERT INTO UserRole (user_id, role_id) 
                           VALUES (:user_id, :role_id)';
        $stmtAssign = $db->prepare($queryAssignRole);
        $stmtAssign->bindParam(':user_id', $user_id);
        $stmtAssign->bindParam(':role_id', $new_role_id);
        $stmtAssign->execute();
    }

    $queryLogChange = 'INSERT INTO RoleChangeLog (user_id, admin_id, old_role_id, new_role_id) 
                      VALUES (:user_id, :admin_id, :old_role_id, :new_role_id)';
    $stmtLog = $db->prepare($queryLogChange);
    $stmtLog->bindParam(':user_id', $user_id);
    $stmtLog->bindParam(':admin_id', $admin_id);
    $stmtLog->bindParam(':old_role_id', $old_role_id);
    $stmtLog->bindParam(':new_role_id', $new_role_id);
    $stmtLog->execute();

    header('Location: manage_roles.php?success=1');
    exit();
}

header('Location: manage_roles.php');
exit();
?>