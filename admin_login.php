<?php
session_start();
require_once 'config.php';

$username = $password = "";
$username_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username or email.";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($username_err) && empty($password_err)) {
        $sql = "SELECT id, username, email, password FROM admins WHERE username = ? OR email = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $username, $username);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $db_username, $db_email, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            $_SESSION["admin_loggedin"] = true;
                            $_SESSION["admin_id"] = $id;
                            $_SESSION["admin_username"] = $db_username;

                            header("Location: admin_dashboard.php");
                            exit;
                        } else {
                            $login_err = "Invalid credentials.";
                        }
                    }
                } else {
                    $login_err = "Invalid credentials.";
                }
            } else {
                $login_err = "Something went wrong. Please try again.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="css style.css">
    <link rel="stylesheet" href="css auth.css">
</head>
<body>
    <div class="auth-container">
        <h2>Admin Login</h2>
        <?php if (!empty($login_err)) echo "<p class='error-message'>$login_err</p>"; ?>
        <form method="POST" action="">
            <label>Username or Email</label>
            <input type="text" name="username" required value="<?php echo htmlspecialchars($username); ?>">

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
