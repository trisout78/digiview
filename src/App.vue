<template>
	<main>
		<div id="conteneur-chargement" v-if="chargement">
			<div id="chargement">
				<div class="spinner"><div /><div /><div /><div /><div /><div /><div /><div /><div /><div /><div /><div /></div>
			</div>
		</div>
		<div id="conteneur-message" class="conteneur-modale" role="dialog" tabindex="-1" v-if="message">
			<div id="message" class="modale" role="document">
				<div class="conteneur">
					<div class="contenu">
						<div class="message" v-html="message" />
						<div class="actions">
							<span class="bouton" role="button" tabindex="0" @click="message = ''">Fermer</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<router-view />
	</main>
</template>

<script>
export default {
	name: 'App',
	data () {
		return {
			hote: '',
			googleAPI: '',
			chargement: true,
			message: '',
			notification: ''
		}
	},
	watch: {
		notification: function (notification) {
			if (notification !== '') {
				const element = document.createElement('div')
				const id = 'notification_' + Date.now().toString(36) + Math.random().toString(36).substr(2)
				element.id = id
				element.textContent = notification
				element.classList.add('notification')
				document.querySelector('#app').appendChild(element)
				this.notification = ''
				setTimeout(function () {
					element.parentNode.removeChild(element)
				}, 2000)
			}
		}
	},
	created () {
		this.hote = window.location.href.split('#')[0]
		if (import.meta.env.VITE_GOOGLE_API) {
			this.googleAPI = import.meta.env.VITE_GOOGLE_API
		}
	}
}
</script>

<style src="destyle.css/destyle.css"></style>
<style src="@/assets/css/style.css"></style>
