<?php
include "../../auth/session.php";
include "../../config/db.php";

if ($_SESSION['role'] !== 'student') {
    header("Location: ../../home.php?error=Unauthorized+access");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: student_view.php");
    exit();
}

$quiz_id = intval($_GET['id']);
$student_id = $_SESSION['user_id'];

// Check if they already attempted it
$check_attempt = mysqli_query($conn, "SELECT id FROM quiz_attempts WHERE quiz_id = $quiz_id AND student_id = $student_id");
if (mysqli_num_rows($check_attempt) > 0) {
    header("Location: student_view.php?error=" . urlencode("You have already completed this AI Quiz."));
    exit();
}

// Fetch Quiz Data
$quiz_sql = "SELECT q.*, s.subject_name 
             FROM quizzes q 
             JOIN subjects s ON q.subject_id = s.id 
             WHERE q.id = $quiz_id AND q.is_active = 1";
$quiz_res = mysqli_query($conn, $quiz_sql);

if (mysqli_num_rows($quiz_res) == 0) {
    echo "Quiz not found or not active.";
    exit();
}

$quiz = mysqli_fetch_assoc($quiz_res);

// Enforce time window check (simplified here to allow testing, but strict in production)
$current_date = date('Y-m-d');
$current_time = date('H:i:s');
if ($quiz['quiz_date'] != $current_date || $current_time < $quiz['start_time'] || $current_time > $quiz['end_time']) {
    die("<div style='text-align:center; margin-top:100px; font-family:sans-serif;'><h2>This Exam window is closed or not started yet!</h2><a href='student_view.php'>Back to Dashboard</a></div>");
}

// Fetch Questions
$q_sql = "SELECT * FROM quiz_questions WHERE quiz_id = $quiz_id ORDER BY RAND()";
$q_res = mysqli_query($conn, $q_sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Take Quiz - <?php echo htmlspecialchars($quiz['title']); ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', sans-serif; }
        .quiz-header { background: #2c3e50; color: white; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 1000; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .quiz-container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .question-card { background: white; padding: 30px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 8px 16px rgba(0,0,0,0.05); }
        .question-text { font-size: 20px; font-weight: 600; color: #2c3e50; margin-bottom: 25px; }
        .option-label { display: block; background: #f8f9fa; padding: 15px 20px; border-radius: 8px; border: 2px solid #ecf0f1; margin-bottom: 12px; cursor: pointer; transition: all 0.2s; font-size: 16px; }
        .option-label:hover { border-color: #3498db; background: #e8f4fd; }
        input[type="radio"] { display: none; }
        input[type="radio"]:checked + .option-label { border-color: #2ecc71; background: #ebfcf0; box-shadow: 0 0 0 2px rgba(46, 204, 113, 0.2); font-weight: bold; }
        .timer-badge { background: #e74c3c; color: white; padding: 8px 20px; border-radius: 30px; font-weight: bold; font-size: 18px; display: flex; align-items: center; gap: 10px; animation: pulse 2s infinite; }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(231, 76, 60, 0.7); } 70% { box-shadow: 0 0 0 10px rgba(231, 76, 60, 0); } 100% { box-shadow: 0 0 0 0 rgba(231, 76, 60, 0); } }
    </style>
</head>
<body>

<div class="quiz-header">
    <div>
        <h2 style="margin: 0;"><i class="fas fa-file-signature"></i> <?php echo htmlspecialchars($quiz['title']); ?></h2>
        <p style="margin: 5px 0 0 0; opacity: 0.8;"><?php echo htmlspecialchars($quiz['subject_name']); ?> | AI-Powered Exam</p>
    </div>
    <div class="timer-badge" id="timerBadge">
        <i class="fas fa-stopwatch"></i> <span id="timeRemaining"><?php echo str_pad($quiz['time_limit'], 2, "0", STR_PAD_LEFT); ?>:00</span>
    </div>
</div>

<div class="quiz-container">
    <?php if (mysqli_num_rows($q_res) == 0): ?>
        <div class="alert-error" style="text-align: center; font-size: 18px;">This quiz has no registered questions. Contact your teacher.</div>
        <div style="text-align: center;"><a href="student_view.php" class="btn btn-cancel">Go Back</a></div>
    <?php else: ?>
        <form action="submit.php" method="POST" id="quizForm">
            <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
            <input type="hidden" name="total_questions" value="<?php echo mysqli_num_rows($q_res); ?>">
            
            <?php 
            $i = 1;
            while ($q = mysqli_fetch_assoc($q_res)): 
            ?>
                <div class="question-card" id="q_<?php echo $i; ?>">
                    <div class="question-text">
                        <span style="color: #3498db;">Q<?php echo $i; ?>.</span> <?php echo nl2br(htmlspecialchars($q['question_text'])); ?>
                    </div>
                    
                    <input type="hidden" name="q_id_<?php echo $i; ?>" value="<?php echo $q['id']; ?>">
                    
                    <div>
                        <input type="radio" name="ans_<?php echo $i; ?>" id="opt_<?php echo $i; ?>_A" value="A">
                        <label for="opt_<?php echo $i; ?>_A" class="option-label">A) <?php echo htmlspecialchars($q['option_a']); ?></label>

                        <input type="radio" name="ans_<?php echo $i; ?>" id="opt_<?php echo $i; ?>_B" value="B">
                        <label for="opt_<?php echo $i; ?>_B" class="option-label">B) <?php echo htmlspecialchars($q['option_b']); ?></label>

                        <input type="radio" name="ans_<?php echo $i; ?>" id="opt_<?php echo $i; ?>_C" value="C">
                        <label for="opt_<?php echo $i; ?>_C" class="option-label">C) <?php echo htmlspecialchars($q['option_c']); ?></label>

                        <input type="radio" name="ans_<?php echo $i; ?>" id="opt_<?php echo $i; ?>_D" value="D">
                        <label for="opt_<?php echo $i; ?>_D" class="option-label">D) <?php echo htmlspecialchars($q['option_d']); ?></label>
                    </div>
                </div>
            <?php 
            $i++;
            endwhile; 
            ?>
            
            <div style="text-align: center; margin-top: 50px; margin-bottom: 100px;">
                <button type="submit" class="btn btn-add" style="font-size: 20px; padding: 15px 50px; border-radius: 50px; box-shadow: 0 10px 20px rgba(46, 204, 113, 0.3);"><i class="fas fa-check-circle"></i> Submit Final Answers</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
// Timer Logic
var durationMinutes = <?php echo intval($quiz['time_limit']); ?>;
var timeRemaining = durationMinutes * 60;
var timerDisplay = document.getElementById('timeRemaining');
var form = document.getElementById('quizForm');
var timerInterval;

function startTimer() {
    timerInterval = setInterval(function() {
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            alert("Time's up! Your answers will be submitted automatically.");
            if(form) form.submit();
        } else {
            timeRemaining--;
            document.cookie = "quiz_time_left_" + <?php echo $quiz_id; ?> + "=" + timeRemaining + "; path=/;";
            
            var m = Math.floor(timeRemaining / 60);
            var s = timeRemaining % 60;
            m = m < 10 ? '0' + m : m;
            s = s < 10 ? '0' + s : s;
            timerDisplay.innerText = m + ":" + s;
            
            // Turn timer red when under 5 minutes
            if (timeRemaining < 300) {
                document.getElementById('timerBadge').style.animation = "pulse 0.5s infinite";
                document.getElementById('timerBadge').style.background = "#c0392b";
            }
        }
    }, 1000);
}

// Restore state from cookie if refreshed
function checkExistingTimer() {
    var match = document.cookie.match(new RegExp('(^| )quiz_time_left_<?php echo $quiz_id; ?>=([^;]+)'));
    if (match) {
        var existingTime = parseInt(match[2]);
        if (existingTime > 0 && existingTime <= durationMinutes * 60) {
            timeRemaining = existingTime;
        }
    }
}

checkExistingTimer();
if(form) startTimer();

// Prevent accidental exit
window.addEventListener('beforeunload', function (e) {
    if (timeRemaining > 0) {
        e.preventDefault();
        e.returnValue = 'You have an active exam! Leaving will submit your current answers.';
    }
});
</script>

</body>
</html>
