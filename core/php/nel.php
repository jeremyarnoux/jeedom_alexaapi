<?php

error_reporting(E_ALL);

#
# Fichier : nel.php (Nombres En Lettres)
#
# Auteur : Olivier Miakinen
# Création : mercredi 2 avril 2003
# Dernière modification : dimanche 11 novembre 2007
#
# La fonction enlettres($nombre) retourne une chaîne de caractères
# représentant le nombre $nombre écrit en toutes lettres, en français.
#
# Toute la documentation sur trouve sur :
#  http://www.miakinen.net/vrac/nombres
# Un programme de test existe sur :
#  http://www.miakinen.net/vrac/nombres2
#
######################################################################
#
# Je tiens à remercier tout particulièrement Nicolas Graner pour
# m'avoir fourni le code source de son propre programme d'écriture
# des nombres en lettres.
#
# En effet, c'est à lui que je dois la superbe implémentation du
# système de John Horton Conway et Allan Wechsler sous la forme de
# deux ou trois preg_replace().
#
# Voir <http://www.graner.net/nicolas/nombres/nom.php>
# et <http://www.graner.net/nicolas/nombres/nom-exp.php>
#
######################################################################
#
# Correction de dimanche 11 novembre 2007
#	Dans la version 5.2.2 de PHP, le comportement de la fonction
#	substr() a changé. Auparavant substr($nombre, -6) retournait
#	$nombre dans le cas où sa longueur était inférieure à 6, mais
#	maintenant cela retourne false. Merci à Benjamin (de la société
#	Dreamnex) et à David Duret pour m'avoir signalé le problème et
#	sa solution.
#
######################################################################

define('NEL_SEPTANTE',       0x0001);
define('NEL_HUITANTE',       0x0002);
define('NEL_OCTANTE',        0x0004);
define('NEL_NONANTE',        0x0008);
define('NEL_BELGIQUE',       NEL_SEPTANTE|NEL_NONANTE);
define('NEL_VVF',            NEL_SEPTANTE|NEL_HUITANTE|NEL_NONANTE);
define('NEL_ARCHAIQUE',      NEL_SEPTANTE|NEL_OCTANTE|NEL_NONANTE);
define('NEL_SANS_MILLIARD',  0x0010);
define('NEL_AVEC_ZILLIARD',  0x0020);
define('NEL_TOUS_ZILLIONS',  0x0040);
define('NEL_RECTIF_1990',    0x0100);
define('NEL_ORDINAL',        0x0200);
define('NEL_NIEME',          0x0400);

# Le tableau associatif $NEL contient toutes les variables utilisées
# de façon globale dans ce module. ATTENTION : ce nom est assez court,
# et cela pourrait poser des problèmes de collision avec une autre
# variable si plusieurs modules sont inclus dans le même programme.

$NEL = array(
  '1-99' => array(
    # 0-19
    '', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept',
    'huit', 'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze',
    'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf',
    # 20-29
    'vingt', 'vingt et un', 'vingt-deux', 'vingt-trois',
    'vingt-quatre', 'vingt-cinq', 'vingt-six',
    'vingt-sept', 'vingt-huit', 'vingt-neuf',
    # 30-39
    'trente', 'trente et un', 'trente-deux', 'trente-trois',
    'trente-quatre', 'trente-cinq', 'trente-six',
    'trente-sept', 'trente-huit', 'trente-neuf',
    # 40-49
    'quarante', 'quarante et un', 'quarante-deux', 'quarante-trois',
    'quarante-quatre', 'quarante-cinq', 'quarante-six',
    'quarante-sept', 'quarante-huit', 'quarante-neuf',
    # 50-59
    'cinquante', 'cinquante et un', 'cinquante-deux', 'cinquante-trois',
    'cinquante-quatre', 'cinquante-cinq', 'cinquante-six',
    'cinquante-sept', 'cinquante-huit', 'cinquante-neuf',
    # 60-69
    'soixante', 'soixante et un', 'soixante-deux', 'soixante-trois',
    'soixante-quatre', 'soixante-cinq', 'soixante-six',
    'soixante-sept', 'soixante-huit', 'soixante-neuf',
    # 70-79
    'septante', 'septante et un', 'septante-deux', 'septante-trois',
    'septante-quatre', 'septante-cinq', 'septante-six',
    'septante-sept', 'septante-huit', 'septante-neuf',
    # 80-89
    'huitante', 'huitante et un', 'huitante-deux', 'huitante-trois',
    'huitante-quatre', 'huitante-cinq', 'huitante-six',
    'huitante-sept', 'huitante-huit', 'huitante-neuf',
    # 90-99
    'nonante', 'nonante et un', 'nonante-deux', 'nonante-trois',
    'nonante-quatre', 'nonante-cinq', 'nonante-six',
    'nonante-sept', 'nonante-huit', 'nonante-neuf'
  ),

  'illi' => array('', 'm', 'b', 'tr', 'quatr', 'quint', 'sext'),
  'maxilli' => 0,           # voir plus loin
  'de_maxillions' => '',    # voir plus loin

  'septante' => false,  # valeurs possibles : (false|true)
  'huitante' => false,  # valeurs possibles : (false|true|'octante')
  'nonante' => false,   # valeurs possibles : (false|true)
  'zillions' => false,  # valeurs possibles : (false|true)
  'zilliard' => 1,      # valeurs possibles : (0|1|2)
  'rectif' => false,    # valeurs possibles : (false|true)
  'ordinal' => false,   # valeurs possibles : (false|true|'nieme')

  'separateur' => ' '
);

# Si le tableau $NEL['illi'] s'arrête à 'sext', alors les deux valeurs
# suivantes sont respectivement '6' et ' de sextillions'.
$NEL['maxilli'] = count($NEL['illi']) - 1;
$NEL['de_maxillions'] = " de {$NEL['illi'][$NEL['maxilli']]}illions";

function enlettres_options($options, $separateur=NULL)
{
 global $NEL;

 if ($options !== NULL) {
  $NEL['septante'] = ($options & NEL_SEPTANTE) ? true : false;
  $NEL['huitante'] =
    ($options & NEL_OCTANTE) ? 'octante' :
    (($options & NEL_HUITANTE) ? true : false);
  $NEL['nonante'] = ($options & NEL_NONANTE) ? true : false;
  $NEL['zillions'] = ($options & NEL_TOUS_ZILLIONS) ? true : false;
  $NEL['zilliard'] =
    ($options & NEL_AVEC_ZILLIARD) ? 2 :
    (($options & NEL_SANS_MILLIARD) ? 0 : 1);
  $NEL['rectif'] = ($options & NEL_RECTIF_1990) ? true : false;
  $NEL['ordinal'] =
    ($options & NEL_NIEME) ? 'nieme' :
    (($options & NEL_ORDINAL) ? true : false);
 }

 if ($separateur !== NULL) {
  $NEL['separateur'] = $separateur;
 }
}

function enlettres_par3($par3)
{
 global $NEL;

 if ($par3 == 0) return '';

 $centaine = floor($par3 / 100);
 $par2 = $par3 % 100;
 $dizaine = floor($par2 / 10);

 # On traite à part les particularités du français de référence
 # 'soixante-dix', 'quatre-vingts' et 'quatre-vingt-dix'.
 $nom_par2 = NULL;
 switch ($dizaine) {
 case 7:
  if ($NEL['septante'] === false) {
   if ($par2 == 71) $nom_par2 = 'soixante et onze';
   else $nom_par2 = 'soixante-' . $NEL['1-99'][$par2 - 60];
  }
  break;
 case 8:
  if ($NEL['huitante'] === false) {
   //if ($par2 == 80) $nom_par2 = 'quatre-vingts'; Corrigé car le S posait un souci avec les intéractions Alexa 09/21 - Sigalou
   if ($par2 == 80) $nom_par2 = 'quatre-vingt';
   else $nom_par2 = 'quatre-vingt-' . $NEL['1-99'][$par2 - 80];
  }
  break;
 case 9:
  if ($NEL['nonante'] === false) {
   $nom_par2 = 'quatre-vingt-' . $NEL['1-99'][$par2 - 80];
  }
  break;
 }
 if ($nom_par2 === NULL) {
  $nom_par2 = $NEL['1-99'][$par2];
  if (($dizaine == 8) and ($NEL['huitante'] === 'octante')) {
   $nom_par2 = str_replace('huitante', 'octante', $nom_par2);
  }
 }

 # Après les dizaines et les unités, il reste à voir les centaines
 switch ($centaine) {
 case 0: return $nom_par2;
 case 1: return rtrim("cent {$nom_par2}");
 }

 # Assertion : $centaine = 2 .. 9
 $nom_centaine = $NEL['1-99'][$centaine];
 if ($par2 == 0) return "{$nom_centaine} cents";
 return "{$nom_centaine} cent {$nom_par2}";
}

function enlettres_zilli($idx)
{
 # Noms des 0ème à 9ème zillions
 static $petit = array(
    'n', 'm', 'b', 'tr', 'quatr', 'quint', 'sext', 'sept', 'oct', 'non'
 );
 # Composantes des 10ème à 999ème zillions
 static $unite = array(
    '<', 'un<', 'duo<', 'tre<sé',
    'quattuor<', 'quin<', 'se<xsé',
    'septe<mné', 'octo<', 'nove<mné'
 );
 static $dizaine = array(
    '', 'né>déci<', 'ms>viginti<', 'ns>triginta<',
    'ns>quadraginta<', 'ns>quinquaginta<', 'né>sexaginta<',
    'né>septuaginta<', 'mxs>octoginta<', 'é>nonaginta<'
 );
 static $centaine = array(
    '>', 'nxs>cent', 'né>ducent', 'ns>trécent',
    'ns>quadringent', 'ns>quingent', 'né>sescent',
    'né>septingent', 'mxs>octingent', 'é>nongent'
 );

 # Règles d'assimilation aux préfixes latins, modifiées pour accentuer
 # un éventuel 'é' de fin de préfixe.
 # (1) Si on trouve une lettre deux fois entre < > on la garde.
 #     S'il y a plusieurs lettres dans ce cas, on garde la première.
 # (2) Sinon on efface tout ce qui est entre < >.
 # (3) On remplace "treé" par "tré", "seé" par "sé", "septeé" par "septé"
 #     et "noveé" par "nové".
 # (4) En cas de dizaine sans centaine, on supprime la voyelle en trop.
 #     Par exemple "déciilli" devient "décilli" et "trigintailli" devient
 #     "trigintilli".
 #
 # Il est à noter que ces règles PERL (en particulier la première qui
 # est la plus complexe) sont *très* fortement inspirées du programme
 # de Nicolas Graner. On pourrait même parler de plagiat s'il n'avait
 # pas été au courant que je reprenais son code.
 # Voir <http://www.graner.net/nicolas/nombres/nom.php>
 # et <http://www.graner.net/nicolas/nombres/nom-exp.php>
 #
 static $recherche = array(
  '/<[a-zé]*?([a-zé])[a-zé]*\1[a-zé]*>/',       # (1)
  '/<[a-zé]*>/',                                # (2)
  '/eé/',                                       # (3)
  '/[ai]illi/'                                  # (4)
 );
 static $remplace = array(
  '\\1',                                        # (1)
  '',                                           # (2)
  'é',                                          # (3)
  'illi'                                        # (4)
 );

 $nom = '';
 while ($idx > 0) {
  $p = $idx % 1000;
  $idx = floor($idx/1000);

  if ($p < 10) {
   $nom = $petit[$p] . 'illi' . $nom;
  } else {
   $nom = $unite[$p % 10] . $dizaine[floor($p/10) % 10]
        . $centaine[floor($p/100)] . 'illi' . $nom;
  }
 }
 return preg_replace($recherche, $remplace, $nom);
}

function enlettres_illions($idx)
{
 global $NEL;

 if ($idx == 0) {
  return '';
 }

 if ($NEL['zillions']) {
  return enlettres_zilli($idx) . 'ons';
 }

 $suffixe = '';
 while ($idx > $NEL['maxilli']) {
  $idx -= $NEL['maxilli'];
  $suffixe .= $NEL['de_maxillions'];
 }
 return "{$NEL['illi'][$idx]}illions{$suffixe}";
}

function enlettres_avec_illiards($idx)
{
 global $NEL;

 if ($idx == 0) return false;
 switch ($NEL['zilliard']) {
 case 0: return false;
 case 2: return true;
 }
 return ($idx == 1);
}

function enlettres($nombre, $options=NULL, $separateur=NULL)
{
 global $NEL;

 if ($options !== NULL or $separateur !== NULL) {
  $NELsave = $NEL;
  enlettres_options($options, $separateur);
  $nom = enlettres($nombre);
  $NEL = $NELsave;
  return $nom;
 }

 # On ne garde que les chiffres, puis on supprime les 0 du début
 $nombre = preg_replace('/[^0-9]/', '', $nombre);
 $nombre = ltrim($nombre, '0');

 if ($nombre == '') {
  if ($NEL['ordinal'] === 'nieme') return 'zéroïème';
  else return 'zéro';
 }

 $table_noms = array();
 for ($idx = 0; $nombre != ''; $idx++) {
  $par6 = (int)((strlen($nombre) < 6) ? $nombre : substr($nombre, -6));
  $nombre = substr($nombre, 0, -6);

  if ($par6 == 0) continue;

  $nom_par3_sup = enlettres_par3(floor($par6 / 1000));
  $nom_par3_inf = enlettres_par3($par6 % 1000);

  $illions = enlettres_illions($idx);
  if (enlettres_avec_illiards($idx)) {
   if ($nom_par3_inf != '') {
    $table_noms[$illions] = $nom_par3_inf;
   }
   if ($nom_par3_sup != '') {
    $illiards = preg_replace('/illion/', 'illiard', $illions, 1);
    $table_noms[$illiards] = $nom_par3_sup;
   }
  } else {
   switch($nom_par3_sup) {
   case '':
    $nom_par6 = $nom_par3_inf;
    break;
   case 'un':
    $nom_par6 = rtrim("mille {$nom_par3_inf}");
    break;
   default:
    $nom_par3_sup = preg_replace('/(vingt|cent)s/', '\\1', $nom_par3_sup);
    $nom_par6 = rtrim("{$nom_par3_sup} mille {$nom_par3_inf}");
    break;
   }
   $table_noms[$illions] = $nom_par6;
  }
 }

 $nom_enlettres = '';
 foreach ($table_noms as $nom => $nombre) {
  ##
  # $nombre est compris entre 'un' et
  # 'neuf cent nonante-neuf mille neuf cent nonante-neuf'
  # (ou variante avec 'quatre-vingt-dix-neuf')
  ##
  # $nom peut valoir '', 'millions', 'milliards', 'billions', ...
  # 'sextillions', 'sextilliards', 'millions de sextillions',
  # 'millions de sextilliards', etc.
  ##

  # Rectifications orthographiques de 1990
  if ($NEL['rectif']) {
   $nombre = str_replace(' ', '-', $nombre);
  }

  # Nom (éventuel) et accord (éventuel) des substantifs
  $nom = rtrim("{$nombre} {$nom}");
  if ($nombre == 'un') {
   # Un seul million, milliard, etc., donc au singulier
   # noter la limite de 1 remplacement, pour ne supprimer que le premier 's'
   # dans 'billions de sextillions de sextillions'
   $nom = preg_replace('/(illion|illiard)s/', '\\1', $nom, 1);
  }

  # Ajout d'un séparateur entre chaque partie
  if ($nom_enlettres == '') {
   $nom_enlettres = $nom;
  } else {
   $nom_enlettres = $nom . $NEL['separateur'] . $nom_enlettres;
  }
 }

 if ($NEL['ordinal'] === false) {
  # Nombre cardinal : le traitement est fini
  return $nom_enlettres;
 }

 # Aucun pluriel dans les ordinaux
 $nom_enlettres =
   preg_replace('/(cent|vingt|illion|illiard)s/', '\\1', $nom_enlettres);

 if ($NEL['ordinal'] !== 'nieme') {
  # Nombre ordinal simple (sans '-ième')
  return $nom_enlettres;
 }

 if ($nom_enlettres === 'un') {
  # Le féminin n'est pas traité ici. On fait la supposition
  # qu'il est plus facile de traiter ce cas à part plutôt
  # que de rajouter une option rien que pour ça.
  return 'premier';
 }

 switch (substr($nom_enlettres, -1)) {
 case 'e':
  # quatre, onze à seize, trente à nonante, mille
  # exemple : quatre -> quatrième
  return substr($nom_enlettres, 0, -1) . 'ième';
 case 'f':
  # neuf -> neuvième
  return substr($nom_enlettres, 0, -1) . 'vième';
 case 'q':
  # cinq -> cinquième
  return $nom_enlettres . 'uième';
 }

 # Tous les autres cas.
 # Exemples: deuxième, troisième, vingtième, trente et unième,
 #           neuf centième, un millionième, quatre-vingt milliardième.
 return $nom_enlettres . 'ième';
}

?>
