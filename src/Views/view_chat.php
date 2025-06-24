<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Chat - Messagerie</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/emoji-picker-element/1.6.2/emoji-picker-element.min.css">
  <link rel="stylesheet" href="content/css/chat.css" />
</head>
<body>
  <div class="chat-container">
    <div class="chat-header">Messagerie</div>

    <div class="chat-box" id="chatBox"></div>

    <div class="chat-input">
      <emoji-picker style="position: absolute; bottom: 70px; left: 10px; display: none;"></emoji-picker>
      <button id="toggleEmoji">😃</button>
      <input type="text" id="messageInput" placeholder="Message..." />
      <button onclick="sendMessage()">Envoyer</button>
    </div>
  </div>

  <script type="module">
    import 'https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js';

    const input = document.getElementById('messageInput');
    const chatBox = document.getElementById('chatBox');
    const emojiPicker = document.querySelector('emoji-picker');
    const toggleEmoji = document.getElementById('toggleEmoji');

    toggleEmoji.addEventListener('click', () => {
      emojiPicker.style.display = emojiPicker.style.display === 'none' ? 'block' : 'none';
    });

    emojiPicker.addEventListener('emoji-click', event => {
      input.value += event.detail.unicode;
    });

    function sendMessage() {
      const message = input.value.trim();
      if (!message) return;

      const userMessage = document.createElement('div');
      userMessage.className = 'message user';
      userMessage.innerText = message;
      chatBox.appendChild(userMessage);

      input.value = '';
      emojiPicker.style.display = 'none';
      chatBox.scrollTop = chatBox.scrollHeight;
    }

    input.addEventListener("keypress", function(e) {
      if (e.key === "Enter") {
        sendMessage();
      }
    });
  </script>
</body>
</html>
