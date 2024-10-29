<?php 
session_start();
include 'service/database.php';

if (isset($_GET['id'])) {
    $course_id = $_GET['id'];
    $query = "SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ii", $_SESSION['student_id'], $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $enrollment = $result->fetch_assoc();
}
        if (!$enrollment) {
        $query = "INSERT INTO enrollments (student_id, course_id, enrolled_at) VALUES (?, ?, NOW())";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ii", $_SESSION['student_id'], $course_id);
        
        if ($stmt->execute()) {
            // echo "Berhasil mendaftar ke kursus!";
            header("Location: dashboardSiswa.php"); // Redirect untuk mencegah refresh form
            exit;
        } else {
            echo "Gagal mendaftar kursus.";
        }
    }
?>