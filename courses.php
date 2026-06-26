<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

require_once 'includes/header.php';

$courses = getCourses($conn);
?>

<div class="container">
    <h1 class="page-title"><i class="fa-solid fa-graduation-cap"></i> الدورات التدريبية المتاحة</h1>
    
    <div class="courses-grid">
        <?php
        if (!empty($courses)) {
            foreach ($courses as $course) {
                $id = $course['id'];
                $title = $course['course_name'];
                $desc = $course['course_description'];
                $duration = $course['duration'];
                $instructor = $course['instructor'];
                $image = $course['image'];
                
                echo '<div class="course-card">';
                echo '  <div class="course-img-wrapper">';
                echo '      <img src="assets/images/' . htmlspecialchars($image) . '" alt="' . htmlspecialchars($title) . '" class="course-img">';
                echo '  </div>';
                echo '  <div class="course-content">';
                echo '      <h3>' . htmlspecialchars($title) . '</h3>';
                echo '      <p class="course-desc">' . htmlspecialchars($desc) . '</p>';
                echo '      <div class="course-meta">';
                echo '          <span><i class="fa-regular fa-clock"></i> <strong>المدة:</strong> ' . htmlspecialchars($duration) . '</span>';
                echo '          <span><i class="fa-regular fa-user"></i> <strong>المدرب:</strong> ' . htmlspecialchars($instructor) . '</span>';
                echo '      </div>';
                echo '      <a href="register.php?course_id=' . $id . '" class="btn-register"><i class="fa-solid fa-file-signature"></i> سجل الآن في الدورة</a>';
                echo '  </div>';
                echo '</div>';
            }
        } else {
            echo '<div class="alert alert-info">لا توجد دورات تدريبية متاحة حالياً.</div>';
        }
        ?>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
