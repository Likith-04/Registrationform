<?php
$url = getenv("DATABASE_URL");
if ($url) {
  $dbparts = parse_url($url);
  $servername = $dbparts['host'];
  $username   = $dbparts['user'];
  $password   = $dbparts['pass'];
  $dbname     = ltrim($dbparts['path'], '/');
  $port       = $dbparts['port'];
} else {
  die("<h3 style='color:red; text-align:center;'>❌ Database URL not found. Please set DATABASE_URL in Railway Variables.</h3>");
}

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
  die("<h3 style='color:red; text-align:center;'>Database Connection Failed: " . $conn->connect_error . "</h3>");
}

if (isset($_GET['delete_id'])) {
  $id = intval($_GET['delete_id']);
  $delete_sql = "DELETE FROM registrations WHERE id = $id";
  $conn->query($delete_sql);
  header("Location: admin.php");
  exit;
}

$sql = "SELECT * FROM registrations";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0; padding: 0;
      background-image: url("https://wallpapers.com/images/hd/4k-laptop-on-gloomy-desk-f7k0g3xufpxxwjk9.jpg");
      background-repeat: no-repeat; background-size: cover; background-position: center; background-attachment: fixed;
      color: white;
    }
    .container { background-color: rgba(0,0,0,0.7); width: 90%; margin: 40px auto; padding: 25px 40px; border-radius: 12px; box-shadow: 0 0 20px rgba(0,0,0,0.8); }
    h2, h3 { text-align: center; color: #f8f8f8; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; background: transparent; }
    th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
    th { background-color: rgba(80,80,80,0.7); }
    tr:hover { background-color: rgba(120,120,120,0.8); }
    .delete-btn { background-color: rgba(100,100,100,0.8); color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; transition: 0.3s; }
    .delete-btn:hover { background-color: red; transform: scale(1.1); }
    .back-btn { display: inline-block; padding: 10px 20px; background: #444; color: white; border-radius: 6px; text-decoration: none; margin-top: 20px; transition: 0.3s; }
    .back-btn:hover { background-color: #777; transform: scale(1.1); }
  </style>
</head>
<body>
  <div class="container">
    <h2><i class="fas fa-user-shield"></i> Admin Dashboard</h2>
    <h3>Registered Users</h3>
    <?php if ($result && $result->num_rows > 0): ?>
      <table>
        <tr>
          <th>ID</th><th>Full Name</th><th>USN</th><th>Gender</th><th>Email</th><th>Phone</th><th>Address</th><th>Action</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['fullname']) ?></td>
            <td><?= htmlspecialchars($row['usn']) ?></td>
            <td><?= htmlspecialchars($row['gender']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['address']) ?></td>
            <td>
              <a href="admin.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this record?');">
                <button class="delete-btn"><i class="fas fa-trash"></i> Delete</button>
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <p style="text-align:center;color:#ff6666;">No registrations found.</p>
    <?php endif; ?>
    <div style="text-align:center;">
      <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Registration</a>
    </div>
  </div>
</body>
</html>
