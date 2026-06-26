<?php
session_start();

require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect("login.php");
}

$msg = "";
$msg_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_course'])) {
    $course_name = cleanInput($_POST['course_name']);
    $course_description = cleanInput($_POST['course_description']);
    $duration = cleanInput($_POST['duration']);
    $instructor = cleanInput($_POST['instructor']);
    
    if (empty($course_name) || empty($course_description) || empty($duration) || empty($instructor)) {
        $msg = "يرجى ملء جميع الحقول المطلوبة لإضافة الدورة.";
        $msg_type = "danger";
    } else {
        $image_name = "course_default.png";
        
        if (isset($_FILES['course_image']) && $_FILES['course_image']['error'] == 0) {
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $file_name = $_FILES['course_image']['name'];
            $file_size = $_FILES['course_image']['size'];
            $file_tmp = $_FILES['course_image']['tmp_name'];
            
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            if (in_array($file_ext, $allowed_exts)) {
                // حجم الملف أقصى 2 ميجابايت
                if ($file_size <= 2 * 1024 * 1024) {
                    $new_file_name = "course_" . time() . "_" . uniqid() . "." . $file_ext;
                    $upload_path = "assets/images/" . $new_file_name;
                    
                    if (move_uploaded_file($file_tmp, $upload_path)) {
                        $image_name = $new_file_name;
                    } else {
                        $msg = "حدث خطأ أثناء حفظ ملف الصورة في الخادم.";
                        $msg_type = "danger";
                    }
                } else {
                    $msg = "حجم الصورة كبير جداً، الحد الأقصى المسموح به هو 2 ميجابايت.";
                    $msg_type = "danger";
                }
            } else {
                $msg = "صيغة الملف غير مدعومة، الصيغ المسموحة هي: JPG, JPEG, PNG, WEBP, GIF.";
                $msg_type = "danger";
            }
        }
        
        if (empty($msg)) {
            $stmt = $conn->prepare("INSERT INTO courses (course_name, course_description, duration, instructor, image) VALUES (?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sssss", $course_name, $course_description, $duration, $instructor, $image_name);
                if ($stmt->execute()) {
                    $msg = "تمت إضافة الدورة التدريبية بنجاح.";
                    $msg_type = "success";
                } else {
                    $msg = "حدث خطأ في قاعدة البيانات أثناء إضافة الدورة.";
                    $msg_type = "danger";
                }
                $stmt->close();
            } else {
                $msg = "فشل في إعداد استعلام إضافة الدورة.";
                $msg_type = "danger";
            }
        }
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    
    if ($delete_id > 0) {
        $stmt_img = $conn->prepare("SELECT image FROM courses WHERE id = ?");
        if ($stmt_img) {
            $stmt_img->bind_param("i", $delete_id);
            $stmt_img->execute();
            $res_img = $stmt_img->get_result();
            if ($res_img->num_rows > 0) {
                $course_data = $res_img->fetch_assoc();
                $img_to_delete = $course_data['image'];
                
                $defaults = ['course1.png', 'course2.png', 'course3.png', 'course_default.png'];
                if (!in_array($img_to_delete, $defaults) && file_exists("assets/images/" . $img_to_delete)) {
                    unlink("assets/images/" . $img_to_delete);
                }
            }
            $stmt_img->close();
        }
        
        $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $delete_id);
            if ($stmt->execute()) {
                $msg = "تم حذف الدورة التدريبية وجميع طلبات التسجيل المرتبطة بها بنجاح.";
                $msg_type = "success";
            } else {
                $msg = "حدث خطأ أثناء محاولة حذف الدورة من قاعدة البيانات.";
                $msg_type = "danger";
            }
            $stmt->close();
        } else {
            $msg = "فشل في إعداد استعلام الحذف.";
            $msg_type = "danger";
        }
    }
}

$courses = getCourses($conn);

require_once 'includes/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h1><i class="fa-solid fa-graduation-cap"></i> إدارة الدورات التدريبية</h1>
        <a href="dashboard.php" class="btn-dashboard btn-back"><i class="fa-solid fa-circle-chevron-left"></i> العودة للوحة التحكم</a>
    </div>

    <?php if (!empty($msg)): ?>
        <div class="alert alert-<?= $msg_type ?>">
            <i class="fa-solid <?= ($msg_type == 'success' ? 'fa-circle-check' : 'fa-circle-xmark') ?>"></i>
            <?= $msg ?>
        </div>
    <?php endif; ?>

    <div class="course-management-layout">
        <div class="form-container course-add-form">
            <h2><i class="fa-solid fa-plus-circle"></i> إضافة دورة جديدة</h2>
            <form action="manage_courses.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="course_name"><i class="fa-solid fa-book"></i> اسم الدورة التدريبية *</label>
                    <input type="text" name="course_name" id="course_name" class="form-control" required placeholder="مثال: تطوير تطبيقات الموبايل">
                </div>
                
                <div class="form-group">
                    <label for="course_description"><i class="fa-solid fa-align-right"></i> وصف الدورة *</label>
                    <textarea name="course_description" id="course_description" class="form-control" rows="3" required placeholder="اكتب تفاصيل ومحاور الدورة..."></textarea>
                </div>
                
                <div class="form-group-row" style="display: flex; gap: 16px;">
                    <div class="form-group" style="flex: 1;">
                        <label for="duration"><i class="fa-regular fa-clock"></i> مدة الدورة *</label>
                        <input type="text" name="duration" id="duration" class="form-control" required placeholder="مثال: 6 أسابيع">
                    </div>
                    
                    <div class="form-group" style="flex: 1;">
                        <label for="instructor"><i class="fa-regular fa-user"></i> اسم المدرب *</label>
                        <input type="text" name="instructor" id="instructor" class="form-control" required placeholder="مثال: أ. محمد أحمد">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="course_image"><i class="fa-regular fa-image"></i> صورة الدورة التدريبية (اختياري)</label>
                    <input type="file" name="course_image" id="course_image" class="form-control" accept="image/*">
                    <small style="color: var(--text-muted); display: block; margin-top: 4px;">الحد الأقصى للحجم 2 ميجابايت (JPG, PNG, WEBP)</small>
                </div>
                
                <button type="submit" name="add_course" class="btn-submit"><i class="fa-solid fa-square-plus"></i> إضافة الدورة الآن</button>
            </form>
        </div>

        <div class="dashboard-card course-list-card">
            <h3><i class="fa-solid fa-list-ul"></i> الدورات التدريبية المتوفرة حالياً</h3>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>صورة</th>
                            <th>الدورة</th>
                            <th>المدرب</th>
                            <th>المدة</th>
                            <th style="text-align: center;">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($courses)): ?>
                            <?php foreach ($courses as $c): ?>
                                <tr>
                                    <td>
                                        <img src="assets/images/<?= htmlspecialchars($c['image']) ?>" alt="صورة الدورة" style="width: 60px; height: 40px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                                    </td>
                                    <td><strong><?= htmlspecialchars($c['course_name']) ?></strong></td>
                                    <td><?= htmlspecialchars($c['instructor']) ?></td>
                                    <td><span class="course-badge"><?= htmlspecialchars($c['duration']) ?></span></td>
                                    <td style="text-align: center;">
                                        <a href="manage_courses.php?delete_id=<?= $c['id'] ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذه الدورة؟ سيؤدي ذلك أيضاً لحذف جميع الطلاب المسجلين بها.')" style="color: var(--danger); font-size: 1.1rem; padding: 6px 12px; border-radius: var(--radius-sm); display: inline-flex; align-items: center; gap: 4px; transition: var(--transition); border: 1px solid transparent;">
                                            <i class="fa-solid fa-trash-can"></i> حذف
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">لا توجد دورات تدريبية حالياً.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
