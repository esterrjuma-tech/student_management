
<?php
require_once "db.php";

$error = "";
$student_number = "";
$full_name = "";
$gender = "";
$course = "";
$email = "";
$phone = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_number = trim($_POST["student_number"] ?? "");
    $full_name = trim($_POST["full_name"] ?? "");
    $gender = $_POST["gender"] ?? "";
    $course = trim($_POST["course"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    if ($student_number === "" || $full_name === "") {
        $error = "Student number and full name are required.";
    } elseif (!in_array($gender, ["Male", "Female", "Other", ""], true)) {
        $error = "Please select a valid gender.";
    } elseif ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO students
                (student_number, full_name, gender, course, email, phone)
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $student_number,
                $full_name,
                $gender,
                $course,
                $email,
                $phone
            ]);

            header("Location: index.php?message=" . urlencode("Student added successfully."));
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                $error = "This student number already exists.";
            } else {
                $error = "Unable to save student. Please check the database.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container small-container">
    <section class="card">
        <h1>Add New Student</h1>
        <p>Enter the student's information below.</p>

        <?php if ($error !== ""): ?>
            <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></div>
        <?php endif; ?>

        <form action="add.php" method="POST">
            <label for="student_number">Student Number *</label>
            <input
                type="text"
                id="student_number"
                name="student_number"
                maxlength="30"
                required
                value="<?= htmlspecialchars($student_number, ENT_QUOTES, "UTF-8") ?>"
            >

            <label for="full_name">Full Name *</label>
            <input
                type="text"
                id="full_name"
                name="full_name"
                maxlength="100"
                required
                value="<?= htmlspecialchars($full_name, ENT_QUOTES, "UTF-8") ?>"
            >

            <label for="gender">Gender</label>
            <select id="gender" name="gender">
                <option value="">Select gender</option>
                <option value="Male" <?= $gender === "Male" ? "selected" : "" ?>>Male</option>
                <option value="Female" <?= $gender === "Female" ? "selected" : "" ?>>Female</option>
                <option value="Other" <?= $gender === "Other" ? "selected" : "" ?>>Other</option>
            </select>

            <label for="course">Course</label>
            <input
                type="text"
                id="course"
                name="course"
                maxlength="100"
                value="<?= htmlspecialchars($course, ENT_QUOTES, "UTF-8") ?>"
            >

            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                maxlength="100"
                value="<?= htmlspecialchars($email, ENT_QUOTES, "UTF-8") ?>"
            >

            <label for="phone">Phone</label>
            <input
                type="text"
                id="phone"
                name="phone"
                maxlength="20"
                value="<?= htmlspecialchars($phone, ENT_QUOTES, "UTF-8") ?>"
            >

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Student</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </section>
</div>

</body>
</html>
