<?php
include "../../auth/session.php";
include "../../config/db.php";

if ($_SESSION['role'] !== 'student' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../home.php?error=Unauthorized+access");
    exit();
}

$student_id = $_SESSION['user_id'];
$quiz_id = intval($_POST['quiz_id']);
$total_questions = intval($_POST['total_questions']);
$score = 0;

// Fetch correct answers
$q_sql = "SELECT id, correct_option FROM quiz_questions WHERE quiz_id = $quiz_id";
$q_res = mysqli_query($conn, $q_sql);

$correct_answers = [];
while ($row = mysqli_fetch_assoc($q_res)) {
    $correct_answers[$row['id']] = $row['correct_option'];
}

// Calculate Score
for ($i = 1; $i <= $total_questions; $i++) {
    if (isset($_POST["q_id_$i"]) && isset($_POST["ans_$i"])) {
        $q_id = intval($_POST["q_id_$i"]);
        $ans = strtoupper(trim($_POST["ans_$i"]));
        
        if (isset($correct_answers[$q_id]) && $correct_answers[$q_id] === $ans) {
            $score++;
        }
    }
}

// Insert attempt
$sql = "INSERT INTO quiz_attempts (quiz_id, student_id, score, total_questions) VALUES ($quiz_id, $student_id, $score, $total_questions)";
if (mysqli_query($conn, $sql)) {
    // Also add to generic marks if possible! The user mentioned "this mark can addin inyou result"
    // We should check if an exam exists for this subject, but since it's a quiz, let's keep it here.
    // We can add a simple entry to `marks` table if needed, but quiz_attempts tracks it distinctively.
    
    // Clear the timer cookie
    setcookie("quiz_time_left_" . $quiz_id, "", time() - 3600, "/");
    
    header("Location: student_view.php?msg=" . urlencode("Exam completed! You scored $score out of $total_questions."));
} else {
    echo "Error saving result: " . mysqli_error($conn);
}
?>
