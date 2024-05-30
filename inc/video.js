const volume = document.querySelector('#volume')
const tooltip = document.querySelector('#tooltip')
const conteneurVideo = document.querySelector('#conteneur-video')
const options = {
    iv_load_policy: 3,
    cc_load_policy: 1,
    modestbranding: 1,
    playsinline: 1,
    rel: 0,
    controls: 0,
    autoplay: 0,
    start: debut,
    end: fin
}

let player, chargement, statut, cacherSouris, controlesAffiches, niveauVolume, mettreAJourTemps
let temps = 0
let duree = fin - debut
let pleinEcran = false
let videoLancee = false
const navChrome = !!window.chrome

document.body.classList.add(ratio)
document.querySelector('.ratio[data-ratio=' + ratio + ']').classList.add('active')

window.addEventListener('resize', redimensionnerListes)

function redimensionnerListes () {
	const hauteur = document.querySelector('#conteneur-video').offsetHeight
    if (hauteur < 257) {
		document.querySelector('#liste-ratio').style.maxHeight = (hauteur - 60) + 'px'
		document.querySelector('#liste-sous-titres').style.maxHeight = (hauteur - 60) + 'px'
		document.querySelector('#liste-vitesses').style.maxHeight = (hauteur - 60) + 'px'
    } else {
		document.querySelector('#liste-ratio').style.maxHeight = '257px'
        document.querySelector('#liste-sous-titres').style.maxHeight = '257px'
		document.querySelector('#liste-vitesses').style.maxHeight = '257px'
	}
}
redimensionnerListes()

if (vignette === '') {
	document.querySelector('#masque-transparent').classList.add('sans-vignette')
}

const boutonSousTitres = document.querySelector('#bouton-sous-titres')
const boutonVitesse = document.querySelector('#bouton-vitesse')
const boutonRatio = document.querySelector('#bouton-ratio')

if (sousTitres !== '') {
	let liste = ''
	const items = sousTitres.split(',')
	items.forEach(function (item) {
		if (item !== '') {
			liste += '<span class="langue" data-langue="' + item + '">' + item.toUpperCase() + '</span>'
		}
	})
	if (liste !== '') {
		boutonSousTitres.style.display = 'inline-block'
		document.querySelector('#liste-sous-titres').innerHTML = '<span>Sous-titres</span><span class="langue active" data-langue="off">Aucun</span>' + liste
		
		const sousTitres = document.querySelectorAll('#liste-sous-titres span.langue')
		sousTitres.forEach(function (item) {
			item.addEventListener('click', function (event) {
				if (document.querySelector('#liste-sous-titres .active')) {
					document.querySelector('#liste-sous-titres .active').classList.remove('active')
				}
				const langue = event.target.getAttribute('data-langue')
				if (langue === 'off') {
					document.querySelector('#video iframe').style.height = 'calc(100% + 460px)'
					document.querySelector('#video iframe').style.top = '-230px'
					boutonSousTitres.classList.remove('active')
				} else {
					if (statut === 1) {
						document.querySelector('#video iframe').style.height = '100%'
						document.querySelector('#video iframe').style.top = '0'
					}
					player.setOption('captions', 'track', { 'languageCode': langue })
					boutonSousTitres.classList.add('active')
				}
				event.target.classList.add('active')
			})
		})

		boutonSousTitres.addEventListener('click', function () {
			if (boutonVitesse.classList.contains('visible')) {
				boutonVitesse.classList.remove('visible')
			}
			if (boutonRatio.classList.contains('visible')) {
				boutonRatio.classList.remove('visible')
			}
			if (boutonSousTitres.classList.contains('visible')) {
				boutonSousTitres.classList.remove('visible')
			} else {
				boutonSousTitres.classList.add('visible')
			}
		})
	}
}

const script = document.createElement('script')
script.src = 'https://www.youtube.com/iframe_api'
const premierScript = document.getElementsByTagName('script')[0]
premierScript.parentNode.insertBefore(script, premierScript)

function onYouTubeIframeAPIReady () {
    player = new YT.Player('lecteur', {
        height: '100%',
        width: '100%',
        videoId: videoId,
		host: 'https://www.youtube-nocookie.com',
		suggestedQuality: 'default',
        events: {
            'onReady': lecteurCharge,
            'onStateChange': statutLecteurModifie
        },
        playerVars: options
    })
}

function lecteurCharge () {
	document.querySelector('#masque-lecture').style.display = 'block'
	document.querySelector('#masque-relecture').style.display = 'none'
	document.querySelector('#masque').style.display = 'block'
	document.querySelector('#masque').classList.remove('vignette')
	document.querySelector('#duree').textContent = formatTime(duree)
	
	const barreRecherche = document.querySelector('#contenu-recherche')
	barreRecherche.addEventListener('mousemove', function (event) {
		event.stopPropagation()
		const largeur = barreRecherche.offsetWidth
		const rect = barreRecherche.getBoundingClientRect()
		tooltip.textContent = formatTime(Math.round(((event.pageX - rect.left) / largeur) * duree))
		tooltip.style.display = 'block'
		const position = event.pageX - rect.left
		const largeurTooltip = tooltip.offsetWidth
		if (position + largeurTooltip > largeur) {
			tooltip.style.left = largeur - (largeurTooltip / 2) + 'px'
		} else if (position < 10) {
			tooltip.style.left = '10px'
		} else {
			tooltip.style.left = position + 'px'
		}
	})

	barreRecherche.addEventListener('mouseout', function () {
		tooltip.style.display = 'none'
	})

	barreRecherche.addEventListener('click', function (event) {
		const largeur = barreRecherche.offsetWidth
		const rect = barreRecherche.getBoundingClientRect()
		const secondes = Math.round(((event.pageX - rect.left) / largeur) * duree) + debut
		player.seekTo(secondes, true)
	})

	volume.addEventListener('click', function (event) {
		const largeur = volume.offsetWidth
		const rect = volume.getBoundingClientRect()
		let vol = ((event.pageX - rect.left) / largeur) * 100
		if (vol > 94) {
			vol = 100
		}
		niveauVolume = Math.round(vol)
		player.setVolume(Math.round(vol))
		volume.setAttribute('data-volume', Math.round(vol))
		document.querySelector('#niveau').style.width = Math.round(vol) + '%'
	})

	boutonVitesse.addEventListener('click', function () {
		if (boutonRatio.classList.contains('visible')) {
			boutonRatio.classList.remove('visible')
		}
		if (boutonSousTitres.classList.contains('visible')) {
			boutonSousTitres.classList.remove('visible')
		}
		if (boutonVitesse.classList.contains('visible')) {
			boutonVitesse.classList.remove('visible')
		} else {
			boutonVitesse.classList.add('visible')
		}
	})
	
	const elementsVitesse = document.querySelectorAll('#liste-vitesses span.vitesse')
	elementsVitesse.forEach(function (elementVitesse) {
		elementVitesse.addEventListener('click', function (event) {
			if (document.querySelector('#liste-vitesses .active')) {
				document.querySelector('#liste-vitesses .active').classList.remove('active')
			}
			const vitesse = parseFloat(event.target.getAttribute('data-vitesse'))
			player.setPlaybackRate(vitesse)
			if (vitesse === 1) {
				boutonVitesse.classList.remove('active')
			} else {
				boutonVitesse.classList.add('active')
			}
			event.target.classList.add('active')
		})
	})

	boutonRatio.addEventListener('click', function () {
		if (boutonVitesse.classList.contains('visible')) {
			boutonVitesse.classList.remove('visible')
		}
		if (boutonSousTitres.classList.contains('visible')) {
			boutonSousTitres.classList.remove('visible')
		}
		if (boutonRatio.classList.contains('visible')) {
			boutonRatio.classList.remove('visible')
		} else {
			boutonRatio.classList.add('visible')
		}
	})

	const elementsRatio = document.querySelectorAll('#liste-ratio span.ratio')
	elementsRatio.forEach(function (elementRatio) {
		elementRatio.addEventListener('click', function (event) {
			if (document.querySelector('#liste-ratio .active')) {
				document.querySelector('#liste-ratio .active').classList.remove('active')
			}
			document.body.classList.remove(ratio)
			ratio = event.target.getAttribute('data-ratio')
			document.body.classList.add(ratio)
			event.target.classList.add('active')
		})
	})

	document.querySelector('#bouton-image').addEventListener('click', function () {
		if (document.querySelector('#masque-transparent').classList.contains('noir')) {
			document.querySelector('#masque-transparent').classList.remove('noir')
			document.querySelector('#bouton-image').classList.remove('active')
		} else {
			document.querySelector('#masque-transparent').classList.add('noir')
			document.querySelector('#bouton-image').classList.add('active')
		}
	})

    chargement = setInterval(definirTampon, 1000)
    definirVolume(-1)
}

function definirTampon () {
    let fraction = (player.hasOwnProperty('getVideoLoadedFraction') ? player.getVideoLoadedFraction() : 0)
    if (fraction > 0) {
        fraction = parseInt(fraction * 100)
		document.querySelector('#charge').style.width = fraction + '%'
        if (fraction === 100) {
            clearInterval(chargement)
		}
    }
}

function definirTemps () {
    if (player && player.getCurrentTime && player.getCurrentTime() > 0) {
        temps = player.getCurrentTime() - debut
        if (player.getCurrentTime() >= fin) {
            player.pauseVideo()
			document.querySelector('#ecoule').textContent = formatTime(0)
			clearInterval(mettreAJourTemps)
        } else {
			document.querySelector('#ecoule').textContent = formatTime(temps)
		}
		if ((temps / duree) * 100 <= 100) {
			document.querySelector('#lu').style.width = Math.round((temps / duree) * 100) + '%'
		}
    }
}

// Résolution bug iframe Chrome
if (navChrome === true && videoLancee === false) {
	document.querySelector('#masque').style.pointerEvents = 'none'
	document.querySelector('#masque-transparent').style.pointerEvents = 'none'
	document.querySelector('#masque-video').style.pointerEvents = 'auto'
	document.querySelector('#video iframe').style.pointerEvents = 'auto'
}

function lecture () {
    if (statut !== 1) {
		document.querySelector('#masque').style.display = 'none'
		document.querySelector('#icone-lancer').style.display = 'none'
		document.querySelector('#icone-rejouer').style.display = 'none'
		document.querySelector('#icone-pause').style.display = 'inline-block'
		if (document.querySelector('#bouton-sous-titres').classList.contains('active')) {
            setTimeout(function () {
				document.querySelector('#video iframe').style.height = '100%'
				document.querySelector('#video iframe').style.top = '0'
            }, 500)
		}
		if (player.getCurrentTime() === 0 || player.getCurrentTime() >= fin) {
			player.seekTo(debut, true)
			player.playVideo()
        } else {
			player.playVideo()
		}
    } else {
		document.querySelector('#video iframe').style.height = 'calc(100% + 460px)'
		document.querySelector('#video iframe').style.top = '-230px'
		document.querySelector('#icone-lancer').style.display = 'inline-block'
		document.querySelector('#icone-pause').style.display = 'none'
		document.querySelector('#icone-rejouer').style.display = 'none'
		player.pauseVideo()
    }
}

function statutLecteurModifie (event) {
    statut = event.data
    clearTimeout(cacherSouris)
	if (statut === 1 && !controlesAffiches) { // lecture
        mettreAJourTemps = setInterval(definirTemps, 100)
        setTimeout(function () {
            document.querySelector('#masque').style.display = 'none'
			document.querySelector('#masque-transparent').classList.remove('sans-vignette')
        }, 500)
		document.querySelector('#icone-lancer').style.display = 'none'
		document.querySelector('#icone-rejouer').style.display = 'none'
		document.querySelector('#icone-pause').style.display = 'inline-block'

        cacherSouris = setTimeout(function () {
			document.body.style.cursor = 'none'
			document.querySelector('#controles').style.bottom = '-100px'
        }, 1500)
    } else if (statut === 1 && controlesAffiches) {
		mettreAJourTemps = setInterval(definirTemps, 100)
        document.querySelector('#masque').style.display = 'none'
        document.querySelector('#icone-lancer').style.display = 'none'
		document.querySelector('#icone-rejouer').style.display = 'none'
		document.querySelector('#icone-pause').style.display = 'inline-block'
		document.querySelector('#masque-transparent').classList.remove('sans-vignette')
	} else if (statut === 0) { // finie
		document.querySelector('#ecoule').textContent = document.querySelector('#duree').textContent
        document.querySelector('#masque').style.display = 'block'
		if (vignette !== '') {
			document.querySelector('#masque').classList.add('vignette')
		} else {
			document.querySelector('#masque-transparent').classList.add('sans-vignette')
		}
		document.querySelector('#icone-rejouer').style.display = 'inline-block'
		document.querySelector('#icone-lancer').style.display = 'none'
		document.querySelector('#icone-pause').style.display = 'none'
		document.querySelector('#masque-lecture').style.display = 'none'
		document.querySelector('#masque-relecture').style.display = 'block'
	} else if (statut === 2) { // en pause
		document.querySelector('#masque-lecture').style.display = 'block'
		document.querySelector('#masque-relecture').style.display = 'none'
		document.querySelector('#masque').style.display = 'block'
		if (vignette !== '') {
			document.querySelector('#masque').classList.remove('vignette')
		} else {
			document.querySelector('#masque-transparent').classList.remove('sans-vignette')
		}
    } else {
        clearInterval(mettreAJourTemps)
    }

    if (statut !== 1) {
        document.body.style.cursor = ''
        document.querySelector('#controles').style.bottom = '0'
    }
	// Résolution bug iframe Chrome
	if (statut === 1 && navChrome === true && videoLancee === false) {
		videoLancee = true
		document.querySelector('#masque').style.pointerEvents = 'auto'
		document.querySelector('#masque-transparent').style.pointerEvents = 'auto'
		document.querySelector('#masque-video').style.pointerEvents = 'none'
		document.querySelector('#video iframe').style.pointerEvents = 'none'
	}
}

function definirVolume (vol) {
	if (vol !== -1) {
		volume.setAttribute('data-volume', vol)
		document.querySelector('#niveau').style.width = vol + '%'
    	player.setVolume(vol)
	} else {
		niveauVolume = volume.getAttribute('data-volume')
		player.setVolume(niveauVolume)
		if (niveauVolume === 0) {
			document.querySelector('#bouton-volume').setAttribute('data-statut', 'off')
			document.querySelector('#icone-muet').style.display = 'inline-block'
			document.querySelector('#icone-volume').style.display = 'none'
		} else {
			document.querySelector('#bouton-volume').setAttribute('data-statut', 'on')
			document.querySelector('#icone-muet').style.display = 'none'
			document.querySelector('#icone-volume').style.display = 'inline-block'
		}
	}
}

function definirStatutVolume () {
    if (document.querySelector('#bouton-volume').getAttribute('data-statut') === 'on') {
        definirVolume(0)
		document.querySelector('#bouton-volume').setAttribute('data-statut', 'off')
        document.querySelector('#icone-muet').style.display = 'inline-block'
		document.querySelector('#icone-volume').style.display = 'none'
    } else {
        definirVolume(niveauVolume)
        document.querySelector('#bouton-volume').setAttribute('data-statut', 'on')
		document.querySelector('#icone-muet').style.display = 'none'
		document.querySelector('#icone-volume').style.display = 'inline-block'
    }
}

document.querySelector('#masque-transparent').addEventListener('mousemove', gererControles, false)
document.querySelector('#masque').addEventListener('mousemove', gererControles, false)

function gererControles () {
    document.body.style.cursor = ''
    document.querySelector('#controles').style.bottom = '0'
    clearTimeout(cacherSouris)
    if (statut === 1 && !controlesAffiches) {
        cacherSouris = setTimeout(function () {
			document.body.style.cursor = 'none'
			document.querySelector('#controles').style.bottom = '-100px'
        }, 1500)
    }
}

document.querySelector('#controles').addEventListener('mouseover', function () {
	clearTimeout(cacherSouris)
    controlesAffiches = true
})

document.querySelector('#controles').addEventListener('mouseout', function () {
	clearTimeout(cacherSouris)
    controlesAffiches = false
	if (statut === 1 && !controlesAffiches) {
        cacherSouris = setTimeout(function () {
			document.body.style.cursor = 'none'
			document.querySelector('#controles').style.bottom = '-100px'
        }, 1500)
    }
})

function ouvrirPleinEcran () {
    if (pleinEcran) {
        fermerPleinEcran()
        return
    }
    if (conteneurVideo.requestFullscreen) {
        conteneurVideo.requestFullscreen()
	} else if (conteneurVideo.mozRequestFullScreen) {
        conteneurVideo.mozRequestFullScreen()
	} else if (conteneurVideo.webkitRequestFullscreen) {
        conteneurVideo.webkitRequestFullscreen()
	} else if (conteneurVideo.msRequestFullscreen) {
        conteneurVideo.msRequestFullscreen()
		conteneurVideo.classList.add('plein-ecran')
    } else {
        conteneurVideo.classList.add('plein-ecran-ios')
	}
    pleinEcran = true
	document.querySelector('#icone-plein-ecran').style.display = 'none'
	document.querySelector('#icone-sortir-plein-ecran').style.display = 'inline-block'
}

function fermerPleinEcran () {
    if (document.exitFullscreen) {
        document.exitFullscreen()
    } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen()
    } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen()
    } else if (document.msExitFullscreen) {
        document.msExitFullscreen()
        conteneurVideo.classList.remove('plein-ecran')
    } else {
        conteneurVideo.classList.remove('plein-ecran-ios')
	}
    pleinEcran = false
	document.querySelector('#icone-plein-ecran').style.display = 'inline-block'
	document.querySelector('#icone-sortir-plein-ecran').style.display = 'none'
}

document.addEventListener('webkitfullscreenchange', gererSortiePleinEcran, false)
document.addEventListener('mozfullscreenchange', gererSortiePleinEcran, false)
document.addEventListener('fullscreenchange', gererSortiePleinEcran, false)
document.addEventListener('MSFullscreenChange', gererSortiePleinEcran, false)

function gererSortiePleinEcran () {
    if (!document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
        conteneurVideo.classList.remove('plein-ecran-ios')
		pleinEcran = false
		document.querySelector('#icone-plein-ecran').style.display = 'inline-block'
		document.querySelector('#icone-sortir-plein-ecran').style.display = 'none'
    }
}

Date.prototype.addMilliseconds = function (value) {
    this.setMilliseconds(this.getMilliseconds() + value)
    return this
}

Date.prototype.addSeconds = function (value) {
    return this.addMilliseconds(value * 1000)
}

Date.prototype.toString = function (format) {
    const self = this
    const p = function p (s) {
        return (s.toString().length == 1) ? '0' + s : s
    }
    return format ? format.replace(/HH?|mm?|ss?/g, function (format) {
        switch (format) {
            case 'H':
                return self.getHours()
            case 'mm':
                return p(self.getMinutes())
            case 'ss':
                return p(self.getSeconds())

        }
    }) : this._toString()
}

Date.prototype.clearTime = function () {
    this.setHours(0)
    this.setMinutes(0)
    this.setSeconds(0)
    this.setMilliseconds(0)
    return this
}

Date.prototype._toString = Date.prototype.toString

function formatTime (t) {
    t = new Date().clearTime().addSeconds(t).toString('H:mm:ss')
    if (t.substring(0, 2) == '0:')
        t = t.substring(2, t.length)
    return t
}
