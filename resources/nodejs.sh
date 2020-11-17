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
      sudo apt-add-repository -r "deb http://repo.jeedom.com/odroid/ stable main"
    fi
fi

#prioritize nodesource nodejs
sudo bash -c "cat >> /etc/apt/preferences.d/nodesource" << EOL
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

#jeedom mini and rpi 1 2, NodeJS 12 does not support arm6l
#if [[ $arch == "armv6l" ]]
#then
#  echo "$HR"
#  echo "== KO == Erreur d'Installation"
#  echo "$HR"
#  echo "== ATTENTION Vous possédez une Jeedom mini ou Raspberry zero/1/2 (arm6l) et NodeJS 12 n'y est pas supporté, merci d'utiliser du matériel récent !!!"
#  exit 1
#fi

#jessie as libstdc++ > 4.9 needed for nodejs 12
lsb_release -c | grep jessie
if [ $? -eq 0 ]
then
  today=$(date +%Y%m%d)
  if [[ "$today" > "20200630" ]]; 
  then 
    echo "$HR"
    echo "== KO == Erreur d'Installation"
    echo "$HR"
    echo "== ATTENTION Debian 8 Jessie n'est officiellement plus supportée depuis le 30 juin 2020, merci de mettre à jour votre distribution !!!"
    exit 1
  fi
fi

bits=$(getconf LONG_BIT)
vers=$(lsb_release -c | grep stretch | wc -l)
if { [ "$arch" = "i386" ] || [ "$arch" = "i686" ]; } && [ "$bits" -eq "32" ]
then 
  echo "$HR"
  echo "== KO == Erreur d'Installation"
  echo "$HR"
  echo "== ATTENTION Votre système est x86 en 32bits et NodeJS 12 n'y est pas supporté, merci de passer en 64bits !!!"
  exit 1 
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
    echo "Jeedom Mini ou Raspberry 1, 2 ou zéro détecté, non supporté mais on essaye l'utilisation du paquet non-officiel v12.19.0 pour armv6l"
    try wget https://unofficial-builds.nodejs.org/download/release/v12.19.0/node-v12.19.0-linux-armv6l.tar.gz
    try tar -xvf node-v12.19.0-linux-armv6l.tar.gz
    cd node-v12.19.0-linux-armv6l
    try sudo cp -f -R * /usr/local/
    cd ..
    silent rm -fR node-v12.19.0-linux-armv6l*
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
  echo "Version après install : ${new}"
  testVerAfter=$(php -r "echo version_compare('${new}','v${minVer}','>=');")
  if [[ $testVerAfter != "1" ]]
  then
    echo "Version non suffisante, relancez les dépendances"
  fi
fi

silent type npm
if [ $? -ne 0 ]; then
  step 45 "Installation de npm car non présent"
  try sudo DEBIAN_FRONTEND=noninteractive apt-get install -y npm  
  try sudo npm install -g npm
fi

npmPrefix=`npm prefix -g`
npmPrefixSudo=`sudo npm prefix -g`
npmPrefixwwwData=`sudo -u www-data npm prefix -g`
echo -n "[Check Prefix : $npmPrefix and sudo prefix : $npmPrefixSudo and www-data prefix : $npmPrefixwwwData : "
if [[ "$npmPrefixSudo" != "/usr" ]] && [[ "$npmPrefixSudo" != "/usr/local" ]]; then 
  echo "[  KO  ]"
  if [[ "$npmPrefixwwwData" == "/usr" ]] || [[ "$npmPrefixwwwData" == "/usr/local" ]]; then
    step 48 "Reset prefix ($npmPrefixwwwData) pour npm `sudo whoami`"
    sudo npm config set prefix $npmPrefixwwwData
  else
    if [[ "$npmPrefix" == "/usr" ]] || [[ "$npmPrefix" == "/usr/local" ]]; then
      step 48 "Reset prefix ($npmPrefix) pour npm `sudo whoami`"
      sudo npm config set prefix $npmPrefix
    else
      step 48 "Reset prefix (/usr) pour npm `sudo whoami`"
      sudo npm config set prefix /usr
    fi
  fi  
else
  echo "[  OK  ]"
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
