<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Messagerie</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/emoji-picker-element/1.6.2/emoji-picker-element.min.css" />

</head>
<body>
  <div class="chat-container">
    <header class="chat-header">
      <a href="/contacts.html" id="btnHome" title="Retour aux contacts">Contacts</a>
      <div id="chatWithName">Chat</div>
    </header>

    <div class="chat-box" id="chatBox"></div>

    <div class="chat-input">
      <button id="toggleEmoji" title="Ouvrir le sélecteur d'émojis">😃</button>
      <input type="text" id="messageInput" placeholder="Écris un message..." autocomplete="off" />
      <button id="sendBtn">Envoyer</button>
      <emoji-picker style="display:none;"></emoji-picker>
    </div>
  </div>

  <script type="module">
    import 'https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js';

    const currentChatId = 'ContactUnique'; 



    // Elements DOM
    const chatBox = document.getElementById('chatBox');
    const chatWithName = document.getElementById('chatWithName');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const emojiPicker = document.querySelector('emoji-picker');
    const toggleEmoji = document.getElementById('toggleEmoji');

    // Affiche tous les messages
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

    // Envoie un message
    function sendMessage() {
      const text = messageInput.value.trim();
      if (!text) return;
      if (!messages[currentChatId]) messages[currentChatId] = [];
      messages[currentChatId].push({ sender: 'user', text });
      renderMessages();
      messageInput.value = '';
      emojiPicker.style.display = 'none';
    }

    sendBtn.addEventListener('click', sendMessage);

    messageInput.addEventListener('keypress', e => {
      if (e.key === 'Enter') sendMessage();
    });

    toggleEmoji.addEventListener('click', () => {
      emojiPicker.style.display = emojiPicker.style.display === 'none' ? 'block' : 'none';
    });

    emojiPicker.addEventListener('emoji-click', event => {
      messageInput.value += event.detail.unicode;
      messageInput.focus();
    });

    // Initialisation
    chatWithName.textContent = "ContactUnique";
    renderMessages();
  </script>
</body>
</html>
