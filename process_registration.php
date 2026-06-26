<?php
session_start();

require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_registration'])) {
    
    $student_name = cleanInput($_POST['student_name']);
    $email = cleanInput($_POST['email']);
    $phone = cleanInput($_POST['phone']);
    $course_id = (int)$_POST['course_id'];
    $notes = cleanInput($_POST['notes']);
    
    if (empty($student_name) || empty($email) || empty($phone) || empty($course_id)) {
        $_SESSION['msg'] = "يرجى ملء جميع الحقول المطلوبة.";
        $_SESSION['msg_type'] = "danger";
        redirect("register.php?course_id=$course_id");
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['msg'] = "صيغة البريد الإلكتروني غير صحيحة.";
        $_SESSION['msg_type'] = "danger";
        redirect("register.php?course_id=$course_id");
    } else {
        
        $status_code = 1;
        $status_message = "";
        
        switch ($status_code) {
            case 1:
                $status_message = "تم التسجيل بنجاح! سنتواصل معك قريباً.";
                break;
            default:
                $status_message = "اكتمل الإجراء.";
        }
        
        $stmt = $conn->prepare("INSERT INTO registrations (student_name, email, phone, course_id, notes) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("sssis", $student_name, $email, $phone, $course_id, $notes);
            
            if ($stmt->execute()) {
                $_SESSION['msg'] = $status_message;
                $_SESSION['msg_type'] = "success";
            } else {
                $_SESSION['msg'] = "حدث خطأ في قاعدة البيانات: " . $stmt->error;
                $_SESSION['msg_type'] = "danger";
            }
            $stmt->close();
        } else {
            $_SESSION['msg'] = "فشل في إعداد الاستعلام.";
            $_SESSION['msg_type'] = "danger";
        }
        
        redirect("register.php");
    }
} else {
    redirect("index.php");
}
?>
