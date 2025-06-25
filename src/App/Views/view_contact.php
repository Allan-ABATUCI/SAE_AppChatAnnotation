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

/* Chat box à droite */
.chat-box {
    flex-grow: 1;
    padding: 25px;
    display: flex;
    flex-direction: column;
    background: #fff;
    margin: 0;
    box-sizing: border-box;
}

.chat-box h3 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #2da0a8; /* vert assorti */
}

.messages {
    flex-grow: 1;
    border: 1px solid #ccc;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
    overflow-y: auto;
    background: #f9f9f9;
    min-height: 250px;
}

.chat-box textarea {
    resize: none;
    padding: 12px;
    font-size: 16px;
    border-radius: 10px;
    border: 1px solid #ccc;
    margin-bottom: 15px;
    width: 100%;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

.chat-box button {
    background: #2da0a8; /* vert */
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    cursor: pointer;
    width: 140px;
    align-self: flex-end;
    transition: background 0.3s ease;
}

.chat-box button:hover {
    background: #238588;
}


</style>

<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="profile-section">
            <img src="src/App/Content/img/profile.png" alt="profil" class="user_icon">
            <p  id="name-sidebar" style="display:none;"><?= htmlspecialchars($_SESSION['id'] ?? 'Invité') ?></p>
        </div>

        <ul class="menu">
            <li class="menu-item active">
               <a href="index.php?controller=list&id=<?= urlencode($_SESSION['id']) ?>">
    <img src="src/App/Content/img/home.png" alt="home" class="icon-small">
</a>



            </li>
            
            <li class="menu-item">
                <a href="?controller=profil">
                    <img src="src/App/Content/img/settings.png" alt="settings">
                </a>
            </li>
        </ul>

        <div class="logout">
            <a href="?controller=login&action=logout">
                <img src="src/App/Content/img/logout.png" alt="logout">
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
                        <img src="src/App/Content/img/profile.png" alt="<?= htmlspecialchars($row['username']) ?>" class="avatar">
                        <span><?= htmlspecialchars($row['username']) ?></span>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Boîte de dialogue -->
    <div class="chat-box" id="chatbox">
        <p>Sélectionnez un contact pour démarrer une conversation.</p>
    </div>
</div>

<script>
function openChatBox(userId, username) {
    const chatbox = document.getElementById('chatbox');
    chatbox.innerHTML = `
        <h3>Conversation avec ${username}</h3>
        <div class="messages">
            <p><em>Zone messages (à développer)</em></p>
        </div>
        <textarea placeholder="Écrivez un message..." rows="4"></textarea>
        <button onclick="alert('Envoyer message à ${username} (à implémenter)')">Envoyer</button>
    `;
}
</script>

<?php require_once 'view_end.php'; ?>
