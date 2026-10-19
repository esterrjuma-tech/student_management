
<?php
require_once "db.php";

function e($value) {
    return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php?message=" . urlencode("Invalid student ID."));
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    header("Location: index.php?message=" . urlencode("Student not found."));
    exit;
}

$error = "";

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
            $update = $pdo->prepare(
                "UPDATE students
                 SET student_number = ?, full_name = ?, gender = ?,
                     course = ?, email = ?, phone = ?
                 WHERE id = ?"
            );

            $update->execute([
                $student_number,
                $full_name,
                $gender,
                $course,
                $email,
                $phone,
                $id
            ]);

            header("Location: index.php?message=" . urlencode("Student updated successfully."));
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                $error = "That student number is already in use.";
            } else {
                $error = "Unable to update student.";
            }
        }
    }

    $student["student_number"] = $student_number;
    $student["full_name"] = $full_name;
    $student["gender"] = $gender;
    $student["course"] = $course;
    $student["email"] = $email;
    $student["phone"] = $phone;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container small-container">
    <section class="card">
        <h1>Edit Student</h1>
        <p>Update the student's information.</p>

        <?php if ($error !== ""): ?>
            <div class="error"><?= e($error) ?></div>
        <?php endif; ?>

        <form action="edit.php?id=<?= e($id) ?>" method="POST">
            <label for="student_number">Student Number *</label>
            <input
                id="student_number"
                name="student_number"
                maxlength="30"
                required
                value="<?= e($student["student_number"]) ?>"
            >

            <label for="full_name">Full Name *</label>
            <input
                id="full_name"
                name="full_name"
                maxlength="100"
                required
                value="<?= e($student["full_name"]) ?>"
            >

            <label for="gender">Gender</label>
            <select id="gender" name="gender">
                <option value="">Select gender</option>
                <option value="Male" <?= $student["gender"] === "Male" ? "selected" : "" ?>>Male</option>
                <option value="Female" <?= $student["gender"] === "Female" ? "selected" : "" ?>>Female</option>
                <option value="Other" <?= $student["gender"] === "Other" ? "selected" : "" ?>>Other</option>
            </select>

            <label for="course">Course</label>
            <input
                id="course"
                name="course"
                maxlength="100"
                value="<?= e($student["course"]) ?>"
            >

            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                maxlength="100"
                value="<?= e($student["email"]) ?>"
            >

            <label for="phone">Phone</label>
            <input
                id="phone"
                name="phone"
                maxlength="20"
                value="<?= e($student["phone"]) ?>"
            >

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Student</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </section>
</div>

</body>
</html>
