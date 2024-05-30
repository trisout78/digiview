<template>
	<div id="page">
		<div id="accueil" :style="{'background-image': 'url(./static/img/fond.png)'}">
			<div id="conteneur">
				<div id="contenu">
					<h1>
						<span>Triview</span>
					</h1>
					<div>
						<p><strong>TriView</strong> est un outil basé sur Digiview qui permet de visionner des vidéos YouTube en bypassant toute sécurité Google WorkSpace</p>
						<div id="lien">
							<label for="url">Lien de la vidéo YouTube</label>
							<input id="url" type="url" :value="lien" @input="lien = $event.target.value" @keydown.enter="valider">
						</div>
						<span id="bouton" role="button" tabindex="0" @click="valider">Valider</span>
					</div>
				</div>
				<div id="credits">
					<p>{{ new Date().getFullYear() }} - <a href="https://trisout.fr" target="_blank" rel="noreferrer">Trisout</a> - <a href="https://github.com/trisout78/digiview" target="_blank" rel="noreferrer">Code source</a></p>
				</div>
			</div>
		</div>
		<div class="conteneur-modale" role="dialog" tabindex="-1" v-if="modale">
			<div id="parametres-video" class="modale" role="document">
				<header>
					<span class="titre">Paramètres de la vidéo</span>
					<span class="fermer" role="button" tabindex="0" @click="fermerModale"><i class="material-icons">close</i></span>
				</header>
				<div class="conteneur">
					<div class="contenu">
						<label>Titre</label>
						<input type="text" :value="titre" @input="titre = $event.target.value">
						<label>Description</label>
						<div id="description" />
						<div class="temps">
							<div class="debut">
								<label>Début</label>
								<div class="minutes-secondes">
									<div class="minutes">
										<label>Min.</label>
										<input type="number" :value="debutMinutes" :min="0" @input="decouper('debutMinutes', $event.target.value)">
									</div>
									<div class="secondes">
										<label>Sec.</label>
										<input type="number" :value="debutSecondes" :min="0" :max="59" @input="decouper('debutSecondes', $event.target.value)">
									</div>
								</div>
							</div>
							<div class="fin">
								<label>Fin</label>
								<div class="minutes-secondes">
									<div class="minutes">
										<label>Min.</label>
										<input type="number" :value="finMinutes" :min="0" @input="decouper('finMinutes', $event.target.value)">
									</div>
									<div class="secondes">
										<label>Sec.</label>
										<input type="number" :value="finSecondes" :min="0" :max="59" @input="decouper('finSecondes', $event.target.value)">
									</div>
								</div>
							</div>
						</div>
						<div class="option option-vignette">
							<label>Afficher la vignette&nbsp;?</label>
							<span class="oui">
								<input type="radio" id="vignette_oui" name="vignette" value="oui" :checked="vignetteActivee === 'oui'" @change="vignetteActivee = $event.target.value">
								<label for="vignette_oui">Oui</label>
							</span>
							<span class="non">
								<input type="radio" id="vignette_non" name="vignette" value="non" :checked="vignetteActivee === 'non'" @change="vignetteActivee = $event.target.value">
								<label for="vignette_non">Non</label>
							</span>
						</div>
						<div class="option option-sous-titres" v-if="sousTitres.length > 0">
							<label>Afficher le bouton des sous-titres&nbsp;?</label>
							<span class="oui">
								<input type="radio" id="sous_titres_oui" name="sous-titres" value="oui" :checked="sousTitresActives === 'oui'" @change="sousTitresActives = $event.target.value">
								<label for="sous_titres_oui">Oui</label>
							</span>
							<span class="non">
								<input type="radio" id="sous_titres_non" name="sous-titres" value="non" :checked="sousTitresActives === 'non'" @change="sousTitresActives = $event.target.value">
								<label for="sous_titres_non">Non</label>
							</span>
						</div>
						<div class="actions">
							<span class="bouton" role="button" tabindex="0" @click="generer">Générer</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="hub" :class="{'ouvert': hub}">
			<span @click="fermerHub"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#fff" width="36px" height="36px"><path d="M0 0h24v24H0z" fill="none"/><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></span>
			<iframe src="https://ladigitale.dev/hub.html"></iframe>
		</div>
	</div>
</template>

<script>
import pell from 'pell'
import linkifyHtml from 'linkify-html'

export default {
	name: 'Accueil',
	data () {
		return {
			modale: false,
			lien: '',
			videoId: '',
			debutMinutes: 0,
			debutSecondes: 0,
			finMinutes: 120,
			finSecondes: 0,
			debut: 0,
			fin: 0,
			duree: 0,
			titre: '',
			description: '',
			vignette: '',
			largeur: 16,
			hauteur: 9,
			sousTitres: [],
			vignetteActivee: 'oui',
			sousTitresActives: 'oui',
			hub: false,
			version: app_version
		}
	},
	watch: {
		modale: function (modale) {
			if (modale === true) {
				this.$nextTick(function () {
					const editeur = pell.init({
						element: document.querySelector('#description'),
						onChange: function (html) {
							const description = linkifyHtml(html, {
								defaultProtocol: 'https'
							})
							this.description = description
						}.bind(this),
						actions: [{ name: 'gras', title: 'Gras', icon: '<i class="material-icons">format_bold</i>', result: () => pell.exec('bold') }, { name: 'italique', title: 'Italique', icon: '<i class="material-icons">format_italic</i>', result: () => pell.exec('italic') }, { name: 'souligne', title: 'Souligné', icon: '<i class="material-icons">format_underlined</i>', result: () => pell.exec('underline') }, { name: 'barre', title: 'Barré', icon: '<i class="material-icons">format_strikethrough</i>', result: () => pell.exec('strikethrough') }, { name: 'listeordonnee', title: 'Liste ordonnée', icon: '<i class="material-icons">format_list_numbered</i>', result: () => pell.exec('insertOrderedList') }, { name: 'liste', title: 'Liste', icon: '<i class="material-icons">format_list_bulleted</i>', result: () => pell.exec('insertUnorderedList') }],
						classes: { actionbar: 'boutons-editeur', button: 'bouton-editeur', content: 'contenu-editeur', selected: 'bouton-actif' }
					})
					editeur.content.innerHTML = this.description
					editeur.onpaste = function (event) {
						event.preventDefault()
						event.stopPropagation()
						const description = event.clipboardData.getData('text/plain')
						pell.exec('insertText', description)
					}
					document.querySelector('#description .contenu-editeur').addEventListener('focus', function () {
						document.querySelector('#description').classList.add('focus')
					})
					document.querySelector('#description .contenu-editeur').addEventListener('blur', function () {
						document.querySelector('#description').classList.remove('focus')
					})
				}.bind(this))
			}
		}
	},
	mounted () {
		setTimeout(function () {
			this.$parent.$parent.chargement = false
		}.bind(this), 300)
	},
	methods: {
		valider () {
			if (this.lien !== '' && this.verifierURL(this.lien) === true) {
				const regExp = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/
				if (this.lien.match(regExp)) {
					this.videoId = this.lien.match(regExp)[2]
					this.$parent.$parent.chargement = true
					let xhr = new XMLHttpRequest()
					xhr.onload = function () {
						if (xhr.readyState === xhr.DONE && xhr.status === 200) {
							if (xhr.response && Object.keys(xhr.response).length > 0) {
								if (xhr.response.hasOwnProperty('title') === true ) {
									this.titre = xhr.response.title
								}
								if (xhr.response.hasOwnProperty('thumbnail_url') === true) {
									this.vignette = xhr.response.thumbnail_url
								}
								if (xhr.response.hasOwnProperty('xhr.response.width') === true) {
									this.largeur = parseInt(xhr.response.width)
								}
								if (xhr.response.hasOwnProperty('xhr.response.height') === true) {
									this.hauteur = parseInt(xhr.response.height)
								}
								if (this.$parent.$parent.googleAPI !== '') {
									xhr = new XMLHttpRequest()
									xhr.onload = function () {
										if (xhr.readyState === xhr.DONE && xhr.status === 200) {
											if (xhr.response && xhr.response.hasOwnProperty('items') === true) {
												const duree = xhr.response.items[0].contentDetails.duration
												const temps = this.convertirTemps(duree)
												this.duree = temps.total
												let h = 0
												if (temps.h > 0) {
													h = temps.h * 60
												}
												this.finMinutes = temps.m + h
												this.finSecondes = temps.s
												if (xhr.response.items[0].contentDetails.caption === 'true' || xhr.response.items[0].contentDetails.caption === true) {
													xhr = new XMLHttpRequest()
													xhr.onload = function () {
														if (xhr.readyState === xhr.DONE && xhr.status === 200 && xhr.response && xhr.response.hasOwnProperty('items') === true) {
															const items = xhr.response.items
															items.forEach(function (item) {
																if (item.hasOwnProperty('kind') && item.kind === 'youtube#caption') {
																	this.sousTitres.push(item.snippet.language)
																}
															}.bind(this))
														}
														this.$parent.$parent.chargement = false
														this.modale = true
													}.bind(this)
													xhr.onerror = function () {
														this.$parent.$parent.chargement = false
														this.modale = true
													}.bind(this)
													xhr.open('GET', 'https://youtube.googleapis.com/youtube/v3/captions?part=snippet&videoId=' + this.videoId + '&key=' + this.$parent.$parent.googleAPI, true)
													xhr.responseType = 'json'
													xhr.send()
												} else {
													this.$parent.$parent.chargement = false
													this.modale = true
												}
											} else {
												this.$parent.$parent.chargement = false
												this.modale = true
											}
										} else if (xhr.readyState === xhr.DONE && xhr.status === 403) {
											this.$parent.$parent.chargement = false
											this.modale = true
											this.$parent.$parent.message = 'La limite du nombre de requêtes quotidiennes vers l\'API Google a été atteinte. La durée de la vidéo n\'a pas pu être récupérée et doit être précisée manuellement.'
										} else {
											this.$parent.$parent.chargement = false
											this.modale = true
										}
									}.bind(this)
									xhr.onerror = function () {
										this.$parent.$parent.chargement = false
										this.modale = true
									}.bind(this)
									xhr.open('GET', 'https://www.googleapis.com/youtube/v3/videos?part=contentDetails&key=' + this.$parent.$parent.googleAPI + '&id=' + this.videoId, true)
									xhr.responseType = 'json'
									xhr.send()
								} else {
									this.$parent.$parent.chargement = false
									this.modale = true
								}
							} else {
								this.$parent.$parent.chargement = false
								this.$parent.$parent.message = 'Erreur de communication avec le serveur.'
							}
						} else {
							this.$parent.$parent.chargement = false
							this.$parent.$parent.message = 'Erreur de communication avec le serveur.'
						}
					}.bind(this)
					xhr.onerror = function () {
						this.$parent.$parent.chargement = false
						this.$parent.$parent.message = 'Erreur de communication avec le serveur.'
					}.bind(this)
					xhr.open('POST', 'https://noembed.com/embed?url=' + this.lien, true)
					xhr.responseType = 'json'
					xhr.send()
				} else {
					this.$parent.$parent.message = 'Ce lien n\'est pas un lien YouTube valide.'
				}
			} else {
				this.$parent.$parent.message = 'Ce lien n\'est pas valide.'
			}
		},
		decouper (type, valeur) {
			if (type !== '' && valeur !== '') {
				this[type] = valeur
			}
			let debut = 0
			let fin = 0
			debut += parseInt(this.debutMinutes) * 60
			debut += parseInt(this.debutSecondes)
			fin += parseInt(this.finMinutes) * 60
			fin += parseInt(this.finSecondes)
			this.debut = debut
			this.fin = fin
			this.duree = fin - debut
		},
		generer () {
			this.decouper('', '')
			if (this.titre !== '' && this.duree !== 0) {
				if (this.vignetteActivee === 'non') {
					this.vignette = ''
				}
				if (this.sousTitresActives === 'non') {
					this.sousTitres = []
				}
				this.$parent.$parent.chargement = true
				const xhr = new XMLHttpRequest()
				xhr.onload = function () {
					if (xhr.readyState === xhr.DONE && xhr.status === 200) {
						this.$parent.$parent.chargement = false
						this.fermerModale()
						if (xhr.responseText !== 'erreur') {
							this.$router.push('/v/' + xhr.responseText)
						} else {
							this.$parent.$parent.message = 'Erreur de communication avec le serveur.'
						}
					} else {
						this.$parent.$parent.chargement = false
						this.fermerModale()
						this.$parent.$parent.message = 'Erreur de communication avec le serveur.'
					}
				}.bind(this)
				xhr.open('POST', this.$parent.$parent.hote + 'inc/generer_video.php', true)
				xhr.setRequestHeader('Content-type', 'application/json')
				const json = { titre: this.titre, description: this.description, vignette: this.vignette, videoId: this.videoId, debut: this.debut, fin: this.fin, largeur: this.largeur, hauteur: this.hauteur, sousTitres: this.sousTitres.toString() }
				xhr.send(JSON.stringify(json))
			} else if (this.titre === '') {
				this.$parent.$parent.message = 'Veuillez complétez le champ «&nbsp;Titre&nbsp;».'
			} else if (this.duree === 0) {
				this.$parent.$parent.message = 'Veuillez préciser manuellement les champs «&nbsp;Min.&nbsp;» et «&nbsp;Sec.&nbsp;» pour la fin de la vidéo. Ces informations sont nécessaires pour calculer la durée de la vidéo dans le lecteur.'
			}
		},
		fermerModale () {
			this.modale = ''
			this.lien = ''
			this.videoId = ''
			this.debutMinutes = 0
			this.debutSecondes = 0
			this.finMinutes = 0
			this.finSecondes = 0
			this.debut = 0
			this.fin = 0
			this.duree = 0
			this.titre = ''
			this.description = ''
			this.vignette = ''
			this.largeur = 16
			this.hauteur = 9
			this.sousTitres = []
		},
		verifierURL (lien) {
			let url
			try {
				url = new URL(lien)
			} catch (_) {
				return false
			}
			return url.protocol === 'http:' || url.protocol === 'https:'
		},
		convertirTemps (duree) {
			duree = duree.search(/PT/i) > -1 ? duree.slice(2) : duree
			let h, m, s
			let hIndex = duree.search(/h/i), mIndex = duree.search(/m/i), sIndex = duree.search(/s/i)
			let dContainsH = hIndex > -1, dContainsM = mIndex > -1, dContainsS = sIndex > -1
			h = dContainsH ? duree.slice(0, hIndex) : ''
			m = dContainsM ? duree.slice(dContainsH ? hIndex + 1 : 0, mIndex) : dContainsH ? '0' : '0'
			s = dContainsS ? duree.slice(dContainsM ? mIndex + 1 : hIndex + 1, sIndex) : '0'
			let total = 0
			if (h > 0) {
				total = total + (h * 60 * 60)
			}
			if (m > 0) {
				total = total + (m * 60)
			}
			if (s > 0) {
				total = total + s
			}
			return { h: parseInt(h), m: parseInt(m), s: parseInt(s), total: total }
		},
		ouvrirHub () {
			this.hub = true
		},
		fermerHub () {
			this.hub = false
		}
	}
}
</script>

<style scoped>
#page,
#accueil {
	width: 100%;
	height: 100%;
}

#accueil {
	background-size: cover;
	background-position: center;
	background-repeat: no-repeat;
}

#conteneur {
	position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
	flex-wrap: wrap;
	overflow: auto;
}

#contenu {
	max-width: 76em;
	text-align: center;
	padding: 12em 1em 6em;
	margin: auto;
}

#conteneur h1 {
    font-family: 'HKGroteskWide-ExtraBold', 'HKGrotesk-ExtraBold', sans-serif;
    font-size: 3em;
	font-weight: 900;
    margin-bottom: 0.85em;
    line-height: 1.4;
}

#conteneur p {
    font-size: 1.25em;
    font-weight: 400;
    line-height: 1.4;
    margin-bottom: 1.5em;
}

#lien {
	max-width: 450px;
    margin: 0 auto 1.5em;
	text-align: left;
}

#lien label {
	display: block;
	font-size: 1em;
	font-weight: 700;
	margin-bottom: 0.5em;
}

#lien input {
	font-size: 1.25em;
	font-weight: 400;
	line-height: 1.4;
	border: 1px solid #aaa;
	border-radius: 4px;
	padding: 0.5em 0.9em;
	background: #fff;
	width: 100%;
}

#credits {
	width: 100%;
	margin: 0 auto 0.75em;
}

#credits p {
    font-size: 1em;
    font-weight: 400;
    line-height: 1.2;
    margin-bottom: 1em;
	text-align: center;
}

#credits p:last-child {
	display: flex;
	justify-content: center;
	align-items: center;
}

#credits p:last-child a {
    margin: 0 5px;
}

#credits .hub {
	font-size: 0;
	cursor: pointer;
}

#hub {
	position: fixed;
	visibility: hidden;
	opacity: 0;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
	z-index: -1;
}

#hub.ouvert {
	visibility: visible;
	opacity: 1;
    animation: fonduEntrant linear 0.1s;
	z-index: 100000;
}

@keyframes fonduEntrant {
    from { opacity: 0; }
    to   { opacity: 1; }
}

#hub iframe {
	width: 100%;
    height: 100%;
}

#hub span {
	font-size: 0;
	color: #fff;
	position: absolute;
	top: 15px;
	right: 15px;
	cursor: pointer;
}

#parametres-video {
	max-height: 90%;
	overflow: auto;
	-webkit-overflow-scrolling: touch;
}

#parametres-video header {
	position: sticky;
	top: 0;
	background: #fff;
	z-index: 10;
}

#parametres-video .conteneur {
	padding: 0 20px 20px;
}

#parametres-video .conteneur .contenu > label:first-of-type {
	margin-top: 20px;
}

.modale .conteneur input[type="text"] {
	margin-bottom: 20px;
}

.modale .conteneur .temps {
	margin-top: 20px;
}

.modale .conteneur .debut,
.modale .conteneur .fin {
    display: inline-flex;
	justify-content: center;
	width: 50%;
	flex-wrap: wrap;
	text-align: center;
}

.modale .conteneur .video {
	text-align: center;
}

.modale .conteneur iframe {
	width: 400px;
	height: 225px;
	max-width: 100%;
}

.modale .conteneur .temps .minutes-secondes {
	display: flex;
	text-align: center;
}

.modale .conteneur .temps .minutes {
    margin-right: 10px;
}

.modale .conteneur input[type="text"],
.modale .conteneur input[type="number"] {
	display: block;
	font-weight: 400;
	font-size: 16px;
	border: 1px solid #ddd;
	border-radius: 4px;
	padding: 7px 15px;
	line-height: 1.5;
	text-align: left;
}

.modale .conteneur input[type="number"] {
	width: 80px;
}

.modale .conteneur input[type="text"] {
	width: 100%;
}

.modale .conteneur .actions {
	margin-top: 20px;
}

.modale .option {
	display: flex;
	align-items: center;
	margin-top: 20px;
}

.modale .option label {
	display: inline-block;
	width: auto;
	margin-bottom: 0;
}

.modale .option > label {
	margin-right: 25px;
}

.modale .option input {
	margin-right: 10px;
}

.modale .option .oui {
	margin-right: 20px;
}

#description {
	border: 1px solid #ddd;
    width: 100%;
	outline: 0;
	border-radius: 4px;
	margin-bottom: 20px;
}

#description.focus {
	border-color: #001d1d;
}

@media screen and (max-width: 359px) {
	#contenu {
		padding: 4em 1em 2em;
	}

	#bouton {
		font-size: 0.75em!important;
		width: 130px;
	}

	.modale .conteneur input[type="number"] {
		width: 50px;
		padding: 10px 5px;
	}
}

@media screen and (min-width: 360px) and (max-width: 599px) {
	#contenu {
		padding: 5em 1em 2.5em;
	}

	.modale .conteneur input[type="number"] {
		width: 60px;
		padding: 10px;
	}
}

@media screen and (max-width: 399px) {
	#conteneur h1 span {
		display: block;
	}
}

@media screen and (max-width: 599px) {
	#conteneur h1 {
		font-size: 2em;
		margin-bottom: 1em;
	}

	#conteneur p {
		font-size: 1em;
		margin-bottom: 1.2em;
	}

	#lien label {
		font-size: 0.8em;
	}
	
	#lien input {
		font-size: 1em;
	}
	
	#bouton {
		font-size: 0.85em;
	}
	
	#credits p {
		font-size: 0.85em;
	}
}

@media screen and (max-width: 599px) {
	#hub span {
		top: 5px;
		right: 5px;
	}
	
	#hub span svg {
		width: 24px;
		height: 24px;
	}
}

@media screen and (max-width: 599px) and (orientation: landscape) {
	#contenu {
		padding: 2em 1em 1.5em!important;
	}
}

@media screen and (min-width: 600px) and (max-width: 820px) and (orientation: landscape) {
	#contenu {
		padding: 3em 1em 1.5em!important;
	}
}

@media screen and (max-width: 820px) and (orientation: landscape) {
	#conteneur p {
		font-size: 1em!important;
	}

	#lien input {
		font-size: 1em!important;
	}
	
	#credits p {
		font-size: 0.85em!important;
		margin-bottom: 0.85em!important;
	}
}

@media screen and (max-width: 1023px) and (orientation: landscape) {
	#contenu {
		padding: 7em 1em 3.5em;
	}
}

@media screen and (max-width: 850px) and (max-height: 500px) {
	#conteneur h1 {
		font-size: 2em;
		margin-bottom: 1em;
	}

	#conteneur p {
		font-size: 1em;
		margin-bottom: 1.2em;
	}

	#bouton {
		font-size: 0.85em;
	}

	#lien label {
		font-size: 0.8em;
	}
	
	#lien input {
		font-size: 1em;
	}
	
	#credits p {
		font-size: 0.85em;
	}
}
</style>
