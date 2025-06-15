const urlWebSocket = "ws://localhost:8081/chat";
let socketWebSocket;

/**
 * Fonction pour établir la connexion au serveur WebSocket
 */
function connecterWebSocket() {
  // Création de la nouvelle connexion WebSocket
  socketWebSocket = new WebSocket(urlWebSocket);
  // on vérifie qu'on est pas déjà conencter

  // Événement déclenché lors de l'ouverture de la connexion
  socketWebSocket.onopen = () => {
    console.log("Connexion au serveur WebSocket établie");
  };

  // Événement pour la réception des messages
  socketWebSocket.onmessage = (evenement) => {
    try {
      const donnees = JSON.parse(evenement.data);

      if (donnees.erreur) {
        console.error("Erreur du serveur :", donnees.erreur);
      } else {
        console.log("Message reçu :", donnees);
        // Traitez ici le message reçu (affichage, notification, etc.)
        afficherMessage(donnees.expediteur, donnees.message);
      }
    } catch (e) {
      console.error("Erreur d'analyse du message JSON :", e);
    }
  };

  // Événement de fermeture de connexion
  socketWebSocket.onclose = () => {
    console.log("Déconnecté du serveur WebSocket");
  };

  // Gestion des erreurs
  socketWebSocket.onerror = (erreur) => {
    console.error("Erreur WebSocket :", erreur);
  };
}

/**
 * Fonction pour envoyer un message privé
 * @param {string} idUtilisateur - ID du destinataire
 * @param {string} message - Contenu du message
 */
function envoyerMessagePrive(idUtilisateur, message) {
  if (socketWebSocket && socketWebSocket.readyState === WebSocket.OPEN) {
    const donnees = {
      utilisateurCible: idUtilisateur,
      message: message,
      horodatage: new Date().toISOString(), // Ajout d'un timestamp
    };
    socketWebSocket.send(JSON.stringify(donnees));
    console.log(`Message envoyé à ${idUtilisateur} : ${message}`);
  } else {
    console.error("La connexion WebSocket n'est pas active");
    // Optionnel : Mettre en file d'attente
  }
}

/**
 * Fonction pour afficher un message dans l'interface
 * @param {string} expediteur - ID de l'expéditeur
 * @param {string} message - Contenu du message
 */
function afficherMessage(expediteur, message) {
  const zoneChat = document.getElementById("zone-chat");
  if (zoneChat) {
    zoneChat.innerHTML += `<div class="message"><strong>${expediteur}:</strong> ${message}</div>`;
    zoneChat.scrollTop = zoneChat.scrollHeight; // Défilement automatique
  }
}

connecterWebSocket();
