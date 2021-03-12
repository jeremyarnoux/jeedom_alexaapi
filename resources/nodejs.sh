#!/bin/bash
######################### INCLUSION LIB ##########################
BASEDIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
wget https://raw.githubusercontent.com/NebzHB/dependance.lib/master/dependance.lib -O $BASEDIR/dependance.lib &>/dev/null
PLUGIN=$(basename "$(realpath $BASEDIR/..)")
. ${BASEDIR}/dependance.lib
##################################################################
wget https://raw.githubusercontent.com/NebzHB/nodejs_install/main/install_nodejs.sh -O $BASEDIR/install_nodejs.sh &>/dev/null
TIMED=1

installVer='12' 	#NodeJS major version to be installed

pre
step 0 "Vérification des droits"
DIRECTORY="/var/www"
if [ ! -d "$DIRECTORY" ]; then
	silent sudo mkdir $DIRECTORY
fi
silent sudo chown -R www-data $DIRECTORY

step 5 "Mise à jour APT et installation des packages nécessaires"
try sudo apt-get update

#install nodejs, steps 10->50
. ${BASEDIR}/install_nodejs.sh ${installVer}

step 60 "Nettoyage ancien modules"
cd ${BASEDIR};
#remove old local modules
sudo rm -rf node_modules &>/dev/null
sudo rm -f package-lock.json &>/dev/null

step 70 "Installation des librairies, veuillez patienter svp"
silent sudo mkdir node_modules
silent sudo chown -R www-data:www-data . 
try sudo npm install --no-fund --no-package-lock --no-audit
silent sudo chown -R www-data:www-data .

post
