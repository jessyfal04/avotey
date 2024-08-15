<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="lib/bulma.min.css">
	<link rel="stylesheet" href="lib/style.css">
	<script src="lib/jquery.min.js"></script>
	<script src="lib/script.js"></script>
	<script src="lib/jsencrypt.min.js"></script>
	<script type="module" src="lib/notificationModule.js"></script>
	
	<link rel="shortcut icon" href="lib/icon.png" type="image/x-icon">
	<title>AVotey</title>
</head>
<body>
	<!-- Ouverture de session -->
	<?php
		session_start();
		$estConnecte = isset($_SESSION['uuid']);
	?>

	<!-- Hero de bienvenu -->
	<section class="hero is-info">
		<div class="hero-body">
			<p class="title">
			Bienvenue sur AVotey
			</p>
			<p class="subtitle">
			Une plateforme de scrutins sécurisés
			</p>
		</div>
	</section>

	<br>

	<div id="messages" style="position:sticky; top:0px; z-index:10;"></div>
	<br>

	<!-- Block de connexion -->
	<div class='box mx-6'>
		<h2 class="title is-2">Compte</h2>

		<!-- Texte de bienvenue / de demande de connection -->
		<p class='is-size-5 if-login' <?php if(!$estConnecte) {echo "style='display:none;'";} ?>>Bonjour <strong id="txt-connexion-uuid"><?php if($estConnecte) {echo $_SESSION['uuid'];} ?></strong></p>
		<p class='is-size-5 if-not-login' <?php if($estConnecte) {echo "style='display:none;'";} ?>>Veuillez vous connecter.</p>

		<br>

		<!-- Bouton de déconnexion -->
		<div class="block">
			<button class="button is-danger is-medium if-login" onclick="logout()" <?php if(!$estConnecte) {echo "style='display:none;'";} ?>>Se déconnecter</button>
		</div>

		<!-- Boutons de notifications -->
		<div class="block">
			<button class="button is-info is-medium if-login" id="activerNotifs" onclick="activerNotifications()" <?php if(!$estConnecte) {echo "style='display:none;'";} ?>>Activer Notifications</button>
			<button class="button is-warning is-medium if-login" onclick="desactiverNotifications()" <?php if(!$estConnecte) {echo "style='display:none;'";} ?>>Désactiver Notifications</button>
		</div>
		

		<!-- Formulaire de connexion / inscription -->
		<div class="if-not-login" <?php if($estConnecte) {echo "style='display:none;'";} ?>>
			<div class="field">
				<label class="label" for="email">Email : </label>
				<div class="control">
					<input class="input" type="email" name="email" id="email" placeholder="abc@xyz.fr" onchange="emailExists()" required autocomplete>
				</div>
				<p class="help">Email valide</p>
			</div>

			<div class="field">
				<label class="label" for="password">Password : </label>
				<div class="control">
					<input class="input" type="password" name="password" id="password" minlength="8" placeholder="******" required>
				</div>
				<p class="help">Minimum 8 caractères</p>
			</div>

			<div class="field is-grouped">
				<div class="control mr-1">
					<button class="button is-link is-medium" type="button" onclick="login()" id="loginButton" disabled>Login</button>
				</div>
				<div class="control">
					<button class="button is-link is-medium" type="button" onclick="register()" id="registerButton" disabled>Register</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Blocs de scrutins -->
	<div class='box mx-6'>
		<h2 class="title is-2">Scrutins</h2>

		<p class='is-size-5 if-not-login' id="txt-scrutins-notConnected" <?php if($estConnecte) {echo "style='display:none;'";} ?>>Vous devez être connecté pour intéragir avec les scrutins</p>

		<div class="buttons block if-login" id="scrutinsButtons" <?php if(!$estConnecte) {echo "style='display:none;'";} ?>>
			<button class="button is-link is-rounded is-medium is-outlined mr-1" onclick="seeScrutins('gererListes')" id="button-gererListes">Gérer mes listes</button>
			<button class="button is-link is-rounded is-medium is-outlined mr-1" onclick="seeScrutins('createScrutin')" id="button-createScrutin">Créer un scrutin</button>
			<button class="button is-link is-rounded is-medium is-outlined mr-1" onclick="seeScrutins('consulterScrutins')" id="button-consulterScrutins">Voir mes scrutins</button>

			<button class="button is-info is-rounded is-medium" id="button-voterScrutin" style="display:none;" disabled>Voter pour un structin</button>
			<button class="button is-info is-rounded is-medium" id="button-resultatsScrutin" style="display:none;" disabled>Résultat pour un structin</button>
		</div>

		<div class="block" id="scrutins">

			<div id="gererListes" style="display:none;">
				<!-- <h5 class="title is-5">Gérer mes listes</h5> -->
				<div id="listeListes" ></div>
				<div class="field">
					<button class="button is-success is-light" type="button" onclick="addListGerer()" id="listeButton">Ajouter une liste</button>
				</div>
			</div>

			<div id="createScrutin" style="display:none;">
				<!-- <h5 class="title is-5">Créer un scrutin</h5> -->
				<div class="field">
					<label class="label" for="question">Question : </label>
					<div class="control">
						<input class="input" type="text" name="question" id="question" placeholder="Question ... ?">
					</div>
					<p class="help">Cette question sera posée aux votants.</p>
				</div>

				<div class="field">
					<label class="label" for="question">Système de vote : </label>
					<div class="select is-rounded">
						<select id="systemeVote">
							<option value="uninominal" default>Uninominal</option>
							<option value="jugementMajoritaire">Jugement Majoritaire</option>
						</select>
					</div>
				</div>

				<div class="field">
					<label class="label" for="question">Choix : </label>
					<div id="choicesList"></div>
					<p class="help">Ces choix seront proposés aux votants.</p>
					<button class="button is-success is-light" type="button" onclick="addChoice()">Ajouter un choix</button>
				</div>

				<div class="field">
					<label class="label" for="listChoicesChoix">Choix prédéfinis : </label>
					<button class="button is-warning is-light" type="button" onclick="addChoicesFromList()">Ajouter depuis une liste</button>
					<div class="select is-rounded">
						<select id="listChoicesChoix">
							<option value="" default>...</option>
							<option value="Binaire">Binaire</option>
						</select>
					</div>
				</div>

				<div class="field">
					<label class="label" for="question">Votants : </label>
					<div id="votersList"></div>
					<p class="help">Ces votants pourront voter au scrutin. Maximum 2 procurations.</p>
					<button class="button is-success is-light" type="button" onclick="addVoter()">Ajouter un votant</button>
				</div>

				<div class="field">
					<label class="label" for="question">Liste de votants : </label>
					<button class="button is-warning is-light" type="button" onclick="addVotersFromList()">Ajouter depuis une liste</button>
					<div class="select is-rounded">
						<select id="listChoices">
						</select>
					</div>
				</div>
				
				<div class="field">
					<button class="button is-success is-medium" type="button" onclick="create()" id="createButton">Créer le scrutin</button>
				</div>
			</div>

			<div id="consulterScrutins" style="display:none;">
				<!-- <h5 class="title is-5">Consulter mes scrutins</h5> -->
				<div class="block" onclick="consulter();">
					<h5 class="title is-5">Filtres : </h5>
					<span class="tag is-medium is-info">
						<label class="checkbox">
							<input type="checkbox" id="filterVotable"/>
							Votable
						</label>
					</span>
					<span class="tag is-medium is-success">
						<label class="checkbox">
							<input type="checkbox" id="filterResultats"/>
							Résultats
						</label>
					</span>
					<span class="tag is-medium is-link">
						<label class="checkbox">
							<input type="checkbox" id="filterCreateur"/>
							Créateur
						</label>
					</span>
				</div>

				<div class="block table-container">
					<h5 class="title is-5">Liste : </h3>
					<table class="table is-hoverable is-fullwidth">
						<thead>
							<tr>
								<th>Organisateur</th>
								<th>Question</th>
								<th>Infos</th>
								<th>Droits</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody id="scrutinsList">

						</tbody>
					</table>
				</div>
			</div>

			<div id="voterScrutin" style="display:none;">
				<!-- <h5 class="title is-5">Voter pour un scrutin</h5> -->
					
				<div class="field" style="">
					<label class="label" for="idScrutin">N° Scrutin </label>
					<div class="control">
						<input class="input" type="text" name="idScrutin" id="idScrutin" placeholder="Veuillez passer par 'Voir mes scrutins' pour pouvoir voter" disabled>
					</div>
					<p class="help">
						Ceci représente le numéro du scrutin au quel vous allez voter.
					</p>
				</div>

				<div class="field" style="display:none;">
					<label class="label" for="systemeVoteScrutin">Système de Vote</label>
					<div class="control">
						<input class="input" type="text" id="systemeVoteScrutin" placeholder="Veuillez passer par 'Voir mes scrutins' pour pouvoir voter" disabled>
					</div>
					<p class="help">Répresente le système de vote.</p>
				</div>

				<div class="field" style="">
					<label class="label" for="keyScrutin">Clé publique</label>
					<div class="control">
						<input class="input" type="text" name="keyScrutin" id="keyScrutin" placeholder="Veuillez passer par 'Voir mes scrutins' pour pouvoir voter" disabled>
					</div>
					<p class="help">Le vote sera chiffré.</p>
				</div>

				<p class="is-4" id="voteMessage"></p>

				<hr>
				<h3 class="title is-3" id="voteQuestion">...</h3>

				<div class="field" id="choicesUninominal" style="display:none;">
					<label class="label" for="id">Choix Uninominal</label>
					<div class="select is-rounded">
						<select id="voteChoicesUninominal">
						</select>
					</div>
					<p class="help">Veuillez choisir une proposition.</p>
				</div>

				<div class="field" id="choicesJugementMajoritaire" style="display:none;">
					<label class="label" for="id">Choix Jugement Majoritaire</label>
					<div>
						<table class="table is-hoverable is-fullwidth">
							<thead>
								<tr>
									<th>Choix</th>
									<th>Jugement</th>
								</tr>
							</thead>
							<tbody id="voteChoicesJugementMajoritaire">
							</tbody>
						</table>
					</div>
					<p class="help">Veuillez votez pour une proposition.</p>
				</div>
				
				<div class="field">
					<button class="button is-success is-medium" type="button" onclick="voter()" id="voterButton">Voter pour le scrutin</button>
					<p class="help">Votre vote sera chiffré.</p>
				</div>
			</div>

			<div id="resultatsScrutin" class="printable" style="display:none;">
				<!-- <h4 class="title is-4">Résultat pour un scrutin</h5> -->

				<p class='is-size-5'>
					Pour le scrutin : <span id="resultats-id"></span><br>
					Organisateur : <span id="resultats-organizer"></span><br>
					La question était : <span id="resultats-question"></span><br>
					Participation : <span id="resultats-participation"></span><br>
				</p>

				<h3 class="title is-3" >Résultats :</h3>
				<table class="table is-hoverable is-fullwidth has-text-centered" id="tableResultatsUninominal" style="display:none;">
					<thead>
						<tr>
							<th>Choix</th>
							<th>Absolu</th>
							<th>% Votants</th>
							<th>% Inscrits</th>
						</tr>
					</thead>
					<tbody id="resultats-results-uninominal">

					</tbody>
				</table>

				<table class="table is-hoverable is-fullwidth has-text-centered" id="tableResultatsJugementMajoritaire" style="display:none;">
					<thead>
						<tr>
							<th>Choix</th>
							<th>Répartition</th>
							<th>Mention</th>
						</tr>
					</thead>
					<tbody id="resultats-results-jugementMajoritaire">

					</tbody>
				</table>

				<div class="field">
					<button class="button is-info not-printable" type="button" onclick="pdfResultats()">Télécharger en PDF</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Footer -->
	<footer class="footer">
		<div class="content has-text-centered">
			<p>
			<strong>AVotey</strong> par Jessy FALLAVIER. Projet universitaire. Licence : <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/">CC BY NC SA 4.0</a>.
			</p>
		</div>
	</footer>
</body>
</html>