<?php 
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
include('../service/database.php');
  session_start();
  
  if (!isset($_SESSION['teacher_id'])) {
    header("Location: index.php"); // Redirect ke halaman login jika belum login
    exit();
}

  // Ambil course_id dari URL
  if (isset($_GET['id'])) {
    $course_id = $_GET['id'];

    // Query untuk mengambil data course berdasarkan ID
    $stmt = $db->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();
  }
  

// Ambil 'teacher_id' dari session (ID guru yang sedang login)
$logged_in_teacher_id = $_SESSION['teacher_id'];

// Query untuk mendapatkan course berdasarkan 'id' dan 'teacher_id'
$query = "SELECT * FROM courses WHERE id = ? AND teacher_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('ii', $course_id, $logged_in_teacher_id);
$stmt->execute();
$result = $stmt->get_result();

// Jika course tidak ditemukan atau bukan milik guru yang login
if ($result->num_rows == 0) {
    // Redirect ke halaman lain, karena guru tidak berhak mengakses course ini
    header('Location: ../index.php');
    exit();
}

  // Jika form di-submit
  if (isset($_POST['simpan'])) {
    $tittle = $_POST['tittle'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $level = $_POST['level'];
    $jadwal = $_POST['jadwal'];
    $teacher_id = $_SESSION['teacher_id'];
    $content = $_POST['content'];
    $file = $_FILES['file_material'];
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_destination = '../uploads/' . $file_name;
    move_uploaded_file($file_tmp, $file_destination) ?: $file_name = $course['file_url'];


    // $file_url = $_POST['file_url'];
    $video_url = $_POST['video_link'];
    if (strpos($video_url, 'watch?v=') !== false) {
      $video_url = str_replace('watch?v=', 'embed/', $video_url);
    }
    $tags = $_POST['tags'];
    // echo $file_name;
    // Siapkan statement SQL untuk update
    $stmt = $db->prepare("UPDATE courses SET title = ?, description = ?, category = ?, level = ?, jadwal = ?, teacher_id = ?, content = ?, file_url = ?, video_url = ?, tags = ? WHERE id = ?");

    // Bind parameter
    $stmt->bind_param("sssssissssi", $tittle, $description, $category, $level, $jadwal, $teacher_id, $content, $file_name, $video_url, $tags, $course_id);

    // Eksekusi statement
    if ($stmt->execute()) {
      // $_SESSION['rowAffectedEdit']=$stmt->affected_rows
      header("Location: ../dashboardGuru.php");
    }
  }
  
  if (isset($_POST["hapus"])) {
    $stmt = $db->prepare("DELETE FROM courses where id= ?");
    $stmt->bind_param("i", $course['id']);
    $stmt->execute();
    header("Location: ../dashboardGuru.php");
  }


?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta name="robots" content="noindex, nofollow">

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Course Modal</title>
  <style>
    /* CSS styles */
    h2 {
      text-align: center;
    }

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
      box-sizing: border-box;
    }

    textarea {
      resize: vertical;
      height: 100px;
    }

    select {
      margin: auto;
    }

    #samping {
      display: flex;
      flex-direction: row;
      gap: 10px;
    }

    button {
      /* margin: auto; */
      /* width: 50vw; */
      
      padding: 12px;
      /* background-color: #28a745; */
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .simpan {
      background-color: #28a745;
    }

    .hapus {
      background-color: #dc3545;
    }

    .simpan:hover {
      background-color: #218838;
    }

    .hapus:hover {
      background-color: #bd2130;
    }
  </style>
</head>

<body>
  
  
  <!-- HTML Form -->
  <h2>Edit Course</h2>
  <form action="" method="POST" enctype="multipart/form-data">
    <div class="bawah">
      <label for="courseName">Title</label>
      <input type="text" id="courseName" name="tittle" value="<?php echo $course['title']; ?>" required>

      <label for="description">Deskripsi</label>
      <textarea id="description" name="description" required><?php echo $course['description']; ?></textarea>

      <label for="category">Kategori</label>
      <input type="text" id="category" name="category" value="<?php echo $course['category']; ?>" required>

      <div id="samping">
        <select name="level" id="level" required>
          <option value="" disabled>Pilih level</option>
          <option value="Beginner" <?php if ($course['level'] == 'Beginner') echo 'selected'; ?>>Beginner</option>
          <option value="Intermediate" <?php if ($course['level'] == 'Intermediate') echo 'selected'; ?>>Intermediate</option>
          <option value="Advanced" <?php if ($course['level'] == 'Advanced') echo 'selected'; ?>>Advanced</option>
        </select>
        <input type="datetime-local" id="jadwal" name="jadwal" value="<?php echo $course['jadwal']; ?>" required>
      </div>

      <label for="content">Content:</label>
      <textarea id="content" name="content" rows="8"><?php echo $course['content']; ?></textarea>

      <label for="tags">Tags:</label>
      <input type="text" id="tags" name="tags" value="<?php echo $course['tags']; ?>">

      <label for="file_material">Upload Material (PDF/DOC):</label>
      <input type="file" id="file_material" name="file_material" accept=".pdf,.doc,.docx">

      <label for="video_link">Video Link (URL):</label>
      <input type="text" id="video_link" name="video_link" placeholder="https://" value="<?php echo $course['video_url']; ?>">

      <div class="samping">
      <button class="simpan" type="submit" name="simpan">Simpan</button>
      <button class="hapus" type="submit" name="hapus">Delete</button>
      </div>
    </div>
  </form>

</body>

</html>