# jeedom_alexaapi

Pour installer manuellement ce plugin, en ligne de commande :

cd /var/www/html/plugins

git clone https://github.com/sigalou/jeedom_alexaapi

mv jeedom_alexaapi alexaapi

chown -R www-data:www-data alexaapi

Puis allez dans Jeedom / Plugins / Gestion des plugins

Allez sur Alexa-API

Activer le.


Installer les dépendances, si tout se passe bien, vous aurez dans le log "Alexaapi_dep" :


Début de l'installation
Suppression du dossier : alexaapi/ressources/alexa-remote-http
Récupération de la derniere version de alexa-remote-http
Cloning into 'alexa-remote-http'...
Installation npm
npm notice created a lockfile as package-lock.json. You should commit this file.
added 179 packages from 143 contributors and audited 1172 packages in 24.35s
found 0 vulnerabilities
Fin de l'installation

Attention, à compter de là, pour lancer le serveur, il faut le JSON cookie d'Alexa dans alexa-cookie.json
