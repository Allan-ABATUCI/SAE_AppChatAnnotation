<?php require_once 'view_begin.php'; ?>

<style>
body, html {
    margin: 0; padding: 0;
    font-family: 'Poppins', sans-serif;
    background: #fff;
    color: #222;
    height: 100vh;
    overflow: hidden;
}

.container {
    display: flex;
    height: 100vh;
    width: 100vw;
    margin: 0;
    padding: 0;
}

/* Sidebar avec fond vert */
.sidebar {
    width: 70px;
    background-color: #2da0a8; /* couleur verte */
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 15px 0;
    color: white;
    margin: 0;
    box-sizing: border-box;
}

.profile-section {
    text-align: center;
    margin-bottom: 40px;
}

.user_icon {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    border: 2px solid white;
    margin-bottom: 8px;
}

#name-sidebar {
    font-size: 14px;
    font-weight: 600;
}

/* Menu vertical */
.menu {
    list-style: none;
    padding: 0;
    margin: 0 auto;
    flex-grow: 1;
}

.menu-item {
    margin: 20px 0;
}

.menu-item a img {
    width: 32px;
    cursor: pointer;
    transition: transform 0.3s ease;
    filter: brightness(0) invert(1);
}

.menu-item.active a img,
.menu-item a:hover img {
    transform: scale(1.3);
}

/* Icône plus petite pour home (search) */
.icon-small {
    width: 22px !important;
    height: auto;
}

/* Logout */
.logout {
    margin-bottom: 20px;
}

.logout img {
    width: 28px;
    cursor: pointer;
    filter: brightness(0) invert(1);
}

/* Contact list à gauche */
.contact-list {
    width: 300px;
    border-right: 1px solid #ddd;
    padding: 20px;
    overflow-y: auto;
    background: #f7f9fc;
    margin: 0;
    box-sizing: border-box;
}

.contact-list h2 {
    font-weight: 700;
    font-size: 22px;
    margin-bottom: 15px;
    color: #333;
}

.users {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.user-card {
    background: white;
    border-radius: 10px;
    padding: 10px 15px;
    box-shadow: 0 0 5px rgb(0 0 0 / 0.1);
    display: flex;
    align-items: center;
    gap: 15px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.user-card:hover {
    background: #e2f0ff;
}

.user-card .avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    border: 2px solid #53A0FD;
}

/* Chat container */
.chat-container {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    padding: 25px;
    background: #fff;
    margin: 0;
    box-sizing: border-box;
    
}

/* Header chat */
.chat-header {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 15px;
    background-color: #2da0a8;
}

.chat-header #btnHome {
   background-color: #2da0a8;
    font-weight: 600;
    text-decoration: none;
    border: 2px solid #2da0a8;
    padding: 6px 12px;
    border-radius: 8px;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.chat-header #btnHome:hover {
    background-color: #2da0a8;
    color: white;
}

#chatWithName {
    font-weight: 700;
    font-size: 22px;
    background-color: #2da0a8;
    flex-grow: 1;
}

/* Messages box */
#chatBox {
    flex-grow: 1;
    overflow-y: auto;
    max-height: 400px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 10px;
    background: #fafafa;
    font-family: 'Poppins', sans-serif;
}

/* Message style */
.message {
    margin: 6px 0;
    padding: 8px 12px;
    border-radius: 12px;
    max-width: 70%;
    word-wrap: break-word;
}

.message.user {
    background-color: #2da0a8;
    color: white;
    align-self: flex-end;
    margin-left: auto;
}

.message.other {
    background-color: #e2f0ff;
    color: #222;
    align-self: flex-start;
    margin-right: auto;
}

/* Input area */
.chat-input {
    margin-top: 10px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
}

.emotion-selector {
    flex-shrink: 0;
}

.emotion-selector label {
    font-weight: bold;
    margin-right: 5px;
}

.emotion-selector select {
    padding: 6px;
    border-radius: 5px;
    font-size: 14px;
}

#toggleEmoji {
    cursor: pointer;
    font-size: 22px;
    border: none;
    background: none;
}

#messageInput {
    flex-grow: 1;
    padding: 8px 10px;
    font-size: 16px;
    border-radius: 10px;
    border: 1px solid #ccc;
    font-family: 'Poppins', sans-serif;
}

#sendBtn {
    padding: 8px 20px;
    background: #2da0a8;
    border: none;
    color: #fff;
    border-radius: 10px;
    cursor: pointer;
    font-weight: bold;
}

#sendBtn:hover {
    background: #238588;
}

#miniPicker {
    display: none;
    margin-top: 8px;
    width: 100%;
    gap: 10px;
    flex-wrap: wrap;
    user-select: none;
}

.mini-picker-emoji {
    cursor: pointer;
    font-size: 22px;
}

.mini-picker-emoji:hover {
    transform: scale(1.2);
    transition: transform 0.2s ease;
}
</style>

<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="profile-section">
            <img src="src/Content/img/profile.png" alt="profil" class="user_icon">
            <p id="name-sidebar" style="display:none;"><?= htmlspecialchars($_SESSION['id'] ?? 'Invité') ?></p>
        </div>

        <ul class="menu">
            <li class="menu-item active">
                <a href="index.php?controller=list&id=<?= urlencode($_SESSION['id']) ?>">
                    <img src="src/Content/img/home.png" alt="home" class="icon-small">
                </a>
            </li>
            <li class="menu-item">
                <a href="?controller=profil">
                    <img src="src/Content/img/settings.png" alt="settings">
                </a>
            </li>
        </ul>

        <div class="logout">
            <a href="?controller=login&action=logout">
                <img src="src/Content/img/logout.png" alt="logout">
            </a>
        </div>
    </div>

    <!-- Liste contacts -->
    <div class="contact-list">
        <h2>Contacts</h2>
        <div class="users">
            <?php foreach ($contacts as $row): ?>
                <?php if ($row['user_id'] !== $_SESSION['id']): ?>
                    <div class="user-card" onclick="openChatBox(<?= $row['user_id'] ?>, '<?= htmlspecialchars(addslashes($row['username'])) ?>')">
                        <img src="src/Content/img/profile.png" alt="<?= htmlspecialchars($row['username']) ?>" class="avatar">
                        <span><?= htmlspecialchars($row['username']) ?></span>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Boîte de dialogue -->
    <div class="chat-container">
        <header class="chat-header">
            <div id="chatWithName">Chat</div>
        </header>

        <div id="chatBox"></div>

        <div class="chat-input">
            <div class="emotion-selector">
                <label for="emotionSelect">Émotion :</label>
                <select id="emotionSelect" name="emotion" required>
                    <option value="" selected disabled hidden>Choisissez une émotion</option>
                    <option value="joie">😊 Joie</option>
                    <option value="colère">😠 Colère</option>
                    <option value="tristesse">😢 Tristesse</option>
                    <option value="surprise">😲 Surprise</option>
                    <option value="dégoût">🤢 Dégoût</option>
                    <option value="peur">😨 Peur</option>
                </select>
            </div>

            <button id="toggleEmoji" title="Ouvrir le sélecteur d'émojis">😃</button>

            <input type="text" id="messageInput" placeholder="Écris un message..." autocomplete="off" />

            <button id="sendBtn">Envoyer</button>

            <div id="miniPicker">
                <span class="mini-picker-emoji">😊</span>
                <span class="mini-picker-emoji">😂</span>
                <span class="mini-picker-emoji">😎</span>
                <span class="mini-picker-emoji">😢</span>
                <span class="mini-picker-emoji">😡</span>
            </div>
        </div>
    </div>
</div>

<script type="module">
import 'https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js';

let currentChatId = null;
let currentChatName = '';

// Messages stockés par contact (simulé)
let messages = {};

const chatBox = document.getElementById('chatBox');
const messageInput = document.getElementById('messageInput');
const sendBtn = document.getElementById('sendBtn');
const toggleEmoji = document.getElementById('toggleEmoji');
const miniPicker = document.getElementById('miniPicker');
const emotionSelect = document.getElementById('emotionSelect');
const chatWithName = document.getElementById('chatWithName');

function renderMessages() {
    chatBox.innerHTML = '';
    if (!currentChatId || !messages[currentChatId]) return;

    messages[currentChatId].forEach(msg => {
        const div = document.createElement('div');
        div.className = 'message ' + (msg.sender === 'user' ? 'user' : 'other');
        div.textContent = `[${msg.emotion}] ${msg.text}`;
        chatBox.appendChild(div);
    });
    chatBox.scrollTop = chatBox.scrollHeight;
}

function sendMessage() {
    const text = messageInput.value.trim();
    const emotion = emotionSelect.value;

    if (!currentChatId) {
        alert('Sélectionnez un contact.');
        return;
    }
    if (!text) {
        alert('Le message est vide.');
        messageInput.focus();
        return;
    }
    if (!emotion) {
        alert('Veuillez choisir une émotion.');
        emotionSelect.focus();
        return;
    }

    if (!messages[currentChatId]) messages[currentChatId] = [];
    messages[currentChatId].push({ sender: 'user', text, emotion });

    renderMessages();

    // Envoi WebSocket
    if (ws && ws.readyState === WebSocket.OPEN) {
        ws.send(JSON.stringify({
            content: text,
            emotion,
            recipient: currentChatId
        }));
    }

    messageInput.value = '';
    emotionSelect.value = '';
}

sendBtn.addEventListener('click', sendMessage);
messageInput.addEventListener('keypress', e => {
    if (e.key === 'Enter') sendMessage();
});

toggleEmoji.addEventListener('click', () => {
    miniPicker.style.display = miniPicker.style.display === 'flex' ? 'none' : 'flex';
});

document.querySelectorAll('.mini-picker-emoji').forEach(emoji => {
    emoji.addEventListener('click', () => {
        messageInput.value += emoji.textContent;
        messageInput.focus();
        miniPicker.style.display = 'none';
    });
});

function openChatBox(id, name) {
    currentChatId = id;
    currentChatName = name;
    chatWithName.textContent = `Chat avec ${name}`;

    if (!messages[currentChatId]) {
        messages[currentChatId] = [];
        // Simuler un message de bienvenue
        messages[currentChatId].push({ sender: 'other', text: "Salut ! Comment tu vas ?", emotion: 'joie' });
    }

    renderMessages();
    initWebSocket();
}

let ws;

function initWebSocket() {
    if (ws) {
        ws.close();
    }
    if (!currentChatId) return;

    ws = new WebSocket(`ws://${window.location.hostname}:8081/chat?id=${encodeURIComponent(currentChatId)}`);

    ws.onopen = () => {
        console.log('WebSocket connecté');
    };

    ws.onmessage = event => {
        try {
            const msg = JSON.parse(event.data);
            if (msg.content && msg.sender && msg.sender !== currentChatId) {
                if (!messages[currentChatId]) messages[currentChatId] = [];
                messages[currentChatId].push({ sender: 'other', text: msg.content, emotion: msg.emotion || 'aucune' });
                renderMessages();
            }
        } catch(e) {
            console.error('Erreur parsing WS message:', e);
        }
    };

    ws.onerror = e => {
        console.error('WebSocket error:', e);
    };

    ws.onclose = () => {
        console.log('WebSocket déconnecté. Reconnexion dans 5s...');
        setTimeout(initWebSocket, 5000);
    };
}

window.openChatBox = openChatBox;

// Optionnel : ouvrir premier contact automatiquement (si existant)
document.addEventListener('DOMContentLoaded', () => {
    const firstContact = document.querySelector('.user-card');
    if (firstContact) {
        firstContact.click();
    }
});
</script>

<?php require_once 'view_end.php'; ?>
