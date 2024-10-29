<?php
// session_start();
// include('service/database.php');

// Check if the user is a teacher
if ($_SESSION['role'] != 'guru') {
    echo "Access denied!";
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

// Query to get all courses taught by the teacher
$courses_query = "
    SELECT id, title, created_at 
    FROM courses 
    WHERE teacher_id = ?
";
$stmt = $db->prepare($courses_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$courses_result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - Course Progress</title>
     <style>
     h3 {
         word-break: break-all;
         max-width:600px;
         letter-spacing:1px;
         text-align:center;
         margin: 20px auto 10px auto; 
     }
        /*table {*/
        /*    width: 100%;*/
        /*    border-collapse: collapse;*/
        /*}*/
        /*table, th, td {*/
        /*    border: 1px solid black;*/
        /*}*/
        /*th, td {*/
        /*    padding: 10px;*/
        /*    text-align: left;*/
        /*}*/
    </style>
</head>
<body>
    <h1>Teacher Dashboard - Course Progress</h1>

    <?php if ($courses_result->num_rows > 0): ?>
        <?php while ($course = $courses_result->fetch_assoc()): ?>
            
            <?php
            // Get total students enrolled, and completion time for each course
            $stats_query = "
                SELECT 
                    login.nama AS student_name,
                    enrollments.enrolled_at,
                    enrollments.finish_at,
                    TIMESTAMPDIFF(MINUTE, enrollments.enrolled_at, enrollments.finish_at) AS completion_time
                FROM enrollments
                JOIN login ON enrollments.student_id = login.id
                WHERE enrollments.course_id = ?
                ";
                $stmt = $db->prepare($stats_query);
                $stmt->bind_param("i", $course['id']);
                $stmt->execute();
                $stats_result = $stmt->get_result();
                ?>

<?php if ($stats_result->num_rows > 0): ?>
    <h3>Course: <?php echo $course['title']; ?></h3>
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Enrolled At</th>
                            <th>Completed At</th>
                            <th>Completion Time (Minutes)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $stats_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['student_name']; ?></td>
                                <td><?php echo $row['enrolled_at']; ?></td>
                                <td><?php echo $row['finish_at'] ? $row['finish_at'] : 'In Progress'; ?></td>
                                <td><?php echo $row['completion_time'] ? $row['completion_time'] . ' minutes' : 'N/A'; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <!-- <p style="text-align: center;">No students enrolled in this course yet.</p> -->
            <?php endif; ?>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align: center;";>You don't have any courses yet.</p>
    <?php endif; ?>
    <p style="height: 10px";></p>

</body>
</html>



<?php
// session_start();
// include('service/database.php');

// // Check if the user is a teacher
// if ($_SESSION['role'] != 'guru') {
//     echo "Access denied!";
//     exit();
// }

// $teacher_id = $_SESSION['teacher_id'];

// Query to get all quizzes created by the teacher
$quizzes_query = "
    SELECT id, title, created_at 
    FROM quizzes 
    WHERE teacher_id = ?
";
$stmt = $db->prepare($quizzes_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$quizzes_result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - Quiz Progress</title>
    <!-- <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style> -->
</head>
<body>
    <h1>Teacher Dashboard - Quiz Progress</h1>

    <?php if ($quizzes_result->num_rows > 0): ?>
        <?php while ($quiz = $quizzes_result->fetch_assoc()): ?>
            
            <?php
            // Get quiz scores and completion time for each quiz
            $stats_query = "
            SELECT 
            login.nama AS student_name,
            leaderboard.score,
            submissions.submission_date,
            TIMESTAMPDIFF(MINUTE, quizzes.created_at, submissions.submission_date) AS quiz_completion_time
            FROM leaderboard
            JOIN login ON leaderboard.student_id = login.id
            JOIN quizzes ON leaderboard.quiz_id = quizzes.id
            LEFT JOIN submissions ON submissions.student_id = login.id AND submissions.quiz_id = quizzes.id
            WHERE quizzes.id = ?
            ";
            $stmt = $db->prepare($stats_query);
            $stmt->bind_param("i", $quiz['id']);
            $stmt->execute();
            $stats_result = $stmt->get_result();
            ?>

<?php if ($stats_result->num_rows > 0): ?>
    <h3>Quiz : <?php echo $quiz['title']; ?></h3>
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Quiz Score</th>
                            <th>Submission Date</th>
                            <th>Completion Time (Minutes)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $stats_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['student_name']; ?></td>
                                <td><?php echo $row['score'] . '%'; ?></td>
                                <td><?php echo $row['submission_date'] ? date("D, d M Y / H:i", strtotime($row['submission_date'])) : 'Quiz not attempted'; ?></td>
                                <td><?php echo $row['quiz_completion_time'] ? $row['quiz_completion_time'] . ' mins' : 'N/A'; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <?php endif; ?>
                <!-- <p style="text-align: center;">No quiz results available for this quiz.</p> -->
        <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center;";>You don't have any quizzes yet.</p>
    <?php endif; ?>
    <p style="height: 10px";></p>

</body>
</html>

<?php
$stmt->close();
$db->close();
?>

