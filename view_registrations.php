<?php
session_start();

require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect("login.php");
}

$sql = "SELECT r.id, r.student_name, r.email, r.phone, r.notes, r.registration_date, c.course_name 
        FROM registrations r 
        JOIN courses c ON r.course_id = c.id 
        ORDER BY r.registration_date DESC";
$result = $conn->query($sql);

require_once 'includes/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h1><i class="fa-solid fa-users"></i> طلبات تسجيل الطلاب</h1>
        <a href="dashboard.php" class="btn-dashboard btn-back"><i class="fa-solid fa-circle-chevron-left"></i> العودة للوحة التحكم</a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><i class="fa-solid fa-hashtag"></i></th>
                    <th><i class="fa-solid fa-user"></i> اسم الطالب</th>
                    <th><i class="fa-solid fa-address-book"></i> معلومات الاتصال</th>
                    <th><i class="fa-solid fa-graduation-cap"></i> اسم الدورة</th>
                    <th><i class="fa-solid fa-calendar-day"></i> تاريخ التسجيل</th>
                    <th><i class="fa-solid fa-comment-dots"></i> ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td><strong>" . htmlspecialchars($row['student_name']) . "</strong></td>";
                        echo "<td>";
                        echo "<span class='contact-item'><i class='fa-regular fa-envelope'></i> " . htmlspecialchars($row['email']) . "</span><br>";
                        echo "<span class='contact-item'><i class='fa-solid fa-phone-flip'></i> " . htmlspecialchars($row['phone']) . "</span>";
                        echo "</td>";
                        echo "<td><span class='course-badge'>" . htmlspecialchars($row['course_name']) . "</span></td>";
                        echo "<td>" . date('Y-m-d H:i', strtotime($row['registration_date'])) . "</td>";
                        echo "<td>" . (!empty($row['notes']) ? htmlspecialchars($row['notes']) : "<span class='text-muted'>لا يوجد</span>") . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>لا توجد طلبات تسجيل حالياً.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
