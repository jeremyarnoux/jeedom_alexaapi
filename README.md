# jeedom_alexaapi

Pour installer manuellement ce plugin, en ligne de commande :

cd /var/www/html/plugins

git clone https://github.com/sigalou/jeedom_alexaapi

mv jeedom_alexaapi alexaapi

chown -R www-data:www-data alexaapi

Puis allez dans Jeedom / Plugins / Gestion des plugins

Allez sur Alexa-API

Activer le.


Important : Ré-Installer les dépendances

Allez sur Lancer la génération pour générer le Cookie Amazon, il suffit de suivre les étapes.

Pour l'instant, le développement est arrivé à ce point.
A ce stade, une fois le Cookie généré, et le démon lancé, vous pouvez tester dans votre navigateur avec une commande du genre :

http://VOTREIP:3456/speak?device=VOTREDEVICE&text=coucou 
