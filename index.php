<?php
session_start(); // ✅ Start session

// ✅ Try connecting using Railway DATABASE_URL if available
$db_url = getenv("DATABASE_URL");

if ($db_url) {
  $db_parts = parse_url($db_url);
  $servername = $db_parts["host"];
  $username   = $db_parts["user"];
  $password   = $db_parts["pass"];
  $dbname     = ltrim($db_parts["path"], "/");
  $port       = $db_parts["port"];
} else {
  // ✅ Local fallback (XAMPP)
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "regform_db";
  $port = 3306;
}

// ✅ Connect to database
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registration Form</title>
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
    }

    .container {
      background-color: rgba(0, 0, 0, 0.6);
      width: 450px;
      margin: 60px auto;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0,0,0,0.8);
      color: white;
    }

    h2 {
      text-align: center;
      color: #fff;
      margin-bottom: 25px;
    }

    .form-group {
      position: relative;
      margin-top: 20px;
    }

    .form-group i {
      position: absolute;
      top: 50%;
      left: 10px;
      transform: translateY(-50%);
      color: #555;
      font-size: 18px;
    }

    input, select, textarea {
      width: 100%;
      padding: 10px 10px 10px 40px;
      border: none;
      border-radius: 6px;
      outline: none;
      font-size: 15px;
      box-sizing: border-box;
    }

    select { color: #333; }
    textarea { resize: none; }

    input:focus, select:focus, textarea:focus {
      border: 2px solid #4CAF50;
    }

    ::placeholder { color: #777; }

    button {
      width: 100%;
      background-color: #4CAF50;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 6px;
      margin-top: 25px;
      cursor: pointer;
      font-size: 16px;
      transition: 0.3s;
    }

    button:hover { background-color: #45a049; }

    .result { margin-top: 15px; text-align: center; }

    .details {
      margin-top: 25px;
      padding: 20px;
      background-color: rgba(255, 255, 255, 0.9);
      color: #000;
      border-radius: 8px;
    }

    .details h3 { text-align: center; color: #333; }

    .details p { margin: 8px 0; font-size: 15px; }

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
    .back-btn:hover { background-color: rgb(120, 120, 120); transform: scale(1.10); }
  </style>
</head>
<body>

  <div class="container">
    <h2><i class="fas fa-user-edit"></i> Registration Form</h2>

    <form id="registrationForm" method="POST" action="">
      <div class="form-group">
        <i class="fas fa-user"></i>
        <input type="text" id="fullname" name="fullname" placeholder="Full Name" required>
      </div>

      <div class="form-group">
        <i class="fas fa-id-card"></i>
        <input type="text" id="usn" name="USN" placeholder="USN" required>
      </div>

      <div class="form-group">
        <i class="fas fa-venus-mars"></i>
        <select name="gender" id="gender" required>
          <option value="">Select Gender</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
      </div>

      <div class="form-group">
        <i class="fas fa-envelope"></i>
        <input type="email" id="email" name="email" placeholder="Email ID" required>
      </div>

      <div class="form-group">
        <i class="fas fa-phone"></i>
        <input type="text" id="phone" name="phone" placeholder="Phone Number" maxlength="10" required>
      </div>

      <div class="form-group">
        <i class="fas fa-map-marker-alt"></i>
        <textarea id="address" name="address" rows="3" placeholder="Address" required></textarea>
      </div>

      <button type="submit"><i class="fas fa-paper-plane"></i> Submit Application</button>
    </form>

    <div class="result"><?php echo $message; ?></div>

    <?php if ($submittedData): ?>
    <div class="details">
      <h3><i class="fas fa-check-circle"></i> Registered Details</h3>
      <p><strong>Name:</strong> <?php echo $submittedData['fullname']; ?></p>
      <p><strong>USN:</strong> <?php echo $submittedData['usn']; ?></p>
      <p><strong>Gender:</strong> <?php echo $submittedData['gender']; ?></p>
      <p><strong>Email:</strong> <?php echo $submittedData['email']; ?></p>
      <p><strong>Phone:</strong> <?php echo $submittedData['phone']; ?></p>
      <p><strong>Address:</strong> <?php echo $submittedData['address']; ?></p>
    </div>
    <?php endif; ?>

    <div style='text-align:center;'>
      <a href="admin.php" class="back-btn"><i class="fas fa-shield-alt"></i> Admin Page</a>
    </div>
  </div>
</body>
</html>
