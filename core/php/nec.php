<?php

error_reporting(E_ALL);

#
# Fichier : nec.php (Nombres En Chiffres)
#
# Auteur : Olivier Miakinen
# Création : vendredi 18 avril 2003
# Dernière modification : vendredi 16 mai 2003
#
# La fonction enchiffres($nom) reçoit en entrée le nom d'un nombre
# écrit en toutes lettres et l'écrit en chiffres.
#
# Toute la documentation sur trouve sur :
#  http://www.miakinen.net/vrac/nombres
# Un programme de test existe sur :
#  http://www.miakinen.net/vrac/nombres2
#

function enchiffres_petit($mot)
{
 static $petit = array(
   # 1-16
   'un'        => 1,
   'deux'      => 2,
   'trois'     => 3,
   'quatre'    => 4,
   'cinq'      => 5,
   'six'       => 6,
   'sept'      => 7,
   'huit'      => 8,
   'neuf'      => 9,
   'dix'       => 10,
   'onze'      => 11,
   'douze'     => 12,
   'treize'    => 13,
   'quatorze'  => 14,
   'quinze'    => 15,
   'seize'     => 16,
   # 20-90
   'vingt'     => 20,
   'vingts'    => 20,
   'trente'    => 30,
   'quarante'  => 40,
   'cinquante' => 50,
   'soixante'  => 60,
   'septante'  => 70,
   'huitante'  => 80,
   'octante'   => 80,
   'nonante'   => 90,
   # 100, 1000
   'cent'      => 100,
   'cents'     => 100,
   'mil'       => 1000,
   'mille'     => 1000
 );

 if (! isset($petit[$mot]))
  return false;

 return $petit[$mot];
}

function enchiffres_zilli($mot)
{
 # Noms des 0ème à 9ème zillions
 static $petits = array(
    'n', 'm', 'b', 'tr', 'quadr', 'quint', 'sext', 'sept', 'oct', 'non'
 );
 # Composantes des 10ème à 999ème zillions
 static $unites = array(
    '', 'un', 'duo', 'tre', 'quattuor', 'quin', 'se', 'septe', 'octo', 'nove'
 );
 static $dizaines = array(
    '', 'dec', 'vigint', 'trigint', 'quadragint',
    'quinquagint', 'sexagint', 'septuagint', 'octogint', 'nonagint'
 );
 static $centaines = array(
    '', 'cent', 'ducent', 'trecent', 'quadringent',
    'quingent', 'sescent', 'septingent', 'octingent', 'nongent'
 );
 # Expressions rationnelles pour extraire les composantes
 static $um =
    '(|un|duo|tre(?!c)|quattuor|quin|se(?!p)(?!sc)|septe|octo|nove)[mnsx]?';
 static $dm =
    '(|dec|(?:v|tr|quadr|quinqu|sex|septu|oct|non)[aio]gint)[ai]?';
 static $cm =
    '(|(?:|du|tre|ses)cent|(?:quadri|qui|septi|octi|no)ngent)';

 $u = array_search($mot, $petits);
 if ($u !== false) {
  return '00' . $u;
 }

 if (preg_match('/^'.$um.$dm.$cm.'$/', $mot, $resultat) < 1) {
  return false;
 }
 $u = array_search($resultat[1], $unites);
 $d = array_search($resultat[2], $dizaines);
 $c = array_search($resultat[3], $centaines);
 if ($u === false or $d === false or $c === false) {
  return false;
 }

 return $c.$d.$u;
}

function enchiffres_grand($mot)
{
 # Quelques remplacements initiaux pour simplifier (les 'é' ont déjà
 # été tous transformés en 'e').
 # (1) Je supprime le 's' final de '-illions' ou '-illiards' pour ne
 #     tester que '-illion' ou '-illiard'.
 # (2) Les deux orthographes étant possibles pour quadrillion ou
 #     quatrillion, je teste les deux. Noter que j'aurais pu changer
 #     'quadr' en 'quatr' au lieu de l'inverse, mais alors cela aurait
 #     aussi changé 'quadragintillion' en 'quatragintillion', ce qui
 #     n'est pas franchement le but recherché.
 # (3) En latin, on trouve parfois 'quatuor' au lieu de 'quattuor'. De même,
 #     avec google on trouve quelques 'quatuordecillions' au milieu des
 #     'quattuordecillions' (environ 1 sur 10).
 # (4) La règle de John Conway et Allan Wechsler préconisait le préfixe
 #     'quinqua' au lieu de 'quin' que j'ai choisi. Pour accepter les deux,
 #     je remplace 'quinqua' par 'quin', sauf dans 'quinquaginta' (50)
 #     et dans 'quinquadraginta' (45).
 static $recherche = array(
  '/s$/',                   # (1)
  '/quatr/',                # (2)
  '/quatuor/',              # (3)
  '/quinqua(?!(dra)?gint)/' # (4)
 );
 static $remplace = array(
  '',                       # (1)
  'quadr',                  # (2)
  'quattuor',               # (3)
  'quin'                    # (4)
 );

 $mot = preg_replace($recherche, $remplace, $mot);
 if ($mot == 'millier') return 1;
 if ($mot == 'millinillion') return 2000;

 $prefixes = explode('illi', $mot);
 if (count($prefixes) < 2) {
  # Il faut au moins un 'illi' dans le nom
  return false;
 }
 switch (array_pop($prefixes)) {
 case 'on':
  # zillion : nombre pair de milliers
  $ard = 0;
  break;
 case 'ard':
  # zilliard : nombre impair de milliers
  $ard = 1;
  break;
 default:
  # Ce n'est ni un zillion, ni un zilliard
  return false;
 }

 $nombre = '';
 foreach ($prefixes as $prefixe) {
  $par3 = enchiffres_zilli($prefixe);
  if ($par3 === false) return false;
  $nombre .= $par3;
 }
 if (strlen($nombre) > 3) {
  # On n'accepte que les nombres inférieurs au millinillion
  # pour limiter le temps de calcul
  return 0;
 }
 return 2*$nombre + $ard;
}

class enchiffres_struct
{
 var $valeur;
 var $discr;

 function enchiffres_struct($mul, $val)
 {
  $this->valeur = $this->discr = $val;
  if ($mul != 0) {
   $this->valeur *= $mul;
  }
 }
}

function enchiffres_ajouter_petit(&$table_petits, $petit)
{
 $somme = 0;
 while (($elem = array_pop($table_petits)) !== NULL) {
  if ($elem->discr > $petit) {
   array_push($table_petits, $elem);
   break;
  }
  $somme += $elem->valeur;
 }
 $elem = new enchiffres_struct($somme, $petit);
 array_push($table_petits, $elem);
}

function enchiffres_somme_petits($table_petits)
{
 $somme = 0;
 foreach ($table_petits as $elem) {
  $somme += $elem->valeur;
 }
 return $somme;
}

function enchiffres_ajouter_grand(&$table_grands, $mantisse, $exposant)
{
 while ($mantisse > 0) {
  if (isset($table_grands[$exposant])) {
   $mantisse += $table_grands[$exposant];
  }
  $table_grands[$exposant] = $mantisse % 1000;
  $mantisse = floor($mantisse / 1000);
  $exposant++;
 }
}

function enchiffres($nom)
{
 $nom = preg_replace('/[éèÉÈ]/', 'e', $nom);
 $nom = strtolower($nom);
 $table_mots = preg_split('/[^a-z]+/', $nom);

 $table_petits = array();
 $mantisse = $exposant = 0;
 $table_grands = array();

 foreach ($table_mots as $mot) {
  $petit = enchiffres_petit($mot);
  if ($petit !== false) {
   if ($mantisse != 0) {
    enchiffres_ajouter_grand($table_grands, $mantisse, $exposant);
    $mantisse = $exposant = 0;
   }
   enchiffres_ajouter_petit($table_petits, $petit);
   continue;
  }

  $grand = enchiffres_grand($mot);
  if ($grand === false) {
   # Ce n'est pas un nombre
   continue;
  }

  if ($grand == 0) {
   # Ce nombre était trop grand (millinillion et plus) : on annule le
   # tout pour limiter le temps de calcul.
   $mantisse = 0;
   $exposant = 0;
   $table_petits = array();
  } else {
   if (count($table_petits) > 0) {
    $mantisse = enchiffres_somme_petits($table_petits);
    $exposant = 0;
    $table_petits = array();
   }
   if ($mantisse != 0) {
    $exposant += $grand;
   }
  }
 }
 if (count($table_petits) > 0) {
  $mantisse = enchiffres_somme_petits($table_petits);
  $exposant = 0;
 }
 if ($mantisse != 0) {
  enchiffres_ajouter_grand($table_grands, $mantisse, $exposant);
 }

 $nombre = "";
 for ($exposant = 0; count($table_grands) > 0; $exposant++) {
  if (isset($table_grands[$exposant])) {
   $par3 = $table_grands[$exposant];
   unset($table_grands[$exposant]);
  } else {
   $par3 = 0;
  }
  $nombre = sprintf("%03d", $par3) . $nombre;
 }
 $nombre = ltrim($nombre, '0');
 if ($nombre === '') $nombre = '0';
 return $nombre;
}

function enchiffres_aerer($nombre, $blanc=' ', $virgule=',', $tranche=3)
{
 # Si c'est un nombre à virgule, on traite séparément les deux parties
 if ($virgule !== NULL) {
  $ent_dec = preg_split("/$virgule/", $nombre);
  if (count($ent_dec) >= 2) {
   $ent = enchiffres_aerer($ent_dec[0], $blanc, NULL, $tranche);
   $dec = enchiffres_aerer($ent_dec[1], $blanc, NULL, -$tranche);
   return $ent . $virgule . $dec;
  }
 }

 # On ne garde que les chiffres
 $nombre = preg_replace('/[^0-9]/', '', $nombre);

 # Il est plus logique d'avoir un nombre positif pour les entiers,
 # donc négatif pour la partie décimale, mais plus pratique de
 # faire le contraire pour les substr().
 $tranche = - (int)$tranche;

 if ($tranche == 0) {
  # on voulait juste supprimer les caractères en trop, pas en rajouter
  return $nombre;
 }

 $nombre_aere = '';
 if ($tranche < 0) {
  # entier, ou partie entière d'un nombre décimal
  while ($nombre != '') {
   $par3 = substr($nombre, $tranche);
   $nombre = substr($nombre, 0, $tranche);
   if ($nombre_aere == '') {
    $nombre_aere = $par3;
   } else {
    $nombre_aere = $par3 . $blanc . $nombre_aere;
   }
  }
 } else {
  # partie décimale
  while ($nombre != '') {
   $par3 = substr($nombre, 0, $tranche);
   $nombre = substr($nombre, $tranche);
   if ($nombre_aere == '') {
    $nombre_aere = $par3;
   } else {
    $nombre_aere .= $blanc . $par3;
   }
  }
 }
 return $nombre_aere;
}

?>
