<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
session_start();
include('../service/database.php');
$student_id = $_SESSION['student_id'];
if (!isset($_SESSION['student_id'])) {
    header("Location: ../index.php"); // Redirect ke halaman login jika belum login
    exit();
}

if (isset($_GET['quiz_id'])) {
$quiz_id = $_GET['quiz_id'];} else if (isset($_POST['submit_quiz'])) {
    $quiz_id = $_POST['quiz_id'];}

    $query = "SELECT * FROM questions WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Jika course tidak ditemukan atau bukan milik guru yang login
    // if ($result->num_rows == 0) {
    //     // Redirect ke halaman lain, karena guru tidak berhak mengakses course ini
    //     header('Location: ../index.php');
    //     exit();
    // }

// Redirect if not a student
if ($_SESSION['role'] !== 'siswa') {
    header("Location: ../login.php");
    exit();
}

$stmt = $db->prepare("SELECT * FROM submissions WHERE student_id = ? AND quiz_id = ?");
$stmt->bind_param("ii", $student_id, $quiz_id);
$stmt->execute();
$submission = $stmt->get_result()->fetch_assoc();


$stmt = $db->prepare("SELECT * FROM quizzes WHERE id = ?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$quiz = $stmt->get_result()->fetch_assoc();


$stmt = $db->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// echo $quiz['is_active'];

if ($submission !== null || $quiz['is_active'] === 0) {
    header("Location: ../halKuis.php");
    exit();  // Stop further processing
}

if ($submission !== null) {
    header("Location: ../halKuis.php");
    exit();  // Stop further processing
}

if (isset($_POST['submit_quiz'])) {
    $quiz_id = $_POST['quiz_id'];
    $answers = $_POST['answers'];

    $correct_count = 0;
    $total_questions = count($answers);

    $stmt = $db->prepare("INSERT INTO submissions (student_id, quiz_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $student_id, $quiz_id);
    $stmt->execute();

    // Process each answer
    foreach ($answers as $question_id => $student_answer) {
        // Fetch the correct answer
        $stmt = $db->prepare("SELECT correct_answer FROM questions WHERE id = ?");
$stmt->bind_param("i", $question_id);
$stmt->execute();
$result = $stmt->get_result();
$correct_answer = $result->fetch_assoc()['correct_answer'];


        // Check if student's answer is correct
        $is_correct = ($student_answer === $correct_answer) ? 1 : 0;

        if ($is_correct) {
            $correct_count++;
        }

        // Save student's answer
        $stmt = $db->prepare("INSERT INTO student_answers (student_id, question_id, answer, is_correct) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $student_id, $question_id, $student_answer, $is_correct);
        $stmt->execute();
    }

    // Calculate the score
    $score = round((($correct_count / $total_questions) * 100),2);

    // Update the leaderboard
    $stmt = $db->prepare("INSERT INTO leaderboard (student_id, quiz_id, score) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE score = ?");
    $stmt->bind_param("iidd", $student_id, $quiz_id, $score, $score);
    $stmt->execute();

    header("Location: ../halKuis.php");

    // Redirect to the leaderboard
    // header("Location: ../dashboardSiswa.php");
    // echo $answers;
    // echo "----------";
    // echo $correct_count;
    // echo "----------";
    // echo $correct_answer;
    // echo "----------";
    // echo $student_answer;
    // echo "----------";
    // echo "Score: " . $score;
    exit();
}



// Fetch questions for the quiz


if (empty($questions)) {
    echo "This quiz has no questions.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Quiz - SmartQ</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    body {
    font-family: 'Arial', sans-serif;
    background: linear-gradient(to right, #6a11cb, #2575fc);
    color: #fff;
    margin: 0;
    padding: 0;
}

.quiz-section {
    max-width: 800px;
    margin: 50px auto;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    animation: fadeIn 1s ease;
}

h2 {
    text-align: center;
    font-size: 2em;
    word-break:break-all;
    max-width:600px;
    margin:40px auto;
}

h3 {
    word-break:break-all;
    max-width:500px;
}

input {
    margin-bottom:10px;
}

.question-block {
    margin-bottom: 20px;
    padding: 10px 20px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    transition: transform 0.2s;
}

.question-block:hover {
    transform: scale(1.02);
}

.option-label {
    margin-left: 10px;
    cursor: pointer;
}

button {
    display: block;
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 5px;
    background: #f44336;
    color: white;
    font-size: 1.2em;
    cursor: pointer;
    transition: background 0.3s;
}

button:hover {
    background: #c62828;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

</style>
<body>


 <section class="quiz-section">
        <h2><?php echo htmlspecialchars($quiz['title']); ?></h2>

        <form method="POST" action="soalKuis.php">
            <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">

            <?php foreach ($questions as $index => $question): ?>
                <div class="question-block">
                    <h3>Question <?php echo $index + 1; ?> : <?php echo htmlspecialchars($question['question_text']); ?> </h3>
                    <p></p>

                    <input type="hidden" name="questions[<?php echo $question['id']; ?>]" value="">

                    <label>
                        <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="A" required>
                        <span class="option-label"><?php echo htmlspecialchars($question['a']); ?></span>
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="B" required>
                        <span class="option-label"><?php echo htmlspecialchars($question['b']); ?></span>
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="C" required>
                        <span class="option-label"><?php echo htmlspecialchars($question['c']); ?></span>
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="D" required>
                        <span class="option-label"><?php echo htmlspecialchars($question['d']); ?></span>
                    </label>
                </div>
            <?php endforeach; ?>

            <button type="submit" name="submit_quiz">Submit Quiz</button>
        </form>
    </section>

</body>

<script>
    // Optional JavaScript for any interactions
document.querySelectorAll('.question-block').forEach(block => {
    block.addEventListener('mouseenter', () => {
        block.style.backgroundColor = 'rgba(255, 255, 255, 0.3)';
    });

    block.addEventListener('mouseleave', () => {
        block.style.backgroundColor = 'rgba(255, 255, 255, 0.2)';
    });
});

</script>
</html>
