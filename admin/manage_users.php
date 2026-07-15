<?php
require_once 'includes/admin_init.php';
require_admin_login();
$page_title = "Manage Admin Users";
$error = "";
$success = "";

// ADD new admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $full_name = clean_input($conn, $_POST['full_name']);
    $username = clean_input($conn, $_POST['username']);
    $password = $_POST['password'];
    $role = clean_input($conn, $_POST['role']);

    if ($full_name == '' || $username == '' || $password == '') {
        $error = "All fields are required.";
    } else {
        $check = mysqli_query($conn, "SELECT id FROM admins WHERE username='$username'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Username already taken.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn, "INSERT INTO admins (full_name, username, password, role, created_at)
                                  VALUES ('$full_name','$username','$hashed','$role', NOW())");
            log_admin_action($conn, $_SESSION['admin_id'], $_SESSION['admin_username'], 'ADD_ADMIN', "Added new admin user: $username");
            $success = "Admin user added.";
        }
    }
}

// UPDATE existing admin (name/role, optional password reset)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = clean_input($conn, $_POST['id']);
    $full_name = clean_input($conn, $_POST['full_name']);
    $role = clean_input($conn, $_POST['role']);
    $new_password = $_POST['new_password'];

    mysqli_query($conn, "UPDATE admins SET full_name='$full_name', role='$role' WHERE id='$id'");

    if ($new_password !== '') {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE admins SET password='$hashed' WHERE id='$id'");
    }

    log_admin_action($conn, $_SESSION['admin_id'], $_SESSION['admin_username'], 'EDIT_ADMIN', "Edited admin user ID $id");
    $success = "Admin user updated.";
}

$edit_row = null;
if (isset($_GET['edit'])) {
    $edit_id = clean_input($conn, $_GET['edit']);
    $edit_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM admins WHERE id='$edit_id'"));
}

$admins = mysqli_query($conn, "SELECT * FROM admins ORDER BY id");

include 'includes/admin_header.php';
?>
<h1>Manage Admin Users</h1>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

<h2><?php echo $edit_row ? 'Edit Admin User' : 'Add New Admin User'; ?></h2>
<form method="POST" action="manage_users.php">
    <?php if ($edit_row): ?>
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?php echo $edit_row['id']; ?>">
        Full Name: <input type="text" name="full_name" value="<?php echo htmlspecialchars($edit_row['full_name']); ?>"><br><br>
        Role:
        <select name="role">
            <option value="admin" <?php echo $edit_row['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
            <option value="superadmin" <?php echo $edit_row['role'] == 'superadmin' ? 'selected' : ''; ?>>Superadmin</option>
        </select><br><br>
        New Password (leave blank to keep current): <input type="password" name="new_password"><br><br>
        <input type="submit" value="Save Changes">
    <?php else: ?>
        <input type="hidden" name="action" value="add">
        Full Name: <input type="text" name="full_name"><br><br>
        Username: <input type="text" name="username"><br><br>
        Password: <input type="password" name="password"><br><br>
        Role:
        <select name="role">
            <option value="admin">Admin</option>
            <option value="superadmin">Superadmin</option>
        </select><br><br>
        <input type="submit" value="Add Admin User">
    <?php endif; ?>
</form>

<h2>Existing Admin Users</h2>
<table border="1" cellpadding="6">
<tr><th>ID</th><th>Full Name</th><th>Username</th><th>Role</th><th>Created</th><th></th></tr>
<?php while ($a = mysqli_fetch_assoc($admins)): ?>
    <tr>
        <td><?php echo $a['id']; ?></td>
        <td><?php echo htmlspecialchars($a['full_name']); ?></td>
        <td><?php echo htmlspecialchars($a['username']); ?></td>
        <td><?php echo htmlspecialchars($a['role']); ?></td>
        <td><?php echo $a['created_at']; ?></td>
        <td><a href="manage_users.php?edit=<?php echo $a['id']; ?>">Edit</a></td>
    </tr>
<?php endwhile; ?>
</table>

<?php include 'includes/admin_footer.php'; ?>
