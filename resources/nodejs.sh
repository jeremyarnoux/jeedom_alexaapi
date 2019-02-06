#!/bin/bash
cd $1
echo "Début de l'installation"

echo 0 > /tmp/${2}_dep
BIN=`which node`
if [ "$BIN" = "" ]; then
  BIN=`which nodejs`
fi
if [ "$BIN" = "" ]; then
  echo "No nodejs found"
  major=0
else
  actual=`$BIN -v`
  major=`$BIN -v | sed "s#v##" | sed "s#[.].*##"`
  echo "Current version: ${actual} (major $major)"
fi

echo 10 > /tmp/${2}_dep
if [ `arch` == "armv6l" -a $major -lt 5 ]; then
  echo "Raspberry 1 detected, using armv6 package"
  sudo npm rebuild
  sudo apt-get -y --purge autoremove nodejs npm
  sudo rm /etc/apt/sources.list.d/nodesource.list
  wget http://node-arm.herokuapp.com/node_latest_armhf.deb
  sudo dpkg -i node_latest_armhf.deb
  rm node_latest_armhf.deb
elif [ $major -lt 8 ]; then
  echo "using official repository"
  curl -sL https://deb.nodesource.com/setup_8.x | sudo -E bash -
  sudo apt-get install -y nodejs npm
fi

BIN=`which node`
if [ "$BIN" = "" ]; then
  BIN=`which nodejs`
fi
new=`$BIN -v`;
echo "new version installed: ${new}"

echo 60 > /tmp/${2}_dep
echo "Récupération de la derniere version de alexa-remote-http"
rm -r alexa-remote-http
git clone https://github.com/sx-1/alexa-remote-http.git
cd alexa-remote-http
ln -s index.js alexaapi.js
ln -s ../cookie.js .
echo "Installation npm"
npm install
rm /tmp/${2}_dep

echo "Fin de l'installation"