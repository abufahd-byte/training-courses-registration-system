<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

require_once 'includes/header.php';

$selected_course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

$courses = getCourses($conn);
?>

<div class="container">
    <div class="form-container">
        <h2><i class="fa-solid fa-file-signature"></i> نموذج التسجيل في دورة تدريبية</h2>
        <p class="form-subtitle">يرجى ملء الحقول التالية لإتمام عملية التسجيل. الحقول التي تحتوي على (*) إلزامية.</p>
        
        <?php
        if (isset($_SESSION['msg'])) {
            $msgType = $_SESSION['msg_type']; // 'success' أو 'danger'
            echo '<div class="alert alert-' . $msgType . '"><i class="fa-solid ' . ($msgType == 'success' ? 'fa-circle-check' : 'fa-circle-xmark') . '"></i> ' . $_SESSION['msg'] . '</div>';
            unset($_SESSION['msg']);
            unset($_SESSION['msg_type']);
        }
        ?>

        <form action="process_registration.php" method="POST" id="registrationForm">
            <div class="form-group">
                <label for="student_name"><i class="fa-solid fa-user"></i> الاسم الكامل *</label>
                <input type="text" name="student_name" id="student_name" class="form-control" required placeholder="أدخل اسمك الكامل الثلاثي">
            </div>
            
            <div class="form-group">
                <label for="email"><i class="fa-solid fa-envelope"></i> البريد الإلكتروني *</label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="name@example.com">
            </div>
            
            <div class="form-group">
                <label for="phone"><i class="fa-solid fa-phone"></i> رقم الهاتف *</label>
                <input type="tel" name="phone" id="phone" class="form-control" required placeholder="مثال: 0512345678">
            </div>
            
            <div class="form-group">
                <label for="course_id"><i class="fa-solid fa-graduation-cap"></i> اختر الدورة التدريبية *</label>
                <select name="course_id" id="course_id" class="form-control" required>
                    <option value="">-- اختر الدورة المطلوبة --</option>
                    <?php
                    foreach ($courses as $c) {
                        $selected = ($c['id'] == $selected_course_id) ? 'selected' : '';
                        echo '<option value="' . $c['id'] . '" ' . $selected . '>' . htmlspecialchars($c['course_name']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="notes"><i class="fa-solid fa-comment-dots"></i> ملاحظات إضافية (اختياري)</label>
                <textarea name="notes" id="notes" class="form-control" rows="4" placeholder="هل لديك أي استفسار أو تفاصيل أخرى ترغب في مشاركتها معنا؟"></textarea>
            </div>
            
            <button type="submit" name="submit_registration" class="btn-submit"><i class="fa-solid fa-paper-plane"></i> إرسال طلب التسجيل</button>
        </form>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
