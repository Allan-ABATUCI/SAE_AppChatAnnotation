<!DOCTYPE html>
<head>
    <title>Chat Annoté</title>
    <style>
        /* [Vos styles CSS existants restent identiques] */
    </style>
</head>

<body>
    <div id="chat-container">
        <div id="recipient-info"></div>
        <div id="message-area"></div>
        <div id="input-area">
            <textarea id="message-input" placeholder="Écrivez votre message..."></textarea>
            <div id="emotion-buttons">
                <button data-emotion="joie">Joie</button>
                <button data-emotion="colère">Colère</button>
                <button data-emotion="tristesse">Tristesse</button>
            </div>
            <button id="send-button">Envoyer</button>
        </div>
    </div>

    <script>
        // Éléments de l'interface
        const messageArea = document.getElementById('message-area');
        const messageInput = document.getElementById('message-input');
        const sendButton = document.getElementById('send-button');
        const emotionButtons = document.getElementById('emotion-buttons').querySelectorAll('button');
        const recipientInfo = document.getElementById('recipient-info');
        
        // Variables d'état
        let selectedEmotion = null;
        let conn;
        let currentRecipientId = null;
        let currentUserId = "<?php echo $_SESSION['user']['id'] ?? ''; ?>";

        // Initialisation du chat
        window.onload = function() {
            currentRecipientId = "<?php echo $_GET['user_id'] ?? ''; ?>";
            
            if (currentRecipientId) {
                recipientInfo.textContent = "Discussion avec l'utilisateur ID: " + currentRecipientId;
                initierWebSocket();
            } else {
                recipientInfo.textContent = "Aucun ID de destinataire spécifié.";
            }
        };

        // Gestion des boutons d'émotion
        emotionButtons.forEach(bouton => {
            bouton.addEventListener('click', () => {
                emotionButtons.forEach(btn => btn.classList.remove('selected'));
                bouton.classList.add('selected');
                selectedEmotion = bouton.dataset.emotion;
            });
        });

        // Envoi du message
        sendButton.addEventListener('click', envoyerMessage);
        
        messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                envoyerMessage();
            }
        });

        async function envoyerMessage() {
            const texteMessage = messageInput.value.trim();
            if (texteMessage === "" || selectedEmotion == null) {
                alert("Veuillez écrire un message et sélectionner une émotion");
                return;
            }

            // Afficher le message localement
            afficherMessage(texteMessage, "mine", selectedEmotion, 'Vous', new Date());
            
            // 1. Envoyer via WebSocket pour le chat en temps réel
            if (conn && conn.readyState === WebSocket.OPEN) {
                const donneesMessage = {
                    content: texteMessage,
                    recipient: currentRecipientId
                    // Note: L'émotion n'est pas envoyée via WS si votre serveur ne la gère pas
                };
                conn.send(JSON.stringify(donneesMessage));
            }

            // 2. Sauvegarder dans la base de données via AJAX
            try {
                const response = await fetch('?controller=chat&action=save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        sender_id: currentUserId,
                        recipient_id: currentRecipientId,
                        content: texteMessage,
                        emotion: selectedEmotion
                    })
                });

                if (!response.ok) {
                    console.error("Erreur lors de la sauvegarde du message");
                }
            } catch (error) {
                console.error("Erreur AJAX:", error);
            }

            // Réinitialiser le champ de saisie
            messageInput.value = '';
            emotionButtons.forEach(btn => btn.classList.remove('selected'));
            selectedEmotion = null;
        }

        function afficherMessage(message, expediteur, emotion, nomExpediteur, date) {
            const divMessage = document.createElement('div');
            divMessage.classList.add('message');
            divMessage.classList.add(expediteur);
            
            const divInfos = document.createElement('div');
            divInfos.classList.add('message-info');
            divInfos.textContent = `${nomExpediteur} - ${date.toLocaleTimeString()}`;
            
            const divContenu = document.createElement('div');
            divContenu.innerHTML = `
    <p>${message} (${emotion})</p>
    <div class="reactions">
        <button class="emoji-btn" data-emoji="❤️">❤️</button>
        <button class="emoji-btn" data-emoji="😂">😂</button>
        <button class="emoji-btn" data-emoji="👍">👍</button>
        <button class="emoji-btn" data-emoji="😡">😡</button>
        <div class="current-reactions" id="reaction-${Date.now()}"></div>
    </div>
`;

            
            divMessage.appendChild(divInfos);
            divMessage.appendChild(divContenu);
            messageArea.appendChild(divMessage);
            messageArea.scrollTop = messageArea.scrollHeight;
        }

        function initierWebSocket() {
            conn = new WebSocket('ws://' + window.location.hostname + ':8080');
            
            conn.onopen = function(e) {
                console.log("Connexion WebSocket établie !");
            };

            conn.onmessage = function(e) {
                try {
                    const donnees = JSON.parse(e.data);
                    console.log("Message reçu:", donnees);
                    
                    if (donnees.content && donnees.sender) {
                        afficherMessage(
                            donnees.content, 
                            "other", 
                            'aucune', // Ou récupérer l'émotion si envoyée par le serveur
                            donnees.senderName || 'Inconnu', 
                            new Date()
                        );
                    }
                } catch (erreur) {
                    console.error("Erreur d'analyse du message:", erreur);
                }
            };

            conn.onerror = function(e) {
                console.error("Erreur WebSocket :", e);
            };

            conn.onclose = function(e) {
                console.log("Connexion WebSocket fermée.");
            };
        }
    </script>
</body>
</html>
