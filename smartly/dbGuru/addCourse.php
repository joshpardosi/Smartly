<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Jakarta');

// session_start();
include('service/database.php');
if (isset($_POST['simpan'])) {
  $tittle = $_POST['tittle'];
  $description = $_POST['description'];
  $category = $_POST['category'];
  $level = $_POST['level'];
  $jadwal = $_POST['jadwal'];
  $teacher_id = $_SESSION['teacher_id'];

  // Siapkan statement SQL dengan placeholder (?)
  $stmt = $db->prepare("INSERT INTO courses ( title, description, category, level, jadwal, teacher_id) VALUES ( ?, ?, ?, ?, ?, ?)");

  // Bind parameter (s = string, i = integer)
  $stmt->bind_param("sssssi", $tittle, $description, $category, $level, $jadwal, $teacher_id);

  // Eksekusi statement
  $stmt->execute();
   header("Location: dashboardGuru.php");
  // Cek apakah query berhasil
  $stmt->close();

  // header("Location: dashboardGuru.php");
  // exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Course Modal</title>
  <style>
    /* Gaya modal */
    .modal {
      display: none;
      /* Modal tersembunyi secara default */
      position: fixed;
      z-index: 1;
      /* Memastikan modal berada di atas elemen lain */
      left: 0;
      top: 0;
      overflow: auto;
      width: 100%;
      /* Lebar layar penuh */
      height: 100%;
      /* Tinggi layar penuh */
      background-color: rgba(0, 0, 0, 0.5);
      /* Background semi-transparent */
    }

    /* Gaya konten modal */
    .modal-content {
      border-radius: 15px;
      background-color: #fff;
      margin: 50px auto;
      padding: 20px;
      border: 1px solid #888;
      width: 50%;
      /* Lebar modal diubah menjadi lebih kecil */
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Tombol closed */
    .closed, .closedQuiz {
      color: #fff;
      float: right;
      font-size: 24px;
      font-weight: bold;
      /* background-color: red; */
      border: none;
      /* border-radius: 50%; */
      cursor: pointer;
      padding: 5px 10px;
      /* transition: background-color 0.3s ease; */
    }

    .closed:hover , .closedQuiz:hover {
      background-color: darkred;
    }

    /* Form styling */
    .bawah {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    input,
    textarea,
    select {
      padding: 10px;
      width: 100%;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 16px;
      font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
      box-sizing: border-box;
    }

    textarea {
      resize: vertical;
      height: 100px;
    }

    select {
      margin: auto;
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      /* background-color: #fff; */

    }

    #samping {
      display: flex;
      flex-direction: row;
      gap: 10px;
    }

    button {
      padding: 12px;
      background-color: #0073ff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s ease;
    }

    button:hover {
      /* transform: scale(1.05); */
      background-color: #0083ff;
    }

    label,
    h2 {
      color: black;
    }

   

    /* Style untuk membuat panah pada select lebih konsisten */
  </style>
</head>

<body>

  <button id="addCourse">Add Course</button>

  <!-- Modal -->
  <div id="addCourseModal" class="modal">
    <!-- Konten modal -->
    <div class="modal-content">
      <button class="closed">&times;</button>
      <h2>Add Course</h2>
      <form action="dashboardGuru.php" method="POST">
        <div class="bawah">
          <label for="courseName">Title</label>
          <input type="text" id="courseName" name="tittle" required>

          <label for="description">Deskripsi</label>
          <textarea id="description" name="description" required></textarea>

          <label for="category">Kategori</label>
          <input type="text" id="category" name="category" required>

          <div id="samping">
            <!-- <label for="level">Level</label> -->
            <select name="level" id="level" required>
              <option value="" disabled selected>Pilih level</option>
              <option value="Beginner">Beginner</option>
              <option value="Intermediate">Intermediate</option>
              <option value="Advanced">Advanced</option>
            </select>

            <!-- <label for="jadwal">Jadwal</label> -->
            <input type="datetime-local" id="jadwal" name="jadwal" required>
          </div>

          <button type="submit" name="simpan">Create</button>
        </div>
      </form>
    </div>
  </div>

  <script>

  </script>

</body>

</html>