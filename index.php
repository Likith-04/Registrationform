<?php
session_start();

// ✅ FreeDB Connection
$servername = "sql.freedb.tech";
$username   = "YOUR_FREEDB_USERNAME";   // ← replace with your FreeDB username
$password   = "YOUR_FREEDB_PASSWORD";   // ← replace with your FreeDB password
$dbname     = "freedb_registrations";   // ← your FreeDB database name
$port       = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
  die("<h3 style='color:red; text-align:center;'>Database Connection Failed: " . $conn->connect_error . "</h3>");
}

$message = "";
$errors = [];
$submittedData = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = htmlspecialchars(trim($_POST['fullname']));
  $usn = htmlspecialchars(trim($_POST['USN']));
  $gender = htmlspecialchars(trim($_POST['gender']));
  $email = htmlspecialchars(trim($_POST['email']));
  $phone = htmlspecialchars(trim($_POST['phone']));
  $address = htmlspecialchars(trim($_POST['address']));

  // Validation
  if (!preg_match("/^[a-zA-Z0-9._%+-]+@gmail\.com$/", $email)) {
    $errors[] = "Please enter a valid Gmail address.";
  }
  if (!preg_match("/^[0-9]{10}$/", $phone)) {
    $errors[] = "Phone number must contain exactly 10 digits.";
  }
  if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d).+$/", $usn)) {
    $errors[] = "USN must contain at least one letter and one number.";
  }

  if (empty($errors)) {
    $stmt = $conn->prepare("INSERT INTO registrations (fullname, usn, gender, email, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $usn, $gender, $email, $phone, $address);

    if ($stmt->execute()) {
      $_SESSION['message'] = "✅ Registration Successful!";
      $_SESSION['submittedData'] = [
        'fullname' => $name,
        'usn' => $usn,
        'gender' => $gender,
        'email' => $email,
        'phone' => $phone,
        'address' => $address
      ];
      header("Location: index.php");
      exit;
    } else {
      $message = "<p style='color:red; text-align:center;'>❌ Error storing data: " . $stmt->error . "</p>";
    }
    $stmt->close();
  } else {
    $message = "<div style='color:red; text-align:center;'><b>Please correct the following errors:</b><ul>";
    foreach ($errors as $error) {
      $message .= "<li>$error</li>";
    }
    $message .= "</ul></div>";
  }
}
$conn->close();

if (isset($_SESSION['message'])) {
  $message = "<p style='color:lime; text-align:center;'>" . $_SESSION['message'] . "</p>";
  $submittedData = $_SESSION['submittedData'];
  unset($_SESSION['message']);
  unset($_SESSION['submittedData']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Form</title>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background-image: url("https://wallpapers.com/images/hd/4k-laptop-on-gloomy-desk-f7k0g3xufpxxwjk9.jpg");
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
    }
    .container {
      background-color: rgba(0, 0, 0, 0.6);
      width: 450px;
      margin: 60px auto;
      padding: 30px 40px;
      border-radius: 12px;
      color: white;
    }
    h2 { text-align: center; color: #fff; margin-bottom: 25px; }
    .form-group { position: relative; margin-top: 20px; }
    .form-group i {
      position: absolute; top: 50%; left: 10px;
      transform: translateY(-50%);
      color: #555; font-size: 18px;
    }
    input, select, textarea {
      width: 100%; padding: 10px 10px 10px 40px;
      border: none; border-radius: 6px; font-size: 15px;
    }
    button {
      width: 100%; background-color: #4CAF50;
      color: white; padding: 12px; border: none;
      border-radius: 6px; margin-top: 25px;
      cursor: pointer; font-size: 16px;
    }
    button:hover { background-color: #45a049; }
    .details {
      margin-top: 25px; background-color: rgba(255,255,255,0.9);
      color: black; padding: 15px; border-radius: 6px;
    }
  </style>
</head>
<body>

  <div class="container">
    <h2><i class="fas fa-user-edit"></i> Registration Form</h2>

    <form method="POST" action="">
      <div class="form-group"><i class="fas fa-user"></i><input type="text" name="fullname" placeholder="Full Name" required></div>
      <div class="form-group"><i class="fas fa-id-card"></i><input type="text" name="USN" placeholder="USN" required></div>
      <div class="form-group"><i class="fas fa-venus-mars"></i>
        <select name="gender" required>
          <option value="">Select Gender</option><option>Male</option><option>Female</option>
        </select>
      </div>
      <div class="form-group"><i class="fas fa-envelope"></i><input type="email" name="email" placeholder="Email ID" required></div>
      <div class="form-group"><i class="fas fa-phone"></i><input type="text" name="phone" placeholder="Phone Number" maxlength="10" required></div>
      <div class="form-group"><i class="fas fa-map-marker-alt"></i><textarea name="address" rows="3" placeholder="Address" required></textarea></div>
      <button type="submit"><i class="fas fa-paper-plane"></i> Submit</button>
    </form>

    <div class="result"><?php echo $message; ?></div>

    <?php if ($submittedData): ?>
    <div class="details">
      <h3>Registered Details</h3>
      <p><b>Name:</b> <?= $submittedData['fullname'] ?></p>
      <p><b>USN:</b> <?= $submittedData['usn'] ?></p>
      <p><b>Gender:</b> <?= $submittedData['gender'] ?></p>
      <p><b>Email:</b> <?= $submittedData['email'] ?></p>
      <p><b>Phone:</b> <?= $submittedData['phone'] ?></p>
      <p><b>Address:</b> <?= $submittedData['address'] ?></p>
    </div>
    <?php endif; ?>

    <div style='text-align:center;margin-top:15px;'>
      <a href="admin.php" style="color:white;">Go to Admin Dashboard</a>
    </div>
  </div>

</body>
</html>
