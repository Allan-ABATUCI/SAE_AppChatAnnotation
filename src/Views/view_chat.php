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
    // import emoji-picker 
    import 'https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js';

    const currentChatId = '<?php echo e($_GET['id']) ; ?>'; 

    let messages = {
      ContactUnique: [
        { sender: 'other', text: "Salut ! Comment tu vas ?" },
      ],
    };

    const chatBox = document.getElementById('chatBox');
    const chatWithName = document.getElementById('chatWithName');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const toggleEmoji = document.getElementById('toggleEmoji');
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

      // Vérifie que les deux champs sont remplis
      if (!text || !emotion) {
        if (!emotion) {
          emotionSelect.focus();
          alert("Veuillez sélectionner une émotion");
        }
        return;
      }
      
      if (!messages[currentChatId]) messages[currentChatId] = [];
      messages[currentChatId].push({ 
        sender: 'user', 
        text: `[${emotion}] ${text}` // Ajoute l'émotion au message
      });
      
      renderMessages();
      messageInput.value = '';
      emotionSelect.value = ''; // Réinitialise le select
      emojiPicker.style.display = 'none';
    }

    sendBtn.addEventListener('click', sendMessage);
    messageInput.addEventListener('keypress', e => { if (e.key === 'Enter') sendMessage(); });

    toggleEmoji.addEventListener('click', () => {
      miniPicker.style.display = miniPicker.style.display === 'flex' ? 'none' : 'flex';
    });

    emojiPicker.addEventListener('emoji-click', event => {
      messageInput.value += event.detail.unicode;
      messageInput.focus();
    });

    // Initialisation
    chatWithName.textContent = "<?php echo $username?>";
    renderMessages();

    window.onload = function() {
      currentRecipientId = "<?php echo $_GET['id'] ?? ''; ?>";
      
      if (currentRecipientId) {
        recipientInfo.textContent = "Discussion avec l'utilisateur ID: " + currentRecipientId;
        initierWebSocket();
      } else {
        recipientInfo.textContent = "Aucun ID de destinataire spécifié.";
      }
    };

    function initWebSocket() {
  // Adjust the WebSocket URL to match your Ratchet server
  ws = new WebSocket('ws://localhost:8081/chat&id='+currentUserId);

  ws.onopen = function() {
    console.log('WebSocket connection established');
  };

  ws.onmessage = function(event) {
    const message = JSON.parse(event.data);
    
    // Only process messages for this chat
    if (message.recipient_id === currentUserId || message.sender_id === recipientId) {
      if (!messages[currentChatId]) messages[currentChatId] = [];
      
      messages[currentChatId].push({
        sender: message.sender_id === currentUserId ? 'user' : 'other',
        text: `[${message.emotion}] ${message.text}`
      });
      
      renderMessages();
    }
  };

  ws.onclose = function() {
    console.log('WebSocket connection closed');
    // Attempt to reconnect after 5 seconds
    setTimeout(initWebSocket, 5000);
  };
}

// Modified sendMessage function for WebSocket
function sendMessage() {
  const text = messageInput.value.trim();
  const emotion = emotionSelect.value;

  if (!text || !emotion) {
    if (!emotion) {
      emotionSelect.focus();
      alert("Veuillez sélectionner une émotion");
    }
    return;
  }

  const messageData = {
    type: 'chat',
    sender_id: currentUserId,
    recipient_id: recipientId,
    chat_id: currentChatId,
    text: text,
    emotion: emotion,
    timestamp: new Date().toISOString()
  };

  if (ws && ws.readyState === WebSocket.OPEN) {
    ws.send(JSON.stringify(messageData));
    
    
    if (!messages[currentChatId]) messages[currentChatId] = [];
    messages[currentChatId].push({
      sender: 'user',
      text: `[${emotion}] ${text}`
    });
    
    renderMessages();
    messageInput.value = '';
    emotionSelect.value = '';
  } else {
    alert('Connection lost. Trying to reconnect...');
    initWebSocket();
  }
}

// Initialize WebSocket when page loads
document.addEventListener('DOMContentLoaded', initWebSocket);
  </script>
</body>
</html>