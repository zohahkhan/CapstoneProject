<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
require_once __DIR__ . '/../include/db_connect.php';

if (!isset($_SESSION['user'])) 
{
    echo '<p>You must be logged in to access this page.</p>';
    echo '<p><a href="../login.php">Login here</a></p>';
    exit();
}

$current_user_id = $_SESSION['user']['user_id'];

$queryCheckAdmin = 'SELECT COUNT(*) FROM UserRole ur
                    JOIN Role r ON ur.role_id = r.role_id
                    WHERE ur.user_id = :user_id AND r.role_name = "Admin"';
$stmt = $db->prepare($queryCheckAdmin);
$stmt->bindParam(':user_id', $current_user_id);
$stmt->execute();
$isAdmin = $stmt->fetchColumn() > 0;

if (!$isAdmin) 
{
    require_once __DIR__ . '/../include/config.php';
	$error_page = BASE_URL.'/include/error.php';
    header("Location: $error_page");
    exit();
}

$queryGetUsers = 'SELECT u.user_id, u.first_name, u.last_name, u.user_email
                  FROM User u
                  WHERE u.is_active = 1
                  ORDER BY u.last_name, u.first_name';
$stmtUsers = $db->prepare($queryGetUsers);
$stmtUsers->execute();
$users = $stmtUsers->fetchAll();

$queryGetRoles = 'SELECT role_id, role_name FROM Role ORDER BY role_name';
$stmtRoles = $db->prepare($queryGetRoles);
$stmtRoles->execute();
$roles = $stmtRoles->fetchAll();

$queryAllUserRoles = 'SELECT user_id, role_id FROM UserRole';
$stmtAllRoles = $db->prepare($queryAllUserRoles);
$stmtAllRoles->execute();
$userRolesMap = [];
foreach ($stmtAllRoles->fetchAll() as $row) 
{
    $userRolesMap[$row['user_id']][] = $row['role_id'];
}

$success_message = '';
if (isset($_GET['success']) && $_GET['success'] == 1) 
{
    $success_message = 'Role assignment updated successfully!';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage User Roles</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../style.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 40px 20px;
            background-image: url('../images/background.png');
            background-size: cover;
            background-attachment: fixed;
            background-repeat: no-repeat;
            min-height: 100vh;
            margin: 0;
        }
        h1 {
            text-align: center;
            width: 100%;
        }
        .content-box {
            width: 80%;
            max-width: 1400px;
            background-color: #faf5f0;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
            padding: 40px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #c4a484;
            color: white;
        }
        .success {
            color: green;
            font-weight: bold;
            margin: 10px 0;
        }
        .role-form {
            margin-top: 10px;
        }
        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
            text-align: left;
        }
        .checkbox-group label {
            font-size: 0.9em;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .action-btn {
            padding: 8px 16px;
            margin-top: 8px;
            border-radius: 8px;
            border: none;
            background-color: #c4a484;
            color: white;
            cursor: pointer;
            font-size: 0.9em;
        }
        .action-btn:hover {
            background-color: #b39578;
        }
    </style>
</head>
<body>
    <h1>Manage User Roles & Permissions</h1>
    <div class="content-box">
        <?php if ($success_message): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>
        
        <h2>Active Users</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Current Roles</th>
                    <th>Assign Roles</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <?php $currentRoles = $userRolesMap[$user['user_id']] ?? []; ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['user_email']); ?></td>
                    <td>
                        <?php
                        $roleNames = [];
                        foreach ($roles as $r) {
                            if (in_array($r['role_id'], $currentRoles)) {
                                $roleNames[] = htmlspecialchars($r['role_name']);
                            }
                        }
                        echo implode(', ', $roleNames) ?: 'No roles assigned';
                        ?>
                    </td>
                    <td>
                        <form method="POST" action="assign_role_process.php" class="role-form">
                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                            <div class="checkbox-group">
                                <?php foreach ($roles as $role): ?>
                                <label>
                                    <input type="checkbox" name="role_ids[]" value="<?php echo $role['role_id']; ?>"
                                        <?php echo in_array($role['role_id'], $currentRoles) ? 'checked' : ''; ?>>
                                    <?php echo htmlspecialchars($role['role_name']); ?>
                                </label>
                                <?php endforeach; ?>
                            </div>
                            <button type="submit" name="assign_role" class="action-btn">Save Roles</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2 style="margin-top: 40px;">Role Permissions</h2>
        <table>
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Permissions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $role): ?>
                <tr>
                    <td><?php echo htmlspecialchars($role['role_name']); ?></td>
                    <td>
                        <?php
                        $queryRolePerms = 'SELECT p.perm_title 
                                          FROM RolePermission rp
                                          JOIN Permission p ON rp.permission_id = p.permission_id
                                          WHERE rp.role_id = :role_id
                                          ORDER BY p.perm_title';
                        $stmtRP = $db->prepare($queryRolePerms);
                        $stmtRP->bindParam(':role_id', $role['role_id']);
                        $stmtRP->execute();
                        $rolePerms = $stmtRP->fetchAll();
                        $permNames = array_column($rolePerms, 'perm_title');
                        echo htmlspecialchars(implode(', ', $permNames));
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p style="margin-top: 30px;"><a href="../index.php" style="color: #4b3d29;">Back to Home</a></p>
    </div>
</body>
</html>
