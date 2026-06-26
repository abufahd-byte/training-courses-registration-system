<?php
session_start();

require_once 'includes/db.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect("dashboard.php");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = cleanInput($_POST['username']);
    $password = cleanInput($_POST['password']);
    
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $username;
                
                redirect("dashboard.php");
            } else {
                $error = "اسم المستخدم أو كلمة المرور غير صحيحة.";
            }
        } else {
            $error = "اسم المستخدم أو كلمة المرور غير صحيحة.";
        }
        $stmt->close();
    }
}

require_once 'includes/header.php';
?>

<div class="container login-section">
    <div class="form-container login-container">
        <h2><i class="fa-solid fa-lock-open"></i> دخول الإدارة</h2>
        <p class="form-subtitle">يرجى إدخال بيانات الاعتماد للوصول إلى لوحة التحكم.</p>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username"><i class="fa-solid fa-user-gear"></i> اسم المستخدم</label>
                <input type="text" name="username" id="username" class="form-control" required placeholder="أدخل اسم المستخدم">
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fa-solid fa-key"></i> كلمة المرور</label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="أدخل كلمة المرور">
            </div>
            
            <button type="submit" name="login" class="btn-submit"><i class="fa-solid fa-right-to-bracket"></i> تسجيل الدخول</button>
        </form>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
