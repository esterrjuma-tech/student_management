
<?php
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php?message=" . urlencode("Invalid student ID."));
    exit;
}

$stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    $message = "Student deleted successfully.";
} else {
    $message = "Student not found.";
}

header("Location: index.php?message=" . urlencode($message));
exit;
?>
