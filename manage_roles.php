<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
require_once './include/db_connect.php';

if (!isset($_SESSION['user'])) 
{
    echo '<p>You must be logged in to access this page.</p>';
    echo '<p><a href="./loginpages/login.php">Login here</a></p>';
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
    echo '<p>Access denied. Admin privileges required.</p>';
    echo '<p><a href="./loginpages/index.php">Back to Home</a></p>';
    exit();
}

$queryGetUsers = 'SELECT u.user_id, u.first_name, u.last_name, u.user_email,
                  GROUP_CONCAT(r.role_name SEPARATOR ", ") as roles
                  FROM User u
                  LEFT JOIN UserRole ur ON u.user_id = ur.user_id
                  LEFT JOIN Role r ON ur.role_id = r.role_id
                  WHERE u.is_active = 1
                  GROUP BY u.user_id
                  ORDER BY u.last_name, u.first_name';
$stmtUsers = $db->prepare($queryGetUsers);
$stmtUsers->execute();
$users = $stmtUsers->fetchAll();

$queryGetRoles = 'SELECT role_id, role_name FROM Role ORDER BY role_name';
$stmtRoles = $db->prepare($queryGetRoles);
$stmtRoles->execute();
$roles = $stmtRoles->fetchAll();

$queryGetPermissions = 'SELECT permission_id, permission_name FROM Permission ORDER BY permission_name';
$stmtPerms = $db->prepare($queryGetPermissions);
$stmtPerms->execute();
$permissions = $stmtPerms->fetchAll();

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
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        body {
            padding: 20px;
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
        select {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        .action-btn {
            padding: 8px 16px;
            margin-top: 5px;
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
    <header>
        <h1>Manage User Roles & Permissions</h1>
    </header>
    <main>
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
                    <th>Assign Role</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['user_email']); ?></td>
                    <td><?php echo htmlspecialchars($user['roles'] ?: 'No roles assigned'); ?></td>
                    <td>
                        <form method="POST" action="assign_role_process.php" class="role-form">
                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                            <select name="role_id" required>
                                <option value="">Select a role...</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?php echo $role['role_id']; ?>">
                                        <?php echo htmlspecialchars($role['role_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="assign_role" class="action-btn">Assign Role</button>
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
                        $queryRolePerms = 'SELECT p.permission_name 
                                          FROM RolePermission rp
                                          JOIN Permission p ON rp.permission_id = p.permission_id
                                          WHERE rp.role_id = :role_id
                                          ORDER BY p.permission_name';
                        $stmtRP = $db->prepare($queryRolePerms);
                        $stmtRP->bindParam(':role_id', $role['role_id']);
                        $stmtRP->execute();
                        $rolePerms = $stmtRP->fetchAll();
                        
                        $permNames = array_column($rolePerms, 'permission_name');
                        echo htmlspecialchars(implode(', ', $permNames));
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p style="margin-top: 30px;"><a href="./loginpages/index.php">Back to Home</a></p>
    </main>
</body>
</html>