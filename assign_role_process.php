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

    $queryCheckExisting = 'SELECT COUNT(*) FROM UserRole 
                          WHERE user_id = :user_id AND role_id = :role_id';
    $stmtCheck = $db->prepare($queryCheckExisting);
    $stmtCheck->bindParam(':user_id', $user_id);
    $stmtCheck->bindParam(':role_id', $new_role_id);
    $stmtCheck->execute();
    $alreadyHasRole = $stmtCheck->fetchColumn() > 0;

    if (!$alreadyHasRole) 
    {
        $queryDeleteOldRoles = 'DELETE FROM UserRole WHERE user_id = :user_id';
        $stmtDelete = $db->prepare($queryDeleteOldRoles);
        $stmtDelete->bindParam(':user_id', $user_id);
        $stmtDelete->execute();

        $queryAssignRole = 'INSERT INTO UserRole (user_id, role_id) 
                           VALUES (:user_id, :role_id)';
        $stmtAssign = $db->prepare($queryAssignRole);
        $stmtAssign->bindParam(':user_id', $user_id);
        $stmtAssign->bindParam(':role_id', $new_role_id);
        $stmtAssign->execute();

        $queryLogChange = 'INSERT INTO RoleChangeLog (user_id, admin_id, old_role_id, new_role_id) 
                          VALUES (:user_id, :admin_id, :old_role_id, :new_role_id)';
        $stmtLog = $db->prepare($queryLogChange);
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