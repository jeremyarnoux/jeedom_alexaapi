#!/bin/bash
cd $1
touch /tmp/${2}_dep
echo "Début de l'installation"

echo 0 > /tmp/${2}_dep
#echo "Suppression du dossier : alexaapi/ressources/alexa-remote-http"
#rm alexa-remote-http -r
echo "Récupération de la derniere version de alexa-remote-http"
git clone https://github.com/sx-1/alexa-remote-http.git
cd alexa-remote-http
cp index.js alexaapi.js
echo "Installation npm"
npm install
rm /tmp/${2}_dep

echo "Fin de l'installation"