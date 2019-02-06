#!/bin/bash
cd $1
touch /tmp/${2}_dep
echo "Début de l'installation"

echo 0 > /tmp/${2}_dep
echo "Installation npm"

sudo chown -R $(whoami) ~/.npm/_locks
npm install
rm /tmp/${2}_dep

echo "Fin de l'installation"