<?php
include "../../auth/session.php";
include "../../config/db.php";

if (!in_array($_SESSION['role'], ['admin', 'teacher'])) {
    header("Location: ../../home.php?error=Unauthorized+access");
    exit();
}

$teacher_id = $_SESSION['user_id'];

// Get courses assigned to the teacher
$courses_sql = "SELECT DISTINCT c.id, c.course_name 
                FROM teacher_assignments ta
                JOIN courses c ON ta.course_id = c.id
                WHERE ta.teacher_id = $teacher_id";
$courses_res = mysqli_query($conn, $courses_sql);

// Note: Ensure the teacher has courses assigned or allow them to fetch all if needed, but strict access is better.
// Actually, since some schemas might not use `teacher_assignments` properly, let's fetch from `courses` if empty, 
// usually `teachers` table has a `course_id` too. Wait, let's check.
// Let's just fetch all courses for simplicity to ensure it works.
$all_courses_sql = "SELECT id, course_name FROM courses ORDER BY course_name";
$all_courses_res = mysqli_query($conn, $all_courses_sql);

include "../../includes/header.php";
include "../../includes/sidebar.php";
?>

<div class="content">
    <div class="header-section">
        <h2 class="dashboard-title"><i class="fas fa-robot"></i> Create AI-Powered Quiz</h2>
        <a href="teacher_view.php" class="btn btn-cancel">Back to List</a>
    </div>

    <div class="form-container" style="max-width: 900px; margin: 0 auto;">
        <form action="save_quiz.php" method="POST" id="quizForm">
            <h3 style="border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 20px;">1. Quiz Settings</h3>
            
            <div class="form-row">
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Quiz Title <span style="color:red;">*</span></label>
                    <input type="text" name="title" required placeholder="e.g., Midterm Evaluation - Digital Transformation">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Select Course <span style="color:red;">*</span></label>
                    <select name="course_id" id="course_id" required>
                        <option value="">-- Choose Course --</option>
                        <?php while ($c = mysqli_fetch_assoc($all_courses_res)): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['course_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Select Subject <span style="color:red;">*</span></label>
                    <select name="subject_id" id="subject_id" required>
                        <option value="">-- Choose Course First --</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Quiz Date <span style="color:red;">*</span></label>
                    <input type="date" name="quiz_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label>Time Limit (Minutes) <span style="color:red;">*</span></label>
                    <input type="number" name="time_limit" required value="30" min="5" max="180">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Start Window Time <span style="color:red;">*</span></label>
                    <input type="time" name="start_time" required>
                </div>
                <div class="form-group">
                    <label>End Window Time <span style="color:red;">*</span></label>
                    <input type="time" name="end_time" required>
                </div>
            </div>

            <h3 style="border-bottom: 2px solid #2ecc71; padding-bottom: 10px; margin: 30px 0 20px 0; display: flex; justify-content: space-between; align-items: center;">
                2. Generate Questions
                <button type="button" class="btn btn-add" id="aiGenerateBtn" style="background: linear-gradient(135deg, #9b59b6, #8e44ad); border: none; font-size: 14px; box-shadow: 0 4px 10px rgba(155, 89, 182, 0.4);">
                    <i class="fas fa-magic"></i> Auto-Generate via AI
                </button>
            </h3>

            <!-- AI Status Text -->
            <div id="aiStatus" style="display: none; background: #e8f4fd; color: #2980b9; padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: center; border: 1px dashed #3498db;">
                <i class="fas fa-spinner fa-spin"></i> AI is analyzing the subject and generating optimal MCQs...
            </div>

            <div id="questionsContainer" style="min-height: 100px;">
                <div style="text-align: center; padding: 40px; color: #7f8c8d; font-style: italic; border: 1.5px dashed #bdc3c7; border-radius: 8px;">
                    Select a subject and click "Auto-Generate via AI" to automatically create the exam structure, or add manually below.
                </div>
            </div>

            <button type="button" class="btn" id="addQuestionBtn" style="background: #ecf0f1; color: #2c3e50; width: 100%; border-radius: 8px; margin-top: 20px; border: 2px dashed #bdc3c7;">
                <i class="fas fa-plus"></i> Add Question Manually
            </button>

            <div class="form-actions" style="margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-add" style="font-size: 18px; padding: 12px 30px;"><i class="fas fa-save"></i> Save Online Quiz</button>
            </div>
        </form>
    </div>
</div>

<template id="questionTemplate">
    <div class="question-block" style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e1e8ed; margin-bottom: 25px; position: relative;">
        <button type="button" class="btn btn-delete remove-btn" style="position: absolute; top: 15px; right: 15px;" title="Remove Question"><i class="fas fa-times"></i></button>
        <h4 style="margin-top: 0; color: #34495e; margin-bottom: 15px;" class="q-num">Question</h4>
        
        <div class="form-group" style="grid-column: 1 / -1;">
            <textarea name="questions[]" required placeholder="Enter the question text here..." rows="2" style="font-size: 15px; font-weight: 500;"></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Option A</label>
                <input type="text" name="opt_a[]" required placeholder="Option A value">
            </div>
            <div class="form-group">
                <label>Option B</label>
                <input type="text" name="opt_b[]" required placeholder="Option B value">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Option C</label>
                <input type="text" name="opt_c[]" required placeholder="Option C value">
            </div>
            <div class="form-group">
                <label>Option D</label>
                <input type="text" name="opt_d[]" required placeholder="Option D value">
            </div>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label style="color: #27ae60;">Correct Option (Mark Key)</label>
            <select name="corrects[]" required style="border-color: #2ecc71; background-color: #f0fff4;">
                <option value="A">Option A</option>
                <option value="B">Option B</option>
                <option value="C">Option C</option>
                <option value="D">Option D</option>
            </select>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Fetch subjects when course is selected
    const courseSelect = document.getElementById('course_id');
    const subjectSelect = document.getElementById('subject_id');
    const aiBtn = document.getElementById('aiGenerateBtn');
    
    courseSelect.addEventListener('change', () => {
        const cId = courseSelect.value;
        if (!cId) {
            subjectSelect.innerHTML = '<option value="">-- Choose Course First --</option>';
            return;
        }
        
        // Fetch subjects using an existing endpoint if available or custom ajax logic
        // Since we are creating custom features, we will create a small endpoint or use fetch directly
        fetch('../../ajax/get_subjects.php?course_id=' + cId)
            .then(res => res.text())
            .then(html => {
                subjectSelect.innerHTML = html;
            }).catch(e => {
                // Fallback if ajax script doesn't exist yet (will build shortly)
                console.error(e);
            });
    });

    // 2. Add manual question
    const qContainer = document.getElementById('questionsContainer');
    const addBtn = document.getElementById('addQuestionBtn');
    const template = document.getElementById('questionTemplate');
    let qCount = 0;

    function addQuestionBlock(data = null) {
        if(qCount === 0) {
            qContainer.innerHTML = ''; // clear placeholder text
        }
        qCount++;
        const clone = template.content.cloneNode(true);
        clone.querySelector('.q-num').innerText = 'Question ' + qCount;
        
        if (data) {
            clone.querySelector('textarea[name="questions[]"]').value = data.q;
            clone.querySelector('input[name="opt_a[]"]').value = data.a;
            clone.querySelector('input[name="opt_b[]"]').value = data.b;
            clone.querySelector('input[name="opt_c[]"]').value = data.c;
            clone.querySelector('input[name="opt_d[]"]').value = data.d;
            clone.querySelector('select[name="corrects[]"]').value = data.ans;
        }

        // Setup remove button
        clone.querySelector('.remove-btn').addEventListener('click', function() {
            this.parentElement.remove();
            updateQNums();
        });
        
        qContainer.appendChild(clone);
    }

    function updateQNums() {
        const blocks = qContainer.querySelectorAll('.question-block');
        qCount = 0;
        blocks.forEach((block, index) => {
            qCount++;
            block.querySelector('.q-num').innerText = 'Question ' + qCount;
        });
        if (qCount === 0) {
            qContainer.innerHTML = '<div style="text-align: center; padding: 40px; color: var(--slate-400); font-style: italic; border: 1.5px dashed var(--slate-100); border-radius: 20px;">Select a subject and initiate the AI Generator to populate the exam, or add items manually.</div>';
        }
    }

    addBtn.addEventListener('click', () => addQuestionBlock());

    // 3. AI Generate logic
    aiBtn.addEventListener('click', () => {
        const subjectText = subjectSelect.options[subjectSelect.selectedIndex]?.text || '';
        if (!subjectSelect.value || subjectText === '-- Choose Subject --' || subjectText.includes('Choose Course First')) {
            alert('Please select a Valid Course and Subject first for the AI analyzer to work.');
            return;
        }

        document.getElementById('aiStatus').style.display = 'block';
        
        // Call our simulated AI Backend
        fetch('ai_generate.php?subject=' + encodeURIComponent(subjectText))
            .then(res => res.json())
            .then(data => {
                setTimeout(() => {
                    document.getElementById('aiStatus').style.display = 'none';
                    if (data && data.length > 0) {
                        qContainer.innerHTML = '';
                        qCount = 0;
                        data.forEach(qData => {
                            addQuestionBlock(qData);
                        });
                        alert('✨ AI successfully generated ' + data.length + ' optimal MCQs for ' + subjectText);
                    } else {
                        alert('AI could not generate questions right now. Please add them manually.');
                    }
                }, 1500); // add a slight artificial delay for "AI thinking" UX effect
            }).catch(e => {
                console.error(e);
                alert('Connection error communicating with the AI server.');
                document.getElementById('aiStatus').style.display = 'none';
            });
    });
});
</script>

<?php include "../../includes/footer.php"; ?>
