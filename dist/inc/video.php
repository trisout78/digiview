<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

if (empty($_GET['videoId'])) {
	header('Location: ../');
	exit();
}

$serveur = $_SERVER['SERVER_NAME'];

?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link href="./video.css?id=<?php echo md5(uniqid(time(), true)) ?>" rel="stylesheet" type="text/css">
		<?php if (!empty($_GET['vignette'])) { ?>
			<style>
				.vignette {
					background-image: url(<?php echo $_GET['vignette']; ?>);
				}
			</style>
		<?php } ?>
		<script>
			const videoId = '<?php echo $_GET['videoId']; ?>';
			let debut = 0;
			let fin = 600;
			<?php if (!empty($_GET['debut'])) : ?>
				debut = <?php echo $_GET['debut']; ?>;
			<?php endif; ?>
			<?php if (!empty($_GET['fin'])) : ?>
				fin = <?php echo $_GET['fin']; ?>;
			<?php endif; ?>
			let sousTitres = '';
			<?php if (!empty($_GET['st'])) : ?>
				sousTitres = '<?php echo $_GET['st']; ?>';
			<?php endif; ?>
			let vignette = '';
			<?php if (!empty($_GET['vignette'])) : ?>
				vignette = '<?php echo $_GET['vignette']; ?>';
			<?php endif; ?>
			let largeur = 16;
			let hauteur = 9;
			<?php if (!empty($_GET['largeur'])) : ?>
				largeur = <?php echo $_GET['largeur']; ?>;
			<?php endif; ?>
			<?php if (!empty($_GET['hauteur'])) : ?>
				hauteur = <?php echo $_GET['hauteur']; ?>;
			<?php endif; ?>
			let ratio = 'large';
			if ((parseInt(largeur) / parseInt(hauteur)) === (4/3)) {
				ratio = 'tv';
			} else if ((parseInt(largeur) / parseInt(hauteur)) === 1) {
				ratio = 'carre';
			} else if (parseInt(largeur) < parseInt(hauteur)) {
				ratio = 'vertical';
			}
		</script>
	</head>

	<body>
		<div id="app">
			<div id="exterieur">
				<div id="interieur">
					<div id="conteneur">
						<div id="conteneur-video">
							<div id="masque-video">
								<div id="video">
									<iframe id="lecteur" frameborder="0" allowfullscreen width="100%" height="100%"></iframe>
								</div>
							</div>
							<div id="masque-transparent" onclick="lecture()"></div>
							<div id="masque" onclick="lecture()" class="vignette">
								<div id="masque-lecture">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="48px" height="48px"><path d="M0 0h24v24H0z" fill="none"/><path d="M8 5v14l11-7z"/></svg>
								</div>
								<div id="masque-relecture">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="48px" height="48px"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 5V1L7 6l5 5V7c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z"/></svg>
								</div>
							</div>
							<div id="controles">
								<span id="bouton-lecture" class="bouton" role="button" tabindex="0" onclick="lecture()">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="24px" height="24px" id="icone-lancer"><path d="M0 0h24v24H0z" fill="none"/><path d="M8 5v14l11-7z"/></svg>

									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="24px" height="24px" id="icone-pause" style="display: none;"><path d="M0 0h24v24H0z" fill="none"/><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>

									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="24px" height="24px" id="icone-rejouer" style="display: none;"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 5V1L7 6l5 5V7c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z"/></svg>
								</span>
								<span id="bouton-volume" class="bouton" role="button" tabindex="0" onclick="definirStatutVolume()">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="24px" height="24px" id="icone-volume"><path d="M0 0h24v24H0z" fill="none"/><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>

									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="24px" height="24px" id="icone-muet" style="display:none"><path d="M0 0h24v24H0z" fill="none"/><path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/></svg>
								</span>
								<span id="bouton-sous-titres" class="bouton" role="button" tabindex="0">
									<span id="liste-sous-titres"></span>
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="24px" height="24px"><path d="M0 0h24v24H0z" fill="none"/><path d="M19 4H5c-1.11 0-2 .9-2 2v12c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-8 7H9.5v-.5h-2v3h2V13H11v1c0 .55-.45 1-1 1H7c-.55 0-1-.45-1-1v-4c0-.55.45-1 1-1h3c.55 0 1 .45 1 1v1zm7 0h-1.5v-.5h-2v3h2V13H18v1c0 .55-.45 1-1 1h-3c-.55 0-1-.45-1-1v-4c0-.55.45-1 1-1h3c.55 0 1 .45 1 1v1z"/></svg>
								</span>
								<span id="bouton-vitesse" class="bouton" role="button" tabindex="0">
									<span id="liste-vitesses">
										<span>Vitesse de lecture</span>
										<span class="vitesse" data-vitesse="0.2">20%</span>
										<span class="vitesse" data-vitesse="0.4">40%</span>
										<span class="vitesse" data-vitesse="0.6">60%</span>
										<span class="vitesse" data-vitesse="0.7">70%</span>
										<span class="vitesse" data-vitesse="0.8">80%</span>
										<span class="vitesse" data-vitesse="0.9">90%</span>
										<span class="vitesse active" data-vitesse="1">100%</span>
										<span class="vitesse" data-vitesse="1.1">110%</span>
										<span class="vitesse" data-vitesse="1.2">120%</span>
										<span class="vitesse" data-vitesse="1.3">130%</span>
										<span class="vitesse" data-vitesse="1.4">140%</span>
										<span class="vitesse" data-vitesse="1.6">160%</span>
										<span class="vitesse" data-vitesse="1.8">180%</span>
										<span class="vitesse" data-vitesse="2">200%</span>
									</span>
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="24px" height="24px"><path d="M0 0h24v24H0z" fill="none"/><path d="M20.38 8.57l-1.23 1.85a8 8 0 0 1-.22 7.58H5.07A8 8 0 0 1 15.58 6.85l1.85-1.23A10 10 0 0 0 3.35 19a2 2 0 0 0 1.72 1h13.85a2 2 0 0 0 1.74-1 10 10 0 0 0-.27-10.44zm-9.79 6.84a2 2 0 0 0 2.83 0l5.66-8.49-8.49 5.66a2 2 0 0 0 0 2.83z"/></svg>
								</span>
								<span id="bouton-ratio" class="bouton" role="button" tabindex="0">
									<span id="liste-ratio">
										<span>Ratio de l'écran</span>
										<span class="ratio" data-ratio="large">16/9</span>
										<span class="ratio" data-ratio="tv">4/3</span>
										<span class="ratio" data-ratio="carre">1/1</span>
										<span class="ratio" data-ratio="vertical">9/16</span>
									</span>
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="24px" height="24px"><path d="M0 0h24v24H0z" fill="none"/><path d="M19 12h-2v3h-3v2h5v-5zM7 9h3V7H5v5h2V9zm14-6H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16.01H3V4.99h18v14.02z"/></svg>
								</span>
								<span id="bouton-image" class="bouton" role="button" tabindex="0">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="24px" height="24px"><path d="m21 18.15-2-2V5H7.85l-2-2H19q.825 0 1.413.587Q21 4.175 21 5Zm-1.2 4.45L18.2 21H5q-.825 0-1.413-.587Q3 19.825 3 19V5.8L1.4 4.2l1.4-1.4 18.4 18.4ZM6 17l3-4 2.25 3 .825-1.1L5 7.825V19h11.175l-2-2Zm7.425-6.425ZM10.6 13.4Z"/></svg>
								</span>
								<span id="bouton-plein-ecran" class="bouton" role="button" tabindex="0" onclick="ouvrirPleinEcran()">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="24px" height="24px" id="icone-plein-ecran"><path d="M0 0h24v24H0z" fill="none"/><path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/></svg>
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="24px" height="24px" id="icone-sortir-plein-ecran" style="display:none"><path d="M0 0h24v24H0z" fill="none"/><path d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z"/></svg>
								</span>
								<div id="conteneur-recherche">
									<span id="tooltip"></span>
									<div id="recherche">
										<div id="contenu-recherche">
											<div id="lu"></div>
											<div id="charge"></div>
										</div>
									</div>
								</div>
								<div id="conteneur-volume">
									<div id="volume" data-volume="100">
										<div id="niveau"></div>
									</div>
								</div>
								<div id="temps">
									<span id="ecoule">00:00</span> <span>/</span> <span id="duree">00:00</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<script>
			const serveur = '<?php echo $serveur; ?>'
			const parent = (window.location !== window.parent.location) ? document.referrer : document.location.href
			let integration = !parent.includes(serveur)
			let consentement = false
			if (serveur === '127.0.0.1') {
				integration = false
			}
			if ((localStorage.getItem('digiview_consent') && parseInt(localStorage.getItem('digiview_consent')) === 1) || serveur === '127.0.0.1') {
				consentement = true
			}
			let memorisation = false
			if (integration && !consentement) {
				const html= `<div class="conteneur-alerte-contenu"><p>Cette vidéo est hébergée par YouTube. En affichant ce contenu, vous acceptez les <a href="https://www.youtube.com/static?template=terms" target="_blank">conditions d'utilisation</a> de YouTube.</p><span id="afficher" role="button" tabindex="0" onclick="afficher()">Afficher la vidéo</span><div class="memorisation"><input type="checkbox" id="memorisation" onchange="memorisation = event.target.checked"><label for="memorisation">Se souvenir de mon choix</label></div></div>`
				const alerte = document.createElement('div')
				alerte.id = 'alerte-contenu'
				alerte.innerHTML = html
				document.querySelector('#masque-video').parentNode.insertBefore(alerte, document.querySelector('#masque-video'))
			} else {
				integrer()
			}

			function afficher () {
				document.querySelector('#alerte-contenu').remove()
				integrer()
				if (memorisation === true) {
					localStorage.setItem('digiview_consent', 1)
				}
			}

			function integrer () {
				document.querySelector('#lecteur').src = `https://www.youtube-nocookie.com/embed/` + videoId + `?iv_load_policy=3&cc_load_policy=1&modestbranding=1&playsinline=1&rel=0&controls=0&showinfo=0&start=` + debut + `&end=` + fin + `&enablejsapi=1`
				const script = document.createElement('script')
				script.src = './video.js?id=<?php echo md5(uniqid(time(), true)) ?>'
				const premierScript = document.getElementsByTagName('script')[0]
				premierScript.parentNode.insertBefore(script, premierScript)
			}
		</script>
	</body>
</html>
