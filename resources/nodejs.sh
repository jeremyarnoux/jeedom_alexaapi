#!/bin/bash

######################### INCLUSION LIB ##########################
BASEDIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
wget https://raw.githubusercontent.com/NebzHB/dependance.lib/master/dependance.lib -O $BASEDIR/dependance.lib &>/dev/null
PLUGIN=$(basename "$(realpath $BASEDIR/..)")
. ${BASEDIR}/dependance.lib
##################################################################
TIMED=1

installVer='12' 	#NodeJS major version to be installed
minVer='12'	#min NodeJS major version to be accepted

pre
step 0 "Vérification des droits"
DIRECTORY="/var/www"
if [ ! -d "$DIRECTORY" ]; then
  silent sudo mkdir $DIRECTORY
fi
silent sudo chown -R www-data $DIRECTORY

step 10 "Prérequis"
if [ -f /etc/apt/sources.list.d/deb-multimedia.list* ]; then
  echo "Vérification si la source deb-multimedia existe (bug lors du apt-get update si c'est le cas)"
  echo "deb-multimedia existe !"
  if [ -f /etc/apt/sources.list.d/deb-multimedia.list.disabledBy${PLUGIN} ]; then
    echo "mais on l'a déjà désactivé..."
  else
    if [ -f /etc/apt/sources.list.d/deb-multimedia.list ]; then
      echo "Désactivation de la source deb-multimedia !"
      silent sudo mv /etc/apt/sources.list.d/deb-multimedia.list /etc/apt/sources.list.d/deb-multimedia.list.disabledBy${PLUGIN}
    else
      if [ -f /etc/apt/sources.list.d/deb-multimedia.list.disabled ]; then
        echo "mais il est déjà désactivé..."
      else
        echo "mais n'est ni 'disabled' ou 'disabledBy${PLUGIN}'... il sera normalement ignoré donc ca devrait passer..."
      fi
    fi
  fi
fi

toReAddRepo=0
if [ -f /media/boot/multiboot/meson64_odroidc2.dtb.linux ]; then
    hasRepo=$(grep "repo.jeedom.com" /etc/apt/sources.list | wc -l)
    if [ "$hasRepo" -ne "0" ]; then
      echo "Désactivation de la source repo.jeedom.com !"
      toReAddRepo=1
      silent sudo apt-add-repository -r "deb http://repo.jeedom.com/odroid/ stable main"
    fi
fi


#prioritize nodesource nodejs
try sudo bash -c "cat >> /etc/apt/preferences.d/nodesource" << EOL
Package: nodejs
Pin: origin deb.nodesource.com
Pin-Priority: 600
EOL

step 20 "Mise à jour APT et installation des packages nécessaires"
try sudo apt-get update
try sudo DEBIAN_FRONTEND=noninteractive apt-get install -y lsb-release

step 30 "Vérification de la version de NodeJS installée"
silent type nodejs
if [ $? -eq 0 ]; then actual=`nodejs -v`; fi
echo "Version actuelle : ${actual}"
arch=`arch`

#jeedom mini and rpi 1 2, 12 does not support arm6l
if [[ $arch == "armv6l" ]]
then
  installVer='8' 	#NodeJS major version to be installed
  minVer='8'	#min NodeJS major version to be accepted  
fi

#jessie as libstdc++ > 4.9 needed for nodejs 12
lsb_release -c | grep jessie
if [ $? -eq 0 ]
then
  installVer='8' 	#NodeJS major version to be installed
  minVer='8'	#min NodeJS major version to be accepted  
fi

bits=`getconf LONG_BIT`
vers=`lsb_release -c | grep stretch | wc -l`
if { [ "$arch" = "i386" ]; } && [ "$bits" -eq "32" ] && [ "$vers" -eq "1" ]
then 
  installVer='8' 	#NodeJS major version to be installed
  minVer='8'	#min NodeJS major version to be accepted  
fi

testVer=`php -r "echo version_compare('${actual}','v${minVer}','>=');"`
if [[ $testVer == "1" ]]
then
  echo "Ok, version suffisante";
  new=$actual
else
  step 40 "Installation de NodeJS $installVer"
  echo "Version obsolète à upgrader";
  echo "Suppression du Nodejs existant et installation du paquet recommandé"
  #if npm exists
  silent type npm
  if [ $? -eq 0 ]; then
    cd `npm root -g`;
    silent sudo npm rebuild
    npmPrefix=`npm prefix -g`
  else
    npmPrefix="/usr"
  fi
  silent sudo DEBIAN_FRONTEND=noninteractive apt-get -y --purge autoremove npm
  silent sudo DEBIAN_FRONTEND=noninteractive apt-get -y --purge autoremove nodejs

  if [[ $arch == "armv6l" ]]
  then
    echo "Raspberry 1, 2 ou zéro détecté, utilisation du paquet v${installVer} pour ${arch}"
    try wget -nd -nH -nc -np -e robots=off -r -l1 --no-parent -A"node-*-linux-${arch}.tar.gz" https://nodejs.org/download/release/latest-v${installVer}.x/
    try tar -xvf node-*-linux-${arch}.tar.gz
    cd node-*-linux-${arch}
    try sudo cp -R * /usr/local/
    cd ..
    silent rm -fR node-*-linux-${arch}*
    silent ln -s /usr/local/bin/node /usr/bin/node
    silent ln -s /usr/local/bin/node /usr/bin/nodejs
    #upgrade to recent npm
    try sudo npm install -g npm
  else
      echo "Utilisation du dépot officiel"
      curl -sL https://deb.nodesource.com/setup_${installVer}.x | try sudo -E bash -
      try sudo DEBIAN_FRONTEND=noninteractive apt-get install -y nodejs  
  fi

  silent npm config set prefix ${npmPrefix}

  new=`nodejs -v`;
  echo "Version actuelle : ${new}"
fi

silent type npm
if [ $? -ne 0 ]; then
  step 45 "Installation de npm car non présent"
  try sudo DEBIAN_FRONTEND=noninteractive apt-get install -y npm  
  try sudo npm install -g npm
fi

step 50 "Nettoyage ancien modules"
cd ${BASEDIR};
#remove old local modules
silent sudo rm -rf node_modules

step 60 "Installation des librairies, veuillez patienter svp"
try sudo npm install --no-fund --no-package-lock --no-audit
silent sudo chown -R www-data node_modules

step 90 "Nettoyage"
silent sudo rm -f /etc/apt/preferences.d/nodesource

if [ -f /etc/apt/sources.list.d/deb-multimedia.list.disabledBy${PLUGIN} ]; then
  echo "Réactivation de la source deb-multimedia qu'on avait désactivé !"
  silent sudo mv /etc/apt/sources.list.d/deb-multimedia.list.disabledBy${PLUGIN} /etc/apt/sources.list.d/deb-multimedia.list
fi

if [ "$toReAddRepo" -ne "0" ]; then
  echo "Réactivation de la source repo.jeedom.com qu'on avait désactivé !"
  toReAddRepo=0
  silent sudo apt-add-repository "deb http://repo.jeedom.com/odroid/ stable main"
fi

post
