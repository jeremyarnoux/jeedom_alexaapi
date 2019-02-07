# jeedom_alexaapi

Pour installer manuellement ce plugin :

jeedom -> plugins -> gestion des plugins -> ajouter un plugin -> type de source: github
(Si github n'est pas proposé, allez dans Admin/Configuration/Mise à jour/Github et cochez "Activer Github"

ID logique du plugin -> alexaapi

Utilisateur ou organisation du dépôt -> sigalou

Nom du dépôt -> jeedom_alexaapi

Branche -> master

Allez sur Lancer la génération pour générer le Cookie Amazon, il suffit de suivre les étapes.

Pour l'instant, le développement est arrivé à ce point.
A ce stade, une fois le Cookie généré, et le démon lancé, vous pouvez tester dans votre navigateur avec une commande du genre :

http://VOTREIP:3456/speak?device=VOTREDEVICE&text=coucou 
