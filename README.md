# This project is a [Digiview fork](https://codeberg.org/ladigitale/digiview)


# TriView

TriView est une application en ligne pour visionner des vidéos YouTube dans une interface sans distraction. 

Elle est publiée sous licence GNU AGPLv3.
Sauf les fontes Roboto Slab et Material Icons (Apache License Version 2.0) et la fonte HKGrotesk (Sil Open Font Licence 1.1)

### Préparation et installation des dépendances
```
npm install
```

### Lancement du serveur de développement
```
npm run dev
```

### Variables d'environnement (fichier .env.production à créer à la racine avant compilation)
```
AUTHORIZED_DOMAINS (liste des domaines autorisés pour les requêtes POST et l'API, séparés par une virgule / * par défaut)
VITE_GOOGLE_API (Google API pour YouTube pour récupérer la durée de la vidéo)
```

### Compilation et minification des fichiers
```
npm run build
```

### Serveur PHP nécessaire pour l'API
```
php -S 127.0.0.1:8000 (pour le développement uniquement)
```

### Production
Le dossier dist peut être déployé directement sur un serveur PHP avec l'extension SQLite activée. Cette version compilée n'intègre pas l'API Google pour YouTube.
