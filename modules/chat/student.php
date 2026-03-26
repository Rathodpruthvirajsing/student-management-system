<?php
include "../../auth/session.php";
include "../../config/db.php";

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'student' && $_SESSION['role'] !== 'parent')) {
    header("Location: ../../home.php?error=Unauthorized");
    exit();
}

include "../../includes/header.php";
include "../../includes/sidebar.php";

$curr_user_id = intval($_SESSION['user_id']);

// Fetch all admins and teachers
$staff_sql = "SELECT id, name, role FROM users WHERE role IN ('admin', 'teacher') ORDER BY role, name";
$staff_res = mysqli_query($conn, $staff_sql);
$staff = mysqli_fetch_all($staff_res, MYSQLI_ASSOC);
?>

<div class="content">
    <div class="header-section" style="margin-bottom: 25px;">
        <h2>Connect with Faculties</h2>
        <p style="color: #6c757d;">Live chat with your teachers and administration</p>
    </div>

    <div style="display: flex; gap: 20px; height: 60vh; max-height: 800px; background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
        
        <!-- Sidebar - Staff List -->
        <div style="width: 30%; border-right: 1px solid #eee; display: flex; flex-direction: column;">
            <div style="padding: 15px; border-bottom: 1px solid #eee; background: #f8f9fa;">
                <h3 style="margin: 0; font-size: 16px; color: #333;">Faculties</h3>
            </div>
            <div style="overflow-y: auto; flex: 1;">
                <?php foreach($staff as $member): ?>
                    <div class="chat-contact" data-id="<?php echo $member['id']; ?>" onclick="openChat(<?php echo $member['id']; ?>, '<?php echo addslashes($member['name']); ?>')" style="padding: 15px; border-bottom: 1px solid #eee; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                        <div style="width: 40px; height: 40px; background: #0056b3; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                            <?php echo strtoupper(substr($member['name'], 0, 1)); ?>
                        </div>
                        <div>
                            <strong style="color: #333; display: block;"><?php echo htmlspecialchars($member['name']); ?></strong>
                            <span style="font-size: 12px; color: #666; text-transform: capitalize;"><?php echo $member['role']; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div style="flex: 1; display: flex; flex-direction: column; background: #fdfdfd;">
            <!-- Chat Header -->
            <div id="chatHeader" style="padding: 15px; border-bottom: 1px solid #eee; background: #f8f9fa; display: none;">
                <strong id="chatPartnerName" style="font-size: 16px; color: #333;">Select a faculty to start chatting</strong>
            </div>

            <!-- Messages Container -->
            <div id="messagesArea" style="flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px;">
                <div style="text-align: center; color: #aaa; margin-top: 50px;">Please select someone from the list to view your messages</div>
            </div>

            <!-- Input Area -->
            <div id="inputArea" style="padding: 15px; border-top: 1px solid #eee; background: #fff; display: none; gap: 10px;">
                <input type="hidden" id="activeChatId" value="0">
                <input type="text" id="messageInput" placeholder="Type your message here..." style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;" onkeypress="if(event.key === 'Enter') sendMessage()">
                <button onclick="sendMessage()" style="padding: 10px 20px; background: #0056b3; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Send</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentChatInterval = null;

function openChat(id, name) {
    // UI Updates
    document.querySelectorAll('.chat-contact').forEach(el => el.style.background = 'transparent');
    document.querySelector('.chat-contact[data-id="'+id+'"]').style.background = '#e9ecef';
    
    document.getElementById('chatHeader').style.display = 'block';
    document.getElementById('inputArea').style.display = 'flex';
    document.getElementById('chatPartnerName').innerText = 'Chatting with: ' + name;
    document.getElementById('activeChatId').value = id;
    
    // Clear and fetch messages
    fetchMessages(id);
    
    // Set polling interval for live chat
    if(currentChatInterval) clearInterval(currentChatInterval);
    currentChatInterval = setInterval(() => { fetchMessages(id, false); }, 3000);
}

function fetchMessages(id, scrollDown = true) {
    fetch('fetch.php?other_id=' + id)
    .then(r => r.json())
    .then(data => {
        let html = '';
        if(data.length === 0) {
            html = '<div style="text-align: center; color: #aaa; margin-top: 20px;">No messages yet. Say hi!</div>';
        } else {
            data.forEach(msg => {
                let align = msg.is_mine ? 'align-self: flex-end; background: #0056b3; color: white;' : 'align-self: flex-start; background: #f1f0f0; color: #333;';
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
    
    input.value = ''; // clear immediately
    
    let formData = new FormData();
    formData.append('receiver_id', receiverId);
    formData.append('message', message);
    
    fetch('send.php', { method: 'POST', body: formData })
    .then(r => r.text())
    .then(data => {
        fetchMessages(receiverId, true); // instantly refresh and scroll
    });
}
</script>

<?php include "../../includes/footer.php"; ?>
