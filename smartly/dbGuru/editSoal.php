<?php
session_start();
include('../service/database.php');
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../login.php");
    exit();
}

$quiz_id = $_GET['quiz_id'];

// Query untuk data quiz
$stmt = $db->prepare("SELECT * FROM quizzes WHERE id = ?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();
$quizdata = $result->fetch_assoc();

// Ambil 'teacher_id' dari session
$logged_in_teacher_id = $_SESSION['teacher_id'];

// Validasi akses guru
$query = "SELECT * FROM quizzes WHERE id = ? AND teacher_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('ii', $quiz_id, $logged_in_teacher_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: ../index.php');
    exit();
}

// Redirect ke dashboard
if (isset($_POST['kembali'])) {
    header("Location: ../dashboardGuru.php");
    exit();
}

// Menambah soal
if (isset($_POST['add_question'])) {
    $question_text = $_POST['question_text'];
    $correct_answer = $_POST['correct_answer'];
    $a = $_POST['a'];
    $b = $_POST['b'];
    $c = $_POST['c'];
    $d = $_POST['d'];

    $stmt = $db->prepare("INSERT INTO questions (quiz_id, question_text, correct_answer, a, b, c, d) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $quiz_id, $question_text, $correct_answer, $a, $b, $c, $d);
    $stmt->execute();

    header("Location: editSoal.php?quiz_id=" . $quiz_id);
    exit();
}

// Menghapus soal
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $db->prepare("DELETE FROM questions WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();

    header("Location: editSoal.php?quiz_id=" . $quiz_id);
    exit();
}

// Mengupdate soal
if (isset($_POST['update_question'])) {
    $question_id = $_POST['question_id'];
    $question_text = $_POST['question_text'];
    $correct_answer = $_POST['correct_answer'];
    $a = $_POST['a'];
    $b = $_POST['b'];
    $c = $_POST['c'];
    $d = $_POST['d'];

    $stmt = $db->prepare("UPDATE questions SET question_text = ?, correct_answer = ?, a = ?, b = ?, c = ?, d = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $question_text, $correct_answer, $a, $b, $c, $d, $question_id);
    $stmt->execute();

    header("Location: editSoal.php?quiz_id=" . $quiz_id);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 20px;
            margin-bottom: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h1, h2 {
            color: #007bff;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .sidebar-questions {
            background: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .question-item {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #007bff;
            border-radius: 8px;
            background: #f1f1f1;
        }
        .actions {
            margin-top: 10px;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
        }
        .btn-custom:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;

        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        
    </style>
</head>
<body>

    <div class="container">
        <h1><?php echo $quizdata['title']?></h1>

        <!-- Form to Add a New Question -->
        <form method="POST" class="mb-4">
            <div class="form-group">
                <label for="question_text">Enter your question here:</label>
                <textarea name="question_text" id="question_text" class="form-control" placeholder="Enter your question here" required></textarea>
            </div>
            <div class="form-group">
                <label for="correct_answer">Select Correct Answer:</label>
                <select name="correct_answer" id="correct_answer" class="form-select" required>
                    <option value="" disabled selected>Select Correct Answer</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>
            <div class="form-group">
                <label for="a">Option A:</label>
                <input type="text" name="a" id="a" class="form-control" placeholder="Option A" required>
            </div>
            <div class="form-group">
                <label for="b">Option B:</label>
                <input type="text" name="b" id="b" class="form-control" placeholder="Option B" required>
            </div>
            <div class="form-group">
                <label for="c">Option C:</label>
                <input type="text" name="c" id="c" class="form-control" placeholder="Option C" required>
            </div>
            <div class="form-group">
                <label for="d">Option D:</label>
                <input type="text" name="d" id="d" class="form-control" placeholder="Option D" required>
            </div>
            <button type="submit" name="add_question" class="btn btn-custom">Add Question</button>
        </form>

        <h2>Review Questions</h2>

        <!-- Sidebar for Question Navigation -->
        <div class="sidebar-questions mb-4">
    <ul class="list-group">
        <?php
        $stmt = mysqli_query($db,"SELECT * FROM questions WHERE quiz_id = $quiz_id");
        $questions = mysqli_fetch_all($stmt, MYSQLI_ASSOC);
        foreach ($questions as $question) {
            echo "<li class='list-group-item'>";
            echo "<a href='#question-{$question['id']}' class='text-decoration-none text-dark'>{$question['question_text']}</a>";
            echo "</li>";
        }
        ?>
    </ul>
</div>

        <!-- Question Items Display -->
        <?php
        foreach ($questions as $question) {
            echo "<div class='question-item' id='question-{$question['id']}'>";
            echo "<form method='POST' action=''>";
            echo "<input type='hidden' name='question_id' value='{$question['id']}'>";
            echo "<div class='form-group'>";
            echo "<label for='question_text'>Question:</label>";
            echo "<textarea name='question_text' class='form-control'>{$question['question_text']}</textarea>";
            echo "</div>";
            echo "<div class='form-group'>";
            echo "<label for='a'>Option A:</label>";
            echo "<input type='text' name='a' class='form-control' value='{$question['a']}' required>";
            echo "</div>";
            echo "<div class='form-group'>";
            echo "<label for='b'>Option B:</label>";
            echo "<input type='text' name='b' class='form-control' value='{$question['b']}' required>";
            echo "</div>";
            echo "<div class='form-group'>";
            echo "<label for='c'>Option C:</label>";
            echo "<input type='text' name='c' class='form-control' value='{$question['c']}' required>";
            echo "</div>";
            echo "<div class='form-group'>";
            echo "<label for='d'>Option D:</label>";
            echo "<input type='text' name='d' class='form-control' value='{$question['d']}' required>";
            echo "</div>";
            echo "<div class='form-group'>";
            echo "<label for='correct_answer'>Correct Answer:</label>";
            echo "<select name='correct_answer' class='form-select'>";
            echo "<option value='A'" . ($question['correct_answer'] == 'A' ? "selected" : "") . ">A</option>";
            echo "<option value='B'" . ($question['correct_answer'] == 'B' ? "selected" : "") . ">B</option>";
            echo "<option value='C'" . ($question['correct_answer'] == 'C' ? "selected" : "") . ">C</option>";
            echo "<option value='D'" . ($question['correct_answer'] == 'D' ? "selected" : "") . ">D</option>";
            echo "</select>";
            echo "</div>";
            echo "<div class='actions'>";
            echo "<button type='submit' name='update_question' class='btn btn-custom'>Save</button>";
            echo "<a href='editSoal.php?quiz_id=$quiz_id&delete_id={$question['id']}' class=' btn btn-danger' onclick=\"return confirm('Delete this question?')\">Delete</a>";
            echo "</div>";
            echo "</form>";
            echo "</div>";
        }
        ?>

            <a href="../dashboardGuru.php" class="btn btn-custom">Back</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
