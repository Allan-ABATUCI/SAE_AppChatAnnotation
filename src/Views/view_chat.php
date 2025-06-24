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
  <div class="app-container">
    <nav class="contact-list" id="contactList">
      <h3>Contacts</h3>
      <ul id="contactsUl"></ul>
    </nav>

    <section class="chat-container">
      <header class="chat-header">
        <button id="btnHome" title="Retour à l'accueil">Accueil</button>
        <div id="chatWithName">Bienvenue</div>
      </header>

      <div class="chat-box" id="chatBox"></div>

      <div class="chat-input">
        <button id="toggleEmoji" title="Ouvrir le sélecteur d'émojis">😃</button>
        <input type="text" id="messageInput" placeholder="Écris un message..." autocomplete="off" />
        <button id="sendBtn">Envoyer</button>
        <emoji-picker style="display:none;"></emoji-picker>
      </div>
    </section>
  </div>

  <script type="module">
    import 'https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js';

    // Données contacts et messages (simples)
    const contacts = [
      { id: 'Allan', name: 'Allan' },
      { id: 'Bradley', name: 'Bradley' },
      { id: 'Mouhammed', name: 'Mouhammed' },
      { id: 'Karamba', name: 'Karamba' },
      { id: 'Vakisan', name: 'Vakisan' }
    ];

    // Messages par contact (tableau de messages {sender, text})
    const messages = {
      Allan: [{ sender: 'other', text: "Salut ! Comment tu vas ?" }],
    };

    // Elements DOM
    const contactsUl = document.getElementById('contactsUl');
    const chatBox = document.getElementById('chatBox');
    const chatWithName = document.getElementById('chatWithName');
    const btnHome = document.getElementById('btnHome');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const emojiPicker = document.querySelector('emoji-picker');
    const toggleEmoji = document.getElementById('toggleEmoji');
    const contactList = document.getElementById('contactList');

    let currentChatId = null; // null = vue accueil (liste complète)

    // Affiche la liste des contacts dans le menu (avec click)
    function renderContacts() {
      contactsUl.innerHTML = '';
      contacts.forEach(c => {
        const li = document.createElement('li');
        li.textContent = c.name;
        li.dataset.id = c.id;
        li.classList.toggle('active', c.id === currentChatId);
        li.addEventListener('click', () => {
          openChat(c.id);
        });
        contactsUl.appendChild(li);
      });
    }

    // Affiche le chat d'un contact
    function openChat(id) {
      currentChatId = id;
      renderContacts();
      contactList.classList.remove('hidden');
      chatWithName.textContent = contacts.find(c => c.id === id)?.name || 'Inconnu';
      renderMessages();
    }

    // Affiche tous les messages pour le chat actif
    function renderMessages() {
      chatBox.innerHTML = '';
      if (!currentChatId) {
        chatWithName.textContent = "Bienvenue";
        return;
      }
      const msgs = messages[currentChatId] || [];
      msgs.forEach(msg => {
        const div = document.createElement('div');
        div.className = 'message ' + (msg.sender === 'user' ? 'user' : 'other');
        div.innerText = msg.text;
        chatBox.appendChild(div);
      });
      chatBox.scrollTop = chatBox.scrollHeight;
    }

    // Envoie un message pour le chat actif
    function sendMessage() {
      if (!currentChatId) return alert("Choisis un contact avant d'envoyer un message !");
      const text = messageInput.value.trim();
      if (!text) return;
      if (!messages[currentChatId]) messages[currentChatId] = [];
      messages[currentChatId].push({ sender: 'user', text });
      renderMessages();
      messageInput.value = '';
      emojiPicker.style.display = 'none';
    }

    // Bouton accueil : affiche la liste des contacts, cache le chat
    btnHome.addEventListener('click', () => {
      currentChatId = null;
      chatWithName.textContent = 'Bienvenue';
      chatBox.innerHTML = `<p style="text-align:center; padding:20px; color:#bbb;">
        Sélectionne un contact pour commencer à discuter.
      </p>`;
      contactList.classList.remove('hidden');
      renderContacts();
    });

    sendBtn.addEventListener('click', sendMessage);

    messageInput.addEventListener('keypress', e => {
      if (e.key === 'Enter') sendMessage();
    });

    // Toggle emoji picker
    toggleEmoji.addEventListener('click', () => {
      if (emojiPicker.style.display === 'none') {
        emojiPicker.style.display = 'block';
      } else {
        emojiPicker.style.display = 'none';
      }
    });

    // Insère l'émoji choisi dans l'input
    emojiPicker.addEventListener('emoji-click', event => {
      messageInput.value += event.detail.unicode;
      messageInput.focus();
    });

    // Initialisation
    renderContacts();
    btnHome.click();
  </script>
</body>
</html>
