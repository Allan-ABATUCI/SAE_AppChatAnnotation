<!DOCTYPE html>
<html lang="en">

<head>
	<title>Login</title>
	<?php require_once 'view_begin.php'; ?>
</head>

<body>

	<div class="limiter">
		<div class="container-login100" style="background-image: url('src/App/Content/img/bg-01.jpg');">
			<div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
				<form class="login100-form validate-form" method="post" action='?controller=login&action=login'>
					<span class="login100-form-title p-b-49">
						Connexion
					</span>

					<div class="wrap-input100 validate-input m-b-23" data-validate="email is required">
						<span class="label-input100">Email</span>
						<input class="input100" type="mail" name="email" placeholder="Entrez votre adresse e-mail">
						<span class="focus-input100" data-symbol="&#xf206;"></span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Password is required">
						<span class="label-input100">Mot de passe</span>
						<input class="input100" type="password" name="mdp" placeholder="Entrez votre mot de passe">
						<span class="focus-input100" data-symbol="&#xf190;"></span>
					</div>

					<div class="text-right p-t-8 p-b-31">

					</div>

					<div class="container-login100-form-btn">
						<div class="wrap-login100-form-btn">
							<div class="login100-form-bgbtn"></div>
							<button class="login100-form-btn" name="submit_login">
								Connexion
							</button>
						</div>
					</div>

					<div class="txt1 text-center p-t-54 p-b-20">
						<span color="black">
							Vous n'avez pas encore de compte ? <a href="?controller=login&action=form_register">Inscrivez-vous ici pour commencer
								! </a>
						</span>
					</div>
				</form>
			</div>
		</div>
	</div>


	<div id="dropDownSelect1"></div>

	<?php require_once 'view_end.php'; ?>