<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contacts</title>
</head>
<body>
  <div class="container">
    <h1>Liste des Contacts</h1>

    <div class="contact-card" data-contact="Alice Dupont">
      <div class="contact-name">Allan</div>
      <div class="contact-info">
        <div><span class="icon">📧</span> mail@email.com</div>
        <div><span class="icon">📞</span> +33 6 12 34 56 78</div>
      </div>
    </div>

    <div class="contact-card" data-contact="Bob Martin">
      <div class="contact-name">Bradley</div>
      <div class="contact-info">
        <div><span class="icon">📧</span> mail@email.com</div>
        <div><span class="icon">📞</span> +33 6 98 76 54 32</div>
      </div>
    </div>

    <div class="contact-card" data-contact="Charlie Lambert">
      <div class="contact-name">Mouhammed</div>
      <div class="contact-info">
        <div><span class="icon">📧</span> mail@email.com</div>
        <div><span class="icon">📞</span> +33 7 11 22 33 44</div>
      </div>
    </div>

    <div class="contact-card" data-contact="Diana Moreau">
      <div class="contact-name">Karanba</div>
      <div class="contact-info">
        <div><span class="icon">📧</span> mail@email.com</div>
        <div><span class="icon">📞</span> +33 6 44 55 66 77</div>
      </div>
    </div>

  </div>

  <script>
    document.querySelectorAll('.contact-card').forEach(card => {
      card.addEventListener('click', () => {
        const contactName = card.getAttribute('data-contact');
        const encodedName = encodeURIComponent(contactName);
        window.location.href = `chat.html?contact=${encodedName}`;
      });
    });
  </script>
</body>
</html>
