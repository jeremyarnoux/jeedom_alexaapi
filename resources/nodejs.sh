#!/bin/bash

PROGRESS_FILE=/tmp/jeedom/${2}/dependance
installVer='12' 	#NodeJS major version to be installed
minVer='12'	#min NodeJS major version to be accepted

touch ${PROGRESS_FILE}
echo 0 > ${PROGRESS_FILE}
echo "--0%"
if [ "$3" = "1" ]; then
  date +'--[%Y/%m/%d %H:%M:%S]-Mode Debug--'
fi
BASEDIR=$1
DIRECTORY="/var/www"
if [ ! -d "$DIRECTORY" ]; then
  echo "Création du home www-data pour npm"
  sudo mkdir $DIRECTORY
fi
sudo chown -R www-data $DIRECTORY

echo 10 > ${PROGRESS_FILE}
echo "--10%"
echo "Lancement de l'installation/mise à jour des dépendances"

if [ -f /etc/apt/sources.list.d/deb-multimedia.list* ]; then
  echo "Vérification si la source deb-multimedia existe (bug lors du apt-get update si c'est le cas)"
  echo "deb-multimedia existe !"
  if [ -f /etc/apt/sources.list.d/deb-multimedia.list.disabledBy${2} ]; then
    echo "mais on l'a déjà désactivé..."
  else
    if [ -f /etc/apt/sources.list.d/deb-multimedia.list ]; then
      echo "Désactivation de la source deb-multimedia !"
      sudo mv /etc/apt/sources.list.d/deb-multimedia.list /etc/apt/sources.list.d/deb-multimedia.list.disabledBy${2}
    else
      if [ -f /etc/apt/sources.list.d/deb-multimedia.list.disabled ]; then
        echo "mais il est déjà désactivé..."
      else
        echo "mais n'est ni 'disabled' ou 'disabledBy${2}'... il sera normalement ignoré donc ca devrait passer..."
      fi
    fi
  fi
fi

if [ -f /etc/apt/sources.list.d/jeedom.list* ]; then
  if [ -f /media/boot/multiboot/meson64_odroidc2.dtb.linux ]; then
    echo "Smart détectée, migration du repo NodeJS"
    sudo wget --quiet -O - http://repo.jeedom.com/odroid/conf/jeedom.gpg.key | sudo apt-key add -
    sudo rm -rf /etc/apt/sources.list.d/jeedom.list*
    sudo apt-add-repository "deb http://repo.jeedom.com/odroid/ stable main"
  else
    echo "Vérification si la source repo.jeedom.com existe (bug sur mini+)"
    echo "repo.jeedom.com existe !"
    if [ -f /etc/apt/sources.list.d/jeedom.list.disabledBy${2} ]; then
      echo "mais on l'a déjà désactivé..."
    else
      if [ -f /etc/apt/sources.list.d/jeedom.list ]; then
        echo "Désactivation de la source repo.jeedom.com !"
        sudo mv /etc/apt/sources.list.d/jeedom.list /etc/apt/sources.list.d/jeedom.list.disabledBy${2}
      else
        if [ -f /etc/apt/sources.list.d/jeedom.list.disabled ]; then
  	  echo "mais il est déjà désactivé..."
        else
	  echo "mais n'est ni 'disabled' ou 'disabledBy${2}'... il sera normalement ignoré donc ca devrait passer..."
        fi
      fi
    fi
  fi
fi

#prioritize nodesource nodejs
sudo bash -c "cat >> /etc/apt/preferences.d/nodesource" << EOL
Package: nodejs
Pin: origin deb.nodesource.com
Pin-Priority: 600
EOL

echo 20 > ${PROGRESS_FILE}
echo "--20%"
sudo apt-get update
sudo DEBIAN_FRONTEND=noninteractive apt-get install -y lsb-release

echo 30 > ${PROGRESS_FILE}
echo "--30%"
type nodejs &>/dev/null
if [ $? -eq 0 ]; then actual=`nodejs -v`; fi
echo "Version actuelle : ${actual}"
arch=`arch`

if [[ $arch == "armv6l" ]]
then
  installVer='8' 	#NodeJS major version to be installed
  minVer='8'	#min NodeJS major version to be accepted  
fi

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
  echo 40 > ${PROGRESS_FILE}
  echo "--40%"
  echo "KO, version obsolète à upgrader";
  echo "Suppression du Nodejs existant et installation du paquet recommandé"
  #if npm exists
  type npm &>/dev/null
  if [ $? -eq 0 ]; then
    cd `npm root -g`;
    sudo npm rebuild &>/dev/null
    npmPrefix=`npm prefix -g`
  else
    npmPrefix="/usr"
  fi
  sudo DEBIAN_FRONTEND=noninteractive apt-get -y --purge autoremove npm
  sudo DEBIAN_FRONTEND=noninteractive apt-get -y --purge autoremove nodejs

  echo 45 > ${PROGRESS_FILE}
  echo "--45%"
  if [[ $arch == "armv6l" ]]
  then
    echo "Raspberry 1, 2 ou zéro détecté, utilisation du paquet v${installVer} pour ${arch}"
    wget -nd -nH -nc -np -e robots=off -r -l1 --no-parent -A"node-*-linux-${arch}.tar.gz" https://nodejs.org/download/release/latest-v${installVer}.x/
    tar -xvf node-*-linux-${arch}.tar.gz
    cd node-*-linux-${arch}
    sudo cp -R * /usr/local/
    cd ..
    rm -fR node-*-linux-${arch}*
    ln -s /usr/local/bin/node /usr/bin/node &>/dev/null
    ln -s /usr/local/bin/node /usr/bin/nodejs &>/dev/null
    #upgrade to recent npm
    sudo npm install -g npm
  else
      echo "Utilisation du dépot officiel"
      curl -sL https://deb.nodesource.com/setup_${installVer}.x | sudo -E bash -
      sudo DEBIAN_FRONTEND=noninteractive apt-get install -y nodejs  
  fi

  npm config set prefix ${npmPrefix}

  new=`nodejs -v`;
  echo "Version actuelle : ${new}"
fi

type npm &>/dev/null
if [ $? -ne 0 ]; then
  sudo DEBIAN_FRONTEND=noninteractive apt-get install -y npm  
  sudo npm install -g npm
fi

echo 50 > ${PROGRESS_FILE}
echo "--50%"
cd ${BASEDIR};
#remove old local modules
sudo rm -rf node_modules

echo 60 > ${PROGRESS_FILE}
echo "--60%"
echo "Installation..."
if [ "$3" = "1" ]; then
  sudo npm install --verbose
else
  sudo npm install
fi
sudo chown -R www-data node_modules

echo 95 > ${PROGRESS_FILE}
echo "--95%"
sudo rm -f /etc/apt/preferences.d/nodesource

if [ -f /etc/apt/sources.list.d/deb-multimedia.list.disabledBy${2} ]; then
  echo "Réactivation de la source deb-multimedia qu'on avait désactivé !"
  sudo mv /etc/apt/sources.list.d/deb-multimedia.list.disabledBy${2} /etc/apt/sources.list.d/deb-multimedia.list
fi
if [ -f /etc/apt/sources.list.d/jeedom.list.disabledBy${2} ]; then
  echo "Réactivation de la source repo.jeedom.com qu'on avait désactivé !"
  sudo mv /etc/apt/sources.list.d/jeedom.list.disabledBy${2} /etc/apt/sources.list.d/jeedom.list
fi

echo 100 > ${PROGRESS_FILE}
echo "--100%"
echo "Installation des dépendances ${2} terminée, vérifiez qu'il n'y a pas d'erreur"
if [ "$3" = "1" ]; then
  date +'--[%Y/%m/%d %H:%M:%S]--'
fi
rm -f ${PROGRESS_FILE}
