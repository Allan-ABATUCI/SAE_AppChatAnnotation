<!DOCTYPE html>
<html>
<header>
    <h1>Messagerie Instantanée</h1>
    <?php require_once 'view_begin.php'; ?>
    <link rel="stylesheet" href="src/App/Content/css/contact.css">
</header>
<main>
    <div class="contacts">
        
       
        <ul class="contact-list">
            <div class="search-bar">
            <input id="searchInput" type="text" placeholder="Rechercher un utilisateur...">
            <?php foreach ($contacts as $c => $row): ?>
                <?php if($row['user_id']!=$_SESSION['id']) :?>
                <li class="contact-item">
                    <div class="contact-info">
                        <a class="contact-name" href="?controller=chat&id=<?php echo $row["user_id"]?>">
                        <?= htmlspecialchars($row['username']) ?>
                    </a>
                    </div>
                </li>
                <?php endif?>
            <?php endforeach; ?>
        </ul>
    </div>
</main>
<?php require_once 'view_end.php'; ?>