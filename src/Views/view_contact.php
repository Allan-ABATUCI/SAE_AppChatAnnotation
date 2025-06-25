<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contacts</title>
  <link rel="stylesheet" href="src/Content/css/contact.css" />

</head>
<body>
  <div class="container">
    <h1>Liste des Contacts</h1>
    <?php foreach ($contacts as $c => $row): ?>
      <?php if($row['user_id']!=$_SESSION['id']) :?>
        <div class="contact-card" data-contact="<?php echo $row['user_id'] ?? '' ?>">
          <div class="contact-name"><?php echo $row['username'] ?? '' ?></div>
        </div>
          
      <?php endif?> 
    <?php endforeach;?>
  <script>
    document.querySelectorAll('.contact-card').forEach(card => {
      card.addEventListener('click', () => {
        const contactId = card.getAttribute('data-contact');
        
        window.location.href = `?controller=chat&id=${contactId}`;
      });
    });
  </script>
</body>
</html>
