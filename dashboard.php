<?php
// Start Session
session_start();

require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect("login.php");
}

$total_courses = countRecords($conn, 'courses');
$total_registrations = countRecords($conn, 'registrations');
$total_users = countRecords($conn, 'users');

require_once 'includes/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h1><i class="fa-solid fa-chart-line"></i> لوحة تحكم الإدارة</h1>
        <div class="dashboard-actions-group" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="manage_courses.php" class="btn-dashboard" style="background: var(--secondary-gradient); box-shadow: 0 4px 14px rgba(16, 185, 129, 0.2);"><i class="fa-solid fa-graduation-cap"></i> إدارة الدورات التدريبية</a>
            <a href="view_registrations.php" class="btn-dashboard"><i class="fa-solid fa-users-viewfinder"></i> عرض طلبات التسجيل</a>
        </div>
    </div>
    
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-user"></i> مرحباً بعودتك، <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong>!
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-book"></i></div>
            <h3>إجمالي الدورات</h3>
            <div class="stat-number"><?php echo $total_courses; ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-id-card"></i></div>
            <h3>إجمالي التسجيلات</h3>
            <div class="stat-number"><?php echo $total_registrations; ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-user-shield"></i></div>
            <h3>مدراء النظام</h3>
            <div class="stat-number"><?php echo $total_users; ?></div>
        </div>
    </div>
    
    <div class="dashboard-card">
        <h3><i class="fa-solid fa-info-circle"></i> نظرة عامة على النظام</h3>
        <p>مرحباً بك في لوحة تحكم المشرفين الخاصة بـ <strong>أكاديمية التعليم</strong>. يمكنك هنا الإشراف على الدورات المتاحة والطلبات المقدمة من الطلاب.</p>
        
        <div class="dashboard-loops">
            <div class="loop-section">
                <h4><i class="fa-solid fa-clipboard-list"></i> سجل الفحوصات الأمنية (تكرار For)</h4>
                <ul class="activity-list">
                    <?php
                    for ($i = 1; $i <= 3; $i++) {
                        echo "<li><i class='fa-solid fa-check-double text-success'></i> تم فحص حماية النظام بنجاح (المحاولة رقم #$i).</li>";
                    }
                    ?>
                </ul>
            </div>
            
            <div class="loop-section">
                <h4><i class="fa-solid fa-server"></i> حالة الخادم (تكرار Do-While)</h4>
                <div class="server-status">
                    <?php
                    $status = 1;
                    do {
                        echo "<p><i class='fa-solid fa-circle-check text-success animate-pulse'></i> الخادم متصل ويعمل بأعلى كفاءة (رمز الحالة: $status)</p>";
                        $status++;
                    } while ($status < 2);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
