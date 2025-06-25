<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Messagerie</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/emoji-picker-element/1.6.2/emoji-picker-element.min.css" />
  <link rel="stylesheet" href="src/Content/css/chat.css" />
</head>
<body>
  <div class="chat-container">
    <header class="chat-header">
      <a href="?controller=list" id="btnHome" title="Retour aux contacts">Contacts</a>
      <div id="chatWithName">Chat</div>
    </header>

    <div class="chat-box" id="chatBox"></div>

    <div class="chat-input">
      <div class="emotion-selector">
        <label for="emotionSelect">Émotion:</label>
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
      <!-- Mini-picker avec 5 emojis -->
      <div id="miniPicker">
        <span class="mini-picker-emoji">😊</span>
        <span class="mini-picker-emoji">😂</span>
        <span class="mini-picker-emoji">😎</span>
        <span class="mini-picker-emoji">😢</span>
        <span class="mini-picker-emoji">😡</span>
      </div>
    </div>
  </div>

<script type="module">
import 'https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js';

const currentChatId = '<?php echo e($_GET['id']) ; ?>';
let messages = { ContactUnique: [{ sender: 'other', text: "Salut ! Comment tu vas ?" }] };

const chatBox = document.getElementById('chatBox');
const chatWithName = document.getElementById('chatWithName');
const messageInput = document.getElementById('messageInput');
const sendBtn = document.getElementById('sendBtn');
const toggleEmoji = document.getElementById('toggleEmoji');
const miniPicker = document.getElementById('miniPicker');
const emotionSelect = document.getElementById('emotionSelect');

function renderMessages() {
  chatBox.innerHTML = '';
  const msgs = messages[currentChatId] || [];
  msgs.forEach(msg => {
    const div = document.createElement('div');
    div.className = 'message ' + (msg.sender === 'user' ? 'user' : 'other');
    div.innerText = msg.text;
    chatBox.appendChild(div);
  });
  chatBox.scrollTop = chatBox.scrollHeight;
}

function sendMessage() {
  const text = messageInput.value.trim();
  const emotion = emotionSelect.value;
  if (!text || !emotion) {
    if (!emotion) emotionSelect.focus();
    return;
  }
  const full = `[${emotion}] ${text}`;
  messages[currentChatId] = messages[currentChatId] || [];
  messages[currentChatId].push({ sender: 'user', text: full });
  renderMessages();
  messageInput.value = '';
  emotionSelect.value = '';
  if (ws && ws.readyState === WebSocket.OPEN) {
    ws.send(JSON.stringify({ content: text, emotion, recipient: currentChatId }));
  }
}

sendBtn.addEventListener('click', sendMessage);
messageInput.addEventListener('keypress', e => { if (e.key === 'Enter') sendMessage(); });

toggleEmoji.addEventListener('click', () => {
  miniPicker.style.display = miniPicker.style.display === 'flex' ? 'none' : 'flex';
});

document.querySelectorAll('.mini-picker-emoji').forEach(e => {
  e.addEventListener('click', () => {
    messageInput.value += e.textContent;
    messageInput.focus();
    miniPicker.style.display = 'none';
  });
});

chatWithName.textContent = currentChatId || 'Chat';
renderMessages();

let ws;
function initWebSocket() {
  ws = new WebSocket('ws://' + window.location.hostname + ':8081/chat?id=' + encodeURIComponent(currentChatId));

  ws.onopen = function() {
    console.log('WebSocket connecté.');
  };

  ws.onmessage = function(event) {
    const msg = JSON.parse(event.data);
    if (msg.content && msg.sender && msg.sender !== currentChatId) {
      messages[currentChatId] = messages[currentChatId] || [];
      messages[currentChatId].push({ sender: 'other', text: `[${msg.emotion || 'aucune'}] ${msg.content}` });
      renderMessages();
    }
  };

  ws.onerror = function(e) {
    console.error('Erreur WebSocket :', e);
  };

  ws.onclose = function() {
    console.log('WebSocket déconnecté. Tentative de reconnexion dans 5s');
    setTimeout(initWebSocket, 5000);
  };
}

window.onload = function() {
  if (currentChatId) initWebSocket();
};
</script>
</body>
</html>
