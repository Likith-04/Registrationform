<?php
$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "regform_db";     

$conn = new mysqli($servername, $username, $password, $dbname);

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
  <title>Admin Dashboard - Registered Users</title>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background-image: url("https://wallpapers.com/images/hd/4k-laptop-on-gloomy-desk-f7k0g3xufpxxwjk9.jpg");
      background-repeat: no-repeat;
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      color: white;
    }

    .container {
      background-color: rgba(0, 0, 0, 0.7);
      width: 90%;
      margin: 40px auto;
      padding: 25px 40px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.8);
      backdrop-filter: blur(6px);
    }

    h2 {
      text-align: center;
      margin-bottom: 10px;
      color: #f8f8f8;
      font-size: 26px;
    }

    h3 {
      color: #ccc;
      margin-left: 20px;
      margin-bottom: 10px;
    }

    table {
      width: 100%;
      margin-top: 15px;
      border-collapse: collapse;
      background-color: transparent;
      overflow: hidden;
    }

    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: center;
      color: white;
    }

    th {
      color: white;
      font-weight: bold;
      font-size: 16px;
      background-color: rgba(80, 80, 80, 0.7);
    }

    tr:hover {
      background-color: rgba(120, 120, 120, 0.8);
    }

    .no-data {
      text-align: center;
      color: #ff6666;
      font-size: 18px;
      margin-top: 20px;
    }

    .back-btn {
      display: inline-block;
      padding: 10px 20px;
      margin: 25px auto 0;
      background-color: rgb(60, 60, 60);
      color: white;
      text-decoration: none;
      border-radius: 6px;
      text-align: center;
      font-weight: bold;
      transition: 0.3s;
    }

    .back-btn:hover {
      background-color: rgb(120, 120, 120);
      transform: scale(1.10);
    }

    .delete-btn {
      background-color: rgba(100, 100, 100, 0.8);
      color: white;
      border: none;
      padding: 8px 15px;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.3s;
    }

    .delete-btn:hover {
      background-color: red;
      transform: scale(1.1);
    }

    .table-wrapper {
      overflow-x: auto;
    }

  </style>
</head>
<body>

  <div class="container">
    <h2><i class="fas fa-user-shield"></i> Admin Dashboard</h2>
    <h3>Registered Users:</h3>

    <div class="table-wrapper">
    <?php
    if ($result && $result->num_rows > 0) {
      echo "<table>
              <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>USN</th>
                <th>Gender</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Action</th>
              </tr>";

      while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>".$row['id']."</td>
                <td>".$row['fullname']."</td>
                <td>".$row['usn']."</td>
                <td>".$row['gender']."</td>
                <td>".$row['email']."</td>
                <td>".$row['phone']."</td>
                <td>".$row['address']."</td>
                <td>
                  <a href='admin.php?delete_id=".$row['id']."' onclick=\"return confirm('Are you sure you want to delete this record?');\">
                    <button class='delete-btn'><i class='fas fa-trash'></i> Delete</button>
                  </a>
                </td>
              </tr>";
      }

      echo "</table>";
    } else {
      echo "<p class='no-data'>No registrations found in the database.</p>";
    }

    $conn->close();
    ?>
    </div>

    <div style='text-align:center;'>
      <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Registration</a>
    </div>
  </div>

</body>
</html>
