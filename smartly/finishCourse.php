<?php
session_start();
include('service/database.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php"); // Redirect ke halaman login jika belum login
    exit();
}

$course_id = $_GET['id'];
$student_id = $_SESSION['student_id'];

$stmt = $db->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
$stmt->bind_param("ii", $student_id, $course_id);
$stmt->execute();
$result = $stmt->get_result();
$enrollment = $result->fetch_assoc();
if ($enrollment) {
    $stmt = $db->prepare("UPDATE enrollments SET finish_at = NOW() WHERE student_id = ? AND course_id = ?");
    $stmt->bind_param("ii", $student_id, $course_id);
    $stmt->execute();
    header("Location: dashboardSiswa.php");
    exit();
}

?>