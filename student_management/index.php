
<?php
require_once "db.php";

$search = trim($_GET["search"] ?? "");

if ($search !== "") {
    $stmt = $pdo->prepare(
        "SELECT * FROM students
         WHERE student_number LIKE :search
         OR full_name LIKE :search
         OR course LIKE :search
         ORDER BY id DESC"
    );
    $stmt->execute(["search" => "%$search%"]);
} else {
    $stmt = $pdo->query(
        "SELECT * FROM students ORDER BY id DESC"
    );
}

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

function e($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <header class="top-bar">
        <div>
            <h1>Student Management System</h1>
            <p>Manage student information easily</p>
        </div>
        <a href="add.php" class="btn btn-primary">+ Add Student</a>
    </header>

    <?php if (isset($_GET["message"])): ?>
        <div class="alert">
            <?= e($_GET["message"]) ?>
        </div>
    <?php endif; ?>

    <section class="card">
        <h2>Student Records</h2>

        <form action="index.php" method="GET" class="search-form">
            <input
                type="text"
                name="search"
                placeholder="Search name, student number or course"
                value="<?= e($search) ?>"
            >
            <button type="submit" class="btn btn-primary">
                Search
            </button>
            <a href="index.php" class="btn btn-secondary">Reset</a>
        </form>

        <p>Total students displayed: <strong><?= count($students) ?></strong></p>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student Number</th>
                        <th>Full Name</th>
                        <th>Gender</th>
                        <th>Course</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($students) > 0): ?>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= e($student["id"]) ?></td>
                        <td><?= e($student["student_number"]) ?></td>
                        <td><?= e($student["full_name"]) ?></td>
                        <td><?= e($student["gender"]) ?></td>
                        <td><?= e($student["course"]) ?></td>
                        <td><?= e($student["email"]) ?></td>
                        <td><?= e($student["phone"]) ?></td>
                        <td class="actions">
                            <a
                                class="btn btn-edit"
                                href="edit.php?id=<?= e($student["id"]) ?>"
                            >Edit</a>

                            <form
                                action="delete.php"
                                method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this student?');"
                            >
                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= e($student["id"]) ?>"
                                >
                                <button
                                    type="submit"
                                    class="btn btn-danger"
                                >Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="empty">
                            No student records found.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

</body>
</html>
