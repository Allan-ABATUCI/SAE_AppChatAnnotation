<!DOCTYPE html>
<html>

<head>
    <title>Chat Annoté</title>
    <style>
        #chat-container {
            width: 400px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 10px;
        }

        #message-area {
            height: 300px;
            overflow-y: scroll;
            border: 1px solid #eee;
            padding: 5px;
            margin-bottom: 10px;
        }

        .message {
            margin-bottom: 5px;
            padding: 5px;
            border-radius: 5px;
        }

        .message.mine {
            background-color: #e0f2f7;
            /* Bleu clair */
            text-align: right;
        }

        .message.other {
            background-color: #f0f0f0;
            /* Gris clair */
            text-align: left;
        }

        #input-area {
            display: flex;
            flex-direction: column;
        }

        #emotion-buttons {
            display: flex;
            justify-content: space-around;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div id="chat-container">
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
        const messageArea = document.getElementById('message-area');
        const messageInput = document.getElementById('message-input');
        const sendButton = document.getElementById('send-button');
        const emotionButtons = document.getElementById('emotion-buttons').querySelectorAll('button');
        let selectedEmotion = null;

        emotionButtons.forEach(button => {
            button.addEventListener('click', () => {
                selectedEmotion = button.dataset.emotion;
            });
        });

        sendButton.addEventListener('click', () => {
            const messageText = messageInput.value;
            if (messageText.trim() === "" || selectedEmotion == null) return;

            displayMessage(messageText, "mine", selectedEmotion);
            messageInput.value = '';
            selectedEmotion = null;
        });

        function displayMessage(message, sender, emotion) {
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message');
            messageDiv.classList.add(sender);
            messageDiv.textContent = message + " (" + emotion + ")";
            messageArea.appendChild(messageDiv);
            messageArea.scrollTop = messageArea.scrollHeight;
        }
    </script>
    <script>
        // Vérifier si la page a reçu l'ID du destinataire via GET
        window.onload = function() {
            // Récupérer l'ID du destinataire depuis une variable GET (présumée envoyée du backend)
            const recipientId = "<?php echo $dest; ?>";

            // Afficher l'ID du destinataire sur la page
            if (recipientId) {
                document.getElementById("recipientInfo").textContent = "ID du destinataire : " + recipientId;
                initiateWebSocket(recipientId); // Initier la connexion WebSocket avec l'ID du destinataire
            } else {
                document.getElementById("recipientInfo").textContent = "Aucun ID de destinataire fourni.";
            }
        };

        var conn;

        // Fonction pour initier la connexion WebSocket avec l'ID du destinataire
        function initiateWebSocket(recipientId) {
            conn = new WebSocket('ws://localhost:8081/chat?userId=<?php echo $_SESSION['id'] ?? '' ?>' + '&dest=' + recipientId); // Ouvrir la connexion WebSocket avec l'ID utilisateur

            conn.onopen = function(e) {
                console.log("Connexion WebSocket établie !");
            };

            conn.onmessage = function(e) {
                console.log("Message reçu : " + e.data);
                document.getElementById("log").value += e.data + '\n'; // Ajouter le message reçu au journal
            };

            conn.onerror = function(e) {
                console.error("Erreur WebSocket :", e);
            };

            conn.onclose = function(e) {
                console.log("Connexion WebSocket fermée.");
            };
        }

        // Fonction pour envoyer un message au destinataire
        function sendMessage() {
            const message = document.getElementById("message").value; // Récupérer le message saisi

            // Si le message n'est pas vide
            if (message.trim() !== "") {
                const messageData = {
                    dest: "<?php echo $dest; ?>", // Récupérer l'ID du destinataire depuis PHP (GET)
                    message: message
                };

                // Envoyer le message via la connexion WebSocket
                conn.send(JSON.stringify(messageData));

                // Optionnellement, vider le champ après l'envoi du message
                document.getElementById("message").value = '';
            }
        }
    </script>
</body>

</html>