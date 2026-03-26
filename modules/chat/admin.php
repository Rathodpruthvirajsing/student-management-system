<?php
include "../../auth/session.php";
include "../../config/db.php";

// Only admin/teacher
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../../home.php?error=Unauthorized");
    exit();
}

include "../../includes/header.php";
include "../../includes/sidebar.php";

$teacher_id = intval($_SESSION['user_id']);

// Fetch all students who have messaged the teacher OR just list all students
// A simpler approach for the school system: List all students, but highlight those with unread messages

$sql = "SELECT u.id, u.name, s.course_id, c.course_name,
        (SELECT COUNT(*) FROM messages m WHERE m.sender_id = u.id AND m.receiver_id = $teacher_id AND m.is_read = 0) as unread_count
        FROM users u 
        LEFT JOIN students s ON u.email = s.email
        LEFT JOIN courses c ON s.course_id = c.id
        WHERE u.role = 'student'
        ORDER BY unread_count DESC, u.name ASC";
$student_res = mysqli_query($conn, $sql);
$students = mysqli_fetch_all($student_res, MYSQLI_ASSOC);
?>

<div class="content">
    <div class="header-section" style="margin-bottom: 25px;">
        <h2>Student Issues & Chat</h2>
        <p style="color: #6c757d;">Resolve student queries and chat live</p>
    </div>

    <div style="display: flex; gap: 20px; height: 65vh; max-height: 800px; background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
        
        <!-- Sidebar - Student List -->
        <div style="width: 35%; border-right: 1px solid #eee; display: flex; flex-direction: column;">
            <div style="padding: 15px; border-bottom: 1px solid #eee; background: #f8f9fa;">
                <input type="text" id="studentSearch" placeholder="Search students..." style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;" onkeyup="filterStudents()">
            </div>
            <div id="studentListContainer" style="overflow-y: auto; flex: 1;">
                <?php foreach($students as $student): ?>
                    <div class="chat-contact" data-id="<?php echo $student['id']; ?>" data-name="<?php echo strtolower($student['name']); ?>" onclick="openChat(<?php echo $student['id']; ?>, '<?php echo addslashes($student['name']); ?>', this)" style="padding: 15px; border-bottom: 1px solid #eee; cursor: pointer; display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 40px; height: 40px; background: #28a745; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                <?php echo strtoupper(substr($student['name'], 0, 1)); ?>
                            </div>
                            <div>
                                <strong style="color: #333; display: block;"><?php echo htmlspecialchars($student['name']); ?></strong>
                                <span style="font-size: 12px; color: #666;"><?php echo htmlspecialchars($student['course_name'] ?? 'No Course'); ?></span>
                            </div>
                        </div>
                        <?php if($student['unread_count'] > 0): ?>
                            <span class="unread-badge" style="background: red; color: white; border-radius: 50%; padding: 4px 8px; font-size: 12px; font-weight: bold;"><?php echo $student['unread_count']; ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div style="flex: 1; display: flex; flex-direction: column; background: #fdfdfd;">
            <!-- Chat Header -->
            <div id="chatHeader" style="padding: 15px; border-bottom: 1px solid #eee; background: #f8f9fa; display: none; align-items: center; justify-content: space-between;">
                <strong id="chatPartnerName" style="font-size: 16px; color: #333;">Select a student</strong>
            </div>

            <!-- Messages Container -->
            <div id="messagesArea" style="flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px;">
                <div style="text-align: center; color: #aaa; margin-top: 50px;">Please select someone from the list to view your messages</div>
            </div>

            <!-- Input Area -->
            <div id="inputArea" style="padding: 15px; border-top: 1px solid #eee; background: #fff; display: none; gap: 10px;">
                <input type="hidden" id="activeChatId" value="0">
                <input type="text" id="messageInput" placeholder="Type your reply here..." style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;" onkeypress="if(event.key === 'Enter') sendMessage()">
                <button onclick="sendMessage()" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Reply</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentChatInterval = null;

function filterStudents() {
    let q = document.getElementById('studentSearch').value.toLowerCase();
    document.querySelectorAll('.chat-contact').forEach(el => {
        if(el.dataset.name.includes(q)) {
            el.style.display = 'flex';
        } else {
            el.style.display = 'none';
        }
    });
}

function openChat(id, name, element) {
    // UI Updates
    document.querySelectorAll('.chat-contact').forEach(el => el.style.background = 'transparent');
    if(element) element.style.background = '#e9ecef';
    
    // Clear unread badge instantly
    if(element) {
        let badge = element.querySelector('.unread-badge');
        if(badge) badge.style.display = 'none';
    }

    document.getElementById('chatHeader').style.display = 'flex';
    document.getElementById('inputArea').style.display = 'flex';
    document.getElementById('chatPartnerName').innerText = 'Resolving issue for: ' + name;
    document.getElementById('activeChatId').value = id;
    
    // Fetch messages
    fetchMessages(id);
    
    // Start polling
    if(currentChatInterval) clearInterval(currentChatInterval);
    currentChatInterval = setInterval(() => { fetchMessages(id, false); }, 3000);
}

function fetchMessages(id, scrollDown = true) {
    fetch('fetch.php?other_id=' + id)
    .then(r => r.json())
    .then(data => {
        let html = '';
        if(data.length === 0) {
            html = '<div style="text-align: center; color: #aaa; margin-top: 20px;">No issues reported yet. Send a message to start.</div>';
        } else {
            data.forEach(msg => {
                let align = msg.is_mine ? 'align-self: flex-end; background: #28a745; color: white;' : 'align-self: flex-start; background: #f1f0f0; color: #333;';
                html += `
                    <div style="max-width: 70%; padding: 10px 15px; border-radius: 12px; margin-bottom: 5px; ${align}">
                        <div style="word-break: break-word; font-size: 14px;">${msg.message}</div>
                        <div style="font-size: 10px; opacity: 0.8; text-align: right; margin-top: 5px;">${msg.time}</div>
                    </div>
                `;
            });
        }
        
        let area = document.getElementById('messagesArea');
        area.innerHTML = html;
        if(scrollDown) {
            area.scrollTop = area.scrollHeight;
        }
    });
}

function sendMessage() {
    let input = document.getElementById('messageInput');
    let message = input.value.trim();
    let receiverId = document.getElementById('activeChatId').value;
    
    if(message === '' || receiverId == 0) return;
    
    input.value = '';
    
    let formData = new FormData();
    formData.append('receiver_id', receiverId);
    formData.append('message', message);
    
    fetch('send.php', { method: 'POST', body: formData })
    .then(r => r.text())
    .then(data => {
        fetchMessages(receiverId, true);
    });
}
</script>

<?php include "../../includes/footer.php"; ?>
