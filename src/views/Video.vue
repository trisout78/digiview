<template>
	<div id="page" :class="{'video-integree': integration}">
		<div id="conteneur">
			<div id="conteneur-video">
				<div id="contenu-video">
					<div id="video">
						<iframe :src="$parent.$parent.hote + 'inc/video.php?videoId=' + videoId + '&vignette=' + vignette + '&debut=' + debut + '&fin=' + fin + '&largeur=' + largeur + '&hauteur=' + hauteur + '&st=' + sousTitres" :frameborder="0" allowfullscreen title="Lire la vidéo" v-if="videoId !== '' && consentement && sousTitres !== '' && vignette !== ''"></iframe>
						<iframe :src="$parent.$parent.hote + 'inc/video.php?videoId=' + videoId + '&debut=' + debut + '&fin=' + fin + '&largeur=' + largeur + '&hauteur=' + hauteur + '&st=' + sousTitres" :frameborder="0" allowfullscreen title="Lire la vidéo" v-if="videoId !== '' && consentement && sousTitres !== '' && vignette === ''"></iframe>
						<iframe :src="$parent.$parent.hote + 'inc/video.php?videoId=' + videoId + '&vignette=' + vignette + '&debut=' + debut + '&fin=' + fin + '&largeur=' + largeur + '&hauteur=' + hauteur" :frameborder="0" allowfullscreen title="Lire la vidéo" v-if="videoId !== '' && consentement && sousTitres === '' && vignette !== ''"></iframe>
						<iframe :src="$parent.$parent.hote + 'inc/video.php?videoId=' + videoId + '&debut=' + debut + '&fin=' + fin + '&largeur=' + largeur + '&hauteur=' + hauteur" :frameborder="0" allowfullscreen title="Lire la vidéo" v-else-if="videoId !== '' && consentement && sousTitres === '' && vignette === ''"></iframe>
						<div class="alerte-contenu" v-else-if="!consentement">
							<div class="conteneur-alerte-contenu">
								<p>Cette vidéo est hébergée par YouTube. En affichant ce contenu, vous acceptez les <a href="https://www.youtube.com/static?template=terms" target="_blank">conditions d'utilisation</a> de YouTube.</p>
								<span id="afficher" role="button" tabindex="0" @click="afficher">Afficher la vidéo</span>
								<div class="memorisation">
									<input type="checkbox" id="memorisation" :selected="memorisation" @change="memorisation = $event.target.checked">
									<label for="memorisation">Se souvenir de mon choix</label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="titre-video">
				{{ titre }}
			</div>
			<div id="description-video" v-if="description !== ''" v-html="description" />
			<div id="credits" v-if="!integration">
				<p>{{ new Date().getFullYear() }} - <a href="https://trisout.fr" target="_blank" rel="noreferrer">Trisout</a></p>
			</div>	
		</div>

		<div id="menu-partager" v-if="menu === 'partager'" :style="{'left': position + 'px'}">
			<div id="conteneur-partager">
				<label>Lien et code QR&nbsp;:</label>
				<div id="copier-lien" class="copier">
					<input type="text" disabled :value="definirRacine() + '#/v/' + id">
					<span class="icone lien" role="button" tabindex="0" title="Copier le lien"><i class="material-icons">content_copy</i></span>
					<span class="icone codeqr" role="button" tabindex="0" title="Afficher le code QR" @click="afficherCodeQR"><i class="material-icons">qr_code</i></span>
				</div>
				<label>Code d'intégration&nbsp;:</label>
				<div id="copier-iframe" class="copier">
					<input type="text" disabled :value="'<iframe src=&quot;' + $parent.$parent.hote + 'inc/video.php?videoId=' + videoId + '&vignette=' + vignette + '&debut=' + debut + '&fin=' + fin + '&largeur=' + largeur + '&hauteur=' + hauteur + '&st=' + sousTitres + '&quot; allowfullscreen frameborder=&quot;0&quot; width=&quot;700&quot; height=&quot;394&quot;></iframe>'" v-if="sousTitres !== '' && vignette !== ''">
					<input type="text" disabled :value="'<iframe src=&quot;' + $parent.$parent.hote + 'inc/video.php?videoId=' + videoId + '&debut=' + debut + '&fin=' + fin + '&largeur=' + largeur + '&hauteur=' + hauteur + '&st=' + sousTitres + '&quot; allowfullscreen frameborder=&quot;0&quot; width=&quot;700&quot; height=&quot;394&quot;></iframe>'" v-if="sousTitres !== '' && vignette === ''">
					<input type="text" disabled :value="'<iframe src=&quot;' + $parent.$parent.hote + 'inc/video.php?videoId=' + videoId + '&vignette=' + vignette + '&debut=' + debut + '&fin=' + fin + '&largeur=' + largeur + '&hauteur=' + hauteur + '&quot; allowfullscreen frameborder=&quot;0&quot; width=&quot;700&quot; height=&quot;394&quot;></iframe>'" v-if="sousTitres === '' && vignette !== ''">
					<input type="text" disabled :value="'<iframe src=&quot;' + $parent.$parent.hote + 'inc/video.php?videoId=' + videoId + '&debut=' + debut + '&fin=' + fin + '&largeur=' + largeur + '&hauteur=' + hauteur + '&quot; allowfullscreen frameborder=&quot;0&quot; width=&quot;700&quot; height=&quot;394&quot;></iframe>'" v-else>
					<span class="icone" role="button" tabindex="0" title="Copier le code d'intégration"><i class="material-icons">content_copy</i></span>
				</div>
			</div>
		</div>

		<div class="conteneur-modale" role="dialog" tabindex="-1" v-if="modale === 'code-qr'">
			<div id="codeqr" class="modale" role="document">
				<header>
					<span class="titre">Code QR</span>
					<span class="fermer" role="button" tabindex="0" @click="modale = ''"><i class="material-icons">close</i></span>
				</header>
				<div class="conteneur">
					<div class="contenu">
						<div id="qr" />
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import linkifyHtml from 'linkify-html'
import ClipboardJS from 'clipboard'

export default {
	name: 'Video',
	data () {
		return {
			id: '',
			videoId: '',
			titre: '',
			description: '',
			vignette: '',
			debut: 0,
			fin: 0,
			largeur: 16,
			hauteur: 9,
			sousTitres: '',
			integration: false,
			modale: '',
			codeqr: '',
			menu: '',
			position: 0,
			consentement: false,
			memorisation: false
		}
	},
	watch: {
		modale: function (valeur) {
			if (valeur !== '') {
				this.menu = ''
			}
		}
	},
	created () {
		if (process.env.NODE_ENV !== 'production') {
			this.$parent.$parent.hote = 'http://127.0.0.1:8000/'
		}
		if (localStorage.getItem('digiview_consent') && parseInt(localStorage.getItem('digiview_consent')) === 1) {
			this.consentement = true
		}
		this.id = this.$route.params.id
		const xhr = new XMLHttpRequest()
		xhr.onload = function () {
			if (xhr.readyState === xhr.DONE && xhr.status === 200) {
				if (xhr.responseText === 'contenu_inexistant' || this.verifierJSON(xhr.responseText) === false) {
					this.$router.push('/')
					return false
				}
				const donnees = JSON.parse(xhr.responseText)
				if (!donnees || !donnees.hasOwnProperty('videoId') || donnees.videoId === '') {
					this.$router.push('/')
					return false
				}
				this.videoId = donnees.videoId
				this.titre = donnees.titre
				this.description = linkifyHtml(donnees.description, {
					defaultProtocol: 'https'
				})
				this.vignette = donnees.vignette
				this.debut = donnees.debut
				this.fin = donnees.fin
				this.largeur = donnees.largeur
				this.hauteur = donnees.hauteur
				this.sousTitres = donnees.sousTitres
				if (this.vignette === null) {
					this.vignette = ''
				}
				if (this.largeur === null) {
					this.largeur = 16
				}
				if (this.hauteur === null) {
					this.hauteur = 9
				}
				if (this.sousTitres === null) {
					this.sousTitres = ''
				}
				if (window.location !== window.parent.location && document.referrer === 'https://digipad.app') {
					window.parent.postMessage(this.vignette, 'https://digipad.app')
				} else if (window.location !== window.parent.location && document.referrer === 'https://digiwall.app') {
					window.parent.postMessage(this.vignette, 'https://digiwall.app')
				}
				setTimeout(function () {
					document.title = this.titre + ' - Digiview by La Digitale'

					const lien = this.definirRacine() + '#/v/' + this.id
					const clipboardLien = new ClipboardJS('#copier-lien .lien', {
						text: function () {
							return lien
						}
					})
					clipboardLien.on('success', function () {
						this.$parent.$parent.notification = 'Lien copié dans le presse-papier.'
					}.bind(this))
					let iframe
					if (this.sousTitres !== '' && this.vignette !== '') {
						iframe = '<iframe src="' + this.$parent.$parent.hote + 'inc/video.php?videoId=' + this.videoId + '&vignette=' + this.vignette + '&debut=' + this.debut + '&fin=' + this.fin + '&largeur=' + this.largeur + '&hauteur=' + this.hauteur + '&st=' + this.sousTitres + '" allowfullscreen frameborder="0" width="700" height="394"></iframe>'
					} else if (this.sousTitres !== '' && this.vignette === '') {
						iframe = '<iframe src="' + this.$parent.$parent.hote + 'inc/video.php?videoId=' + this.videoId + '&debut=' + this.debut + '&fin=' + this.fin + '&largeur=' + this.largeur + '&hauteur=' + this.hauteur + '&st=' + this.sousTitres + '" allowfullscreen frameborder="0" width="700" height="394"></iframe>'
					} else if (this.sousTitres === '' && this.vignette !== '') {
						iframe = '<iframe src="' + this.$parent.$parent.hote + 'inc/video.php?videoId=' + this.videoId + '&vignette=' + this.vignette + '&debut=' + this.debut + '&fin=' + this.fin + '&largeur=' + this.largeur + '&hauteur=' + this.hauteur + '" allowfullscreen frameborder="0" width="700" height="394"></iframe>'
					} else {
						iframe = '<iframe src="' + this.$parent.$parent.hote + 'inc/video.php?videoId=' + this.videoId + '&debut=' + this.debut + '&fin=' + this.fin + '&largeur=' + this.largeur + '&hauteur=' + this.hauteur + '" allowfullscreen frameborder="0" width="700" height="394"></iframe>'
					}
					const clipboardIframe = new ClipboardJS('#copier-iframe span', {
						text: function () {
							return iframe
						}
					})
					clipboardIframe.on('success', function () {
						this.$parent.$parent.notification = 'Code d\'intégration copié dans le presse-papier.'
					}.bind(this))

					this.$parent.$parent.chargement = false
				}.bind(this), 300)
			} else {
				this.$parent.$parent.chargement = false
				this.$parent.$parent.message = 'Erreur de communication avec le serveur.'
			}
		}.bind(this)
		xhr.open('POST', this.$parent.$parent.hote + 'inc/recuperer_video.php', true)
		xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
		xhr.send('id=' + this.id)
	},
	mounted () {
		document.addEventListener('click', function (event) {
			const partager = document.querySelector('#partager')
			const menuPartager = document.querySelector('#menu-partager')
			if (partager && menuPartager && event.target !== partager && event.target !== menuPartager && !partager.contains(event.target) && !menuPartager.contains(event.target)) {
				this.menu = ''
			}
		}.bind(this))

		if (window !== window.parent) {
			this.integration = true
			document.querySelector('#app').classList.add('integration')
		}
	},
	methods: {
		definirRacine () {
			return window.location.href.split('#')[0]
		},
		verifierJSON (json) {
			try {
				JSON.parse(json)
				return true
			} catch {
				return false
			}
		},
		afficherMenuPartager (event) {
			this.menu = 'partager'
			this.$nextTick(function () {
				const largeurBouton = event.target.getBoundingClientRect().width
				const largeurMenu = document.querySelector('#menu-partager').getBoundingClientRect().width
				this.position = event.target.getBoundingClientRect().left - ((largeurMenu * 90) / 100 - largeurBouton / 2)
			}.bind(this))
		},
		afficherCodeQR () {
			this.modale = 'code-qr'
			this.$nextTick(function () {
				const lien = this.definirRacine() + '#/v/' + this.id
				// eslint-disable-next-line
				this.codeqr = new QRCode('qr', {
					text: lien,
					width: 360,
					height: 360,
					colorDark: '#000000',
					colorLight: '#ffffff',
					// eslint-disable-next-line
					correctLevel : QRCode.CorrectLevel.H
				})
			}.bind(this))
		},
		afficher () {
			this.consentement = true
			if (this.memorisation === true) {
				localStorage.setItem('digiview_consent', 1)
			}
		}
	}
}
</script>

<style scoped>
#page {
	width: 100%;
}

#page > header {
	position: sticky;
	top: 0;
	left: 0;
	right: 0;
	height: 40px;
    width: 100%;
    background: #fff;
    color: #001d1d;
	z-index: 100;
	user-select: none;
}

#logo {
    width: 24px;
    height: 24px;
    background: #00ced1;
    display: inline-block;
    border-radius: 50%;
	margin: 8px 0;
	vertical-align: middle;
}

#titre {
	font-family: 'HKGroteskWide-ExtraBold', 'HKGrotesk-ExtraBold', sans-serif;
	font-size: 20px;
    line-height: 40px;
    vertical-align: middle;
	margin-left: 20px;
	display: inline-block;
}

#titre:before {
	position: absolute;
	top: 100%;
	bottom: auto;
	right: 0;
    content: '';
    width: 100%;
    height: 8px;
    pointer-events: none;
    background: linear-gradient(180deg, rgba(0, 0, 0, 0.25) 0, rgba(0, 0, 0, 0.08) 40%, rgba(0, 0, 0, 0.04) 50%, transparent 90%, transparent);
}

#conteneur {
	position: relative;
}

#conteneur-header {
    display: flex;
	justify-content: space-between;
}

#conteneur-header .conteneur {
	display: flex;
	align-items: center;
}

#partager {
	width: 24px;
	font-size: 24px;
	cursor: pointer;
}

#partager i {
	width: 24px;
	font-size: 24px;
}

#conteneur-video {
    background-color: #001d1d;
    margin: 0;
    width: 100%;
}

#conteneur-header,
#titre-video,
#description-video,
#contenu-video,
#credits {
    padding: 0 20px;
    margin: auto;
    width: 100%;
	max-width: 1024px;
}

#video {
    position: relative;
    display: block;
    height: 0;
    padding: 0 0 56.25% 0;
    overflow: hidden;
	background: #000;
}

#video iframe {
	position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
	right: 0;
	width: 100%;
    height: 100%;
    border: 0;
}

#video .alerte-contenu {
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	display: flex;
	justify-content: center;
	align-items: center;
	width: 100%;
	height: 100%;
	text-align: center;
	background: #3e526d;
	color: #fff;
	padding: 20px;
}

#video .alerte-contenu p {
	font-size: 18px;
	line-height: 1.5;
}

#video .alerte-contenu p a {
	text-decoration: underline;
}

#afficher {
	margin: 20px 0;
}

#video .memorisation label {
	font-size: 14px;
	margin-left: 7px;
}

.copier {
	display: flex;
	justify-content: center;
	align-items: center;
}

.copier input {
	display: block;
	width: calc(100% - 34px);
	font-weight: 400;
	font-size: 16px;
	border: 1px solid #ddd;
	border-radius: 4px;
	padding: 7px 15px;
	line-height: 1.5;
	text-align: left;
	margin-right: 10px;
}

#copier-lien {
	margin-bottom: 20px;
}

#copier-lien input {
	width: calc(100% - 68px);
}

#copier-lien span:last-child {
	margin-left: 10px;
}

.copier span i {
	font-size: 24px;
	width: 24px;
	height: 24px;
	cursor: pointer;
}

.copier span i:active {
	opacity: 0.8;
}

.video-integree #conteneur {
	height: 100%!important;
	margin-top: 0!important;
	background-color: #fff!important;
}

.video-integree #conteneur-video {
	background: #fff!important;
}

.video-integree #contenu-video {
	width: 100%!important;
	max-width: 100%!important;
	padding: 0!important;
}

.video-integree #titre-video {
    padding: 1em 0!important;
}

.video-integree #titre-video + #description-video {
    margin-bottom: 0!important;
    padding: 0 0 1em!important;
}

#menu-partager {
	position: absolute;
    z-index: 1000;
    border: 1px solid #ddd;
    padding: 1rem;
    border-radius: 1rem;
	background: #fff;
	box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.25);
	top: 54px;
	width: 300px;
}
	
#menu-partager:after {
	border: solid #ddd;
	content: '';
	display: block;
	position: absolute;
	top: 0;
	border-width: 0 1px 1px 0;
	transform: translate(-7px, -8px) rotate(-135deg);
	background: #fff;
    left: 90%;
    width: 14px;
    height: 14px;
}

#menu-partager label {
	display: block;
    width: 100%;
    font-weight: 700;
    font-size: 14px;
    margin-bottom: 10px;
    user-select: none;
}

@media screen and (max-width: 399px) {
	#video .alerte-contenu p {
		font-size: 12px;
		line-height: 1.25;
	}

	#afficher {
		font-size: 11px;
		height: 30px;
		line-height: 30px;
		margin: 12px 0;
	}

	#video .memorisation label {
		font-size: 11px;
	}
}

@media screen and (min-width: 400px) and (max-width: 599px) {
	#video .alerte-contenu p {
		font-size: 14px;
		line-height: 1.25;
	}

	#afficher {
		font-size: 12px;
		height: 30px;
		line-height: 30px;
		margin: 15px 0;
	}

	#video .memorisation label {
		font-size: 12px;
	}
}

@media screen and (min-width: 768px) {
	#conteneur-header,
	#titre-video,
	#description-video,
	#contenu-video,
	#credits {
		width: 750px;
	}
}

@media screen and (min-width: 992px) {
	#conteneur-header,
	#titre-video,
	#description-video,
	#contenu-video,
	#credits {
		width: 970px;
	}
}

@media screen and (min-width: 1200px) {
	#conteneur-header,
	#titre-video,
	#description-video,
	#contenu-video,
	#credits {
		width: 1170px;
	}
}

#titre-video {
	font-size: 1.5em;
	font-weight: 700;
	padding: 2em 20px 1em;
}

#description-video {
	font-size: 1.1em;
	padding: 2em 20px;
	line-height: 1.5;
}

#titre-video + #description-video {
	margin-bottom: 1em;
	padding: 0 20px 2em;
}

#credits {
	width: 100%;
	margin: 2em auto 2em;
	padding-top: 2em;
	border-top: 1px solid #ddd;
}

#credits p {
    font-size: 1em;
    font-weight: 400;
    line-height: 1.2;
    margin-bottom: 1em;
	text-align: center;
}

#codeqr.modale {
	max-width: 400px;
}

#codeqr.modale .contenu {
	text-align: center;
	font-size: 0;
}

@media screen and (max-width: 359px) {
	#titre {
		font-size: 16px;
		margin-left: 12px;
	}
}

@media screen and (min-width: 360px) and (max-width: 399px) {
	#titre {
		font-size: 17px;
		margin-left: 12px;
	}
}

@media screen and (max-width: 399px) {
	#titre-video {
		font-size: 1.2em;
	}
}

@media screen and (max-width: 599px) {
	#description-video {
		font-size: 0.95em;
	}

	#credits p {
		font-size: 0.75em;
	}
}

@media screen and (max-width: 820px) and (orientation: landscape) {
	#description-video {
		font-size: 0.95em!important;
	}

	#credits p {
		margin-bottom: 0.75em!important;
	}
}

@media screen and (max-width: 850px) and (max-height: 500px) {
	#description-video {
		font-size: 0.95em;
	}

	#credits p {
		font-size: 0.75em;
	}
}
</style>

<style>
#codeqr.modale #qr {
	display: inline-block;
}

#codeqr.modale #qr img {
	max-width: 100%;
	height: auto;
	max-height: 60vh;
}
</style>
