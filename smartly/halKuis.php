<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: /smartly/login.php"); // Redirect ke halaman login jika belum login
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quiz Selection & Leaderboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: rgb(35,100,217);
    background: linear-gradient(149deg, rgba(35,100,217,1) 0%, rgba(44,164,253,1) 25%, rgba(49,168,253,1) 41%, rgba(213,87,255,1) 87%);
      padding: 0;
      color: #fff;
    }
    .container {
        width: 80%;
        margin: 20px auto;
        padding: 20px;
        background-color: rgba(255, 255, 255, 0.5);
        border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }
    h1 {
      text-align: center;
      color: #0073ff;
      animation: slideUp 0.6s ease-out forwards;
    }
    
    h3 {
         word-wrap: break-word; /* Memecah kata panjang */
  word-break: break-word; /* Memecah kata agar tetap di dalam card */
            max-width: 700px;
    }
    select {
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #0073ff;
      background-color: #fff;
      color: #0073ff;
      font-size: 16px;
      /* margin-bottom: 20px; */
      width: 100%;
      animation: slideUp 0.6s ease-out forwards;
    }
    .quiz-list, .leaderboard {
        margin-top: 30px;
        animation: slideUp 0.6s ease-out forwards;
    }
    .quiz-item {
        animation: slideUp 0.6s ease-out forwards;
        margin-bottom: 20px;
        padding: 15px;
        border-bottom: 1px solid #ddd;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(to right, #f7f7f7, #fbfbff);
        border-radius: 5px;
        transition: background 0.3s ease;
    }
    /* .quiz-item:hover {
        background: rgba(0, 115, 255, 0.8);
        } */
        .quiz-item h3 {
            margin: 0;
            font-size: 18px;
            color: #0073ff;
        }
        .quiz-item p {
            margin: 5px 0;
            color: #555;
        }
    .quiz-item a {
      padding: 10px 15px;
      background-color: #007bff;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      transition: background 0.3s ease;
    }
    .quiz-item a:hover {
        background-color: #0056b3;
    }
        
        
    .leaderboard table {
        animation: slideUp 0.6s ease-out forwards;
      width: 100%;
      border-collapse: separate;
      margin-top: 20px;
      color: #333;
      margin-bottom: 30px;
      /*border-radius:10px;*/
    }
    .leaderboard th, .leaderboard td {
      padding: 10px;
      text-align: center;
      border-bottom: 1px solid #ddd;
       word-wrap: break-word; /* Memecah kata panjang */
  word-break: break-word; /* Memecah kata agar tetap di dalam card */
            max-width: 500px;
    }
    
    .leaderboard th {
      background-color: #0073ff;
      color: white;
    }
    .leaderboard tr:nth-child(even) {
      background-color: #f2f2f2;
    }

    .back-button {
      background-color: #007bff;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      /* margin: 20px; */
    }

    @keyframes slideUp {
    0% {
        transform: translateY(+100%);
        opacity: 0;
    }
    100% {
        transform: translateY(0);
        opacity: 1;
    }
    }
    /* .leaderboard tr:hover {
      background-color: #e0e0e0;
    } */
    /* Loader styling (commented out) */
    /* #loader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: white;
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }
    .spinner {
      width: 50px;
      height: 50px;
      border: 5px solid #f3f3f3;
      border-top: 5px solid #007bff;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    } */
  </style>
</head>
      <?php
        // session_start();
        include 'service/database.php';

        // Database connection (adjust your connection details)
        // Query to get quizzes
        $sql = "SELECT * FROM quizzes";
        $result = $db->query($sql);
    ?>
<body>
  <div class="container">
      
        <h1>Leaderboard</h1>
    <div class="leaderboard">
      <table>
        <thead>
          <tr>
            <th>Rank</th>
            <th>Student Name</th>
            <th>Quiz Title</th>
            <th>Score</th>
          </tr>
        </thead>
        <tbody>
          <?php
            // Query to get leaderboard
            $sql_leaderboard = "SELECT login.nama as student_name, quizzes.title as quiz_title, leaderboard.score 
                               FROM leaderboard
                               JOIN login ON leaderboard.student_id = login.id
                               JOIN quizzes ON leaderboard.quiz_id = quizzes.id
                               ORDER BY leaderboard.score DESC LIMIT 10";
            $result_leaderboard = $db->query($sql_leaderboard);

            if ($result_leaderboard->num_rows > 0) {
              $rank = 1;
              while($row_leaderboard = $result_leaderboard->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $rank++ . "</td>";
                echo "<td>" . $row_leaderboard['student_name'] . "</td>";
                echo "<td>" . $row_leaderboard['quiz_title'] . "</td>";
                echo "<td>" . $row_leaderboard['score'] . "</td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='4'>No leaderboard data available.</td></tr>";
            }
          ?>
        </tbody>
      </table>
    </div>
    <h1>Available Quizzes</h1>
    
    <select id="filterQuiz" onchange="filterQuizzes()">
        <option disabled selected>Filter</option>
      <option value="all">All Quizzes</option>
      <option value="active">Active Quizzes</option>
      <option value="inactive">Inactive Quizzes</option>
    </select>

    <div class="quiz-list" id="quizList">

       <?php if ($result->num_rows > 0) {
          // Output data for each quiz
          while($row = $result->fetch_assoc()) {
            $status = $row['is_active'] ? "Active" : "Inactive";
            echo "<div class='quiz-item' data-status='" . strtolower($status) . "'>";
            echo "<div>";
            echo "<h3>" . $row['title'] . "</h3>";
            echo "<p>Status : " . $status . "</p>";
            echo "<p>Created at : " . date("D, d M Y / H:i",strtotime($row['created_at'])) . "</p>";
            echo "</div>";
            echo "<div>";
            echo "<a href='dbSiswa/soalKuis.php?quiz_id=" . $row['id'] . "'>Start Quiz</a>";
            echo "</div>";
            echo "</div>";
          }
        } else {
          echo "No quizzes available.";
        }
      ?>
    </div>


    <a href="dashboardSiswa.php" class="back-button">Back</a>
</div>


  <script>
    // Loader for page load (commented out)
    // window.addEventListener('load', function() {
    //   document.getElementById('loader').style.display = 'none';
    // });

    // Filter quizzes based on status (Active/Inactive)
    function filterQuizzes() {
      var filter = document.getElementById("filterQuiz").value;
      var quizzes = document.getElementsByClassName("quiz-item");

      for (var i = 0; i < quizzes.length; i++) {
        if (filter == "all") {
          quizzes[i].style.display = "flex";
        } else if (quizzes[i].getAttribute("data-status") != filter) {
          quizzes[i].style.display = "none";
        } else {
          quizzes[i].style.display = "flex";
        }
      }
    }
  </script>
</body>
</html>
