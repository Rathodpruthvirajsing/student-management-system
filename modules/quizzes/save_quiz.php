<?php
include "../../auth/session.php";
include "../../config/db.php";

if (!in_array($_SESSION['role'], ['admin', 'teacher'])) {
    header("Location: ../../home.php?error=Unauthorized+access");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacher_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $course_id = intval($_POST['course_id']);
    $subject_id = intval($_POST['subject_id']);
    $quiz_date = mysqli_real_escape_string($conn, trim($_POST['quiz_date']));
    $time_limit = intval($_POST['time_limit']);
    
    // Process time windows
    $start_time = mysqli_real_escape_string($conn, trim($_POST['start_time']));
    $end_time = mysqli_real_escape_string($conn, trim($_POST['end_time']));

    // Insert Quiz Metadata
    $sql_quiz = "INSERT INTO quizzes (teacher_id, course_id, subject_id, title, quiz_date, time_limit, start_time, end_time) 
                 VALUES ($teacher_id, $course_id, $subject_id, '$title', '$quiz_date', $time_limit, '$start_time', '$end_time')";
                 
    if (mysqli_query($conn, $sql_quiz)) {
        $quiz_id = mysqli_insert_id($conn);

        // Process Questions arrays
        if (!empty($_POST['questions']) && is_array($_POST['questions'])) {
            $questions = $_POST['questions'];
            $opt_a = $_POST['opt_a'];
            $opt_b = $_POST['opt_b'];
            $opt_c = $_POST['opt_c'];
            $opt_d = $_POST['opt_d'];
            $corrects = $_POST['corrects'];

            $count = count($questions);
            for ($i = 0; $i < $count; $i++) {
                $q = mysqli_real_escape_string($conn, trim($questions[$i]));
                $a = mysqli_real_escape_string($conn, trim($opt_a[$i]));
                $b = mysqli_real_escape_string($conn, trim($opt_b[$i]));
                $c = mysqli_real_escape_string($conn, trim($opt_c[$i]));
                $d = mysqli_real_escape_string($conn, trim($opt_d[$i]));
                $ans = mysqli_real_escape_string($conn, trim($corrects[$i]));

                if (!empty($q) && !empty($a) && !empty($b)) {
                    $sql_q = "INSERT INTO quiz_questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) 
                              VALUES ($quiz_id, '$q', '$a', '$b', '$c', '$d', '$ans')";
                    mysqli_query($conn, $sql_q);
                }
            }
        }
        
        header("Location: teacher_view.php?msg=" . urlencode("AI Quiz successfully generated and saved!"));
        exit();
    } else {
        echo "Error saving quiz: " . mysqli_error($conn);
    }
}
?>
