<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Connexion / Inscription</title>
  <?php require_once 'view_begin.php'; ?>

  <!-- FontAwesome pour les icônes -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>

  <!-- Le CSS style_login.css -->
  <link rel="stylesheet"
        href="src/Content/css/style_login.css"/>
</head>
<body>
  <div class="container" id="container">

    <!-- Formulaire Connexion -->
    <div class="form-container sign-in">
      <form method="post" action="?controller=login&action=login">
        <h1>Connexion</h1>
        <div class="social-icons">
          <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
          <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
          <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
        </div>
        <span>ou utilisez votre adresse mail</span>

        <?php if (isset($login_error_message)) : ?>
          <div style="color: red; margin-bottom: 10px;">
            <?= htmlspecialchars($login_error_message) ?>
          </div>
        <?php endif; ?>

        <input type="email" name="email" placeholder="Email" required/>
        <input type="password" name="mdp" placeholder="Mot de passe" required/>
        <a href="#">Mot de passe oublié ?</a>
        <button type="submit" name="submit_login">Se connecter</button>
      </form>
    </div>

    <!-- Formulaire Inscription -->
    <div class="form-container sign-up">
      <form method="post" action="?controller=login&action=register">
        <h1>Inscription</h1>

        <?php if (isset($register_error_message)) : ?>
          <div style="color: red; margin-bottom: 10px;">
            <?= htmlspecialchars($register_error_message) ?>
          </div>
        <?php endif; ?>

        <input type="text" name="prenom" placeholder="Prénom" required />
        <input type="text" name="nom" placeholder="Nom" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="mdp" placeholder="Mot de passe" required />
        <button type="submit" name="submit_registration">S'inscrire</button>
      </form>
    </div>

    <!-- Panels toggle -->
    <div class="toggle-container">
      <div class="toggle">
        <div class="toggle-panel toggle-left">
          <h1>Content de te revoir !</h1>
          <p id="new">Connecte-toi pour retrouver tes amis et discuter.</p>
          <button class="hidden" id="login">Connexion</button>
        </div>
        <div class="toggle-panel toggle-right">
          <h1 id="new">Bienvenue parmi nous !</h1>
          <p id="new">Créez un compte pour commencer à chatter avec vos amis.</p>
          <button class="hidden" id="register">Inscription</button>
        </div>
      </div>
    </div>

  </div>

  <script>
    const container = document.getElementById("container");
    const registerBtn = document.getElementById("register");
    const loginBtn = document.getElementById("login");

    registerBtn.addEventListener("click", () => container.classList.add("active"));
    loginBtn.addEventListener("click", () => container.classList.remove("active"));

    // Si tu veux gérer un switch automatique selon un paramètre PHP (ex: erreur inscription)
    <?php if (isset($register_error_message)) : ?>
      container.classList.add("active");
    <?php endif; ?>
  </script>
</body>
</html>
