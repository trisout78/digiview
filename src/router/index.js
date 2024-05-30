import { createRouter, createWebHashHistory } from 'vue-router'
import Accueil from '../views/Accueil.vue'
import Video from '../views/Video.vue'

const routes = [
	{
		path: '/',
		name: 'Accueil',
		component: Accueil
	},
	{
		path: '/v/:id',
		name: 'Video',
		component: Video
	}
]

const router = createRouter({
	history: createWebHashHistory(),
	routes
})

export default router
