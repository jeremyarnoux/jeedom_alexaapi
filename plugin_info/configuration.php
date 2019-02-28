<?php
if (!isConnect())
{
  include_file('desktop', '404', 'php');
  die();
}
/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

//print $_GET['plugin'];
//print $_GET['configure'];
require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

include_file('core', 'authentification', 'php');
include_file('desktop', 'alexaapi', 'js', 'alexaapi');


?>

<legend><i class="icon divers-triangular42"></i> {{Génération manuelle du cookie Amazon}}</legend>

		<?php
//On va tester si les d�pendances sont install�es
		if (!(is_dir(realpath(dirname(__FILE__) . '/../resources/node_modules'))))
		{
		print "<B>Dépendances non présentes, génération manuelle du cookie Amazon impossible !!</B>";	
		print "<br><small>Le dossier <I>".dirname(__FILE__) . "/../resources/node_modules</I> est introuvable</small>";	
		}
else
		{
		?>
				<center><a class="btn btn-success btn-sm bt_startDeamonCookie"  >Identifiez-vous sur Amazon pour créer le cookie d'identification</a>
				<a class="btn btn-warning btn-sm bt_identificationCookie2"><i class="fa fa-clock-o"></i> ... Attendez la génération du Cookie Amazon ... </a><a class="btn btn-default btn-sm bt_identificationCookie2bis"><i class="fa fa-clock-o"></i> ... Attendez la génération du Cookie Amazon ... </a><a class="btn btn-danger btn-sm bt_identificationCookie2echec"><i class="fa fa-times"></i> La génération du Cookie Amazon a échoué </a><a class="btn btn-success btn-sm bt_identificationCookie3"><i class="fa fa-check"></i> Bravo : Cookie d'identification Amazon chargé !</a><a class="btn btn-success btn-sm bt_identificationCookie"><i class="fa fa-clock-o"></i> Ouverture de la fenetre d'identification Amazon Alexa en cours ...</a></center>

		<?php
		}
?>
<script>
    var compteVerifCookie=0;
    var CookiePresent=0;

	function VerifierSiCookieGenere() 
	{
VerifierSiCookieGenere1();
	}
	
	function VerifierSiCookieGenere1() 
		{
		compteVerifCookie++;
		if (compteVerifCookie>8)
			{
		$('.bt_identificationCookie2').hide();
		$('.bt_identificationCookie2bis').hide();
		$('.bt_identificationCookie2echec').show();
			return;
			}
		if ((compteVerifCookie>3) && (CookiePresent=1))
			{
		$('.bt_identificationCookie2').hide();
		$('.bt_identificationCookie2bis').hide();
		$('.bt_identificationCookie3').show();
			return;
			}		
		$('.bt_identificationCookie2').show();
		$('.bt_identificationCookie2bis').hide();
		setTimeout(VerifierSiCookieGenere2, 1000);
		}

	function VerifierSiCookieGenere2() 
	{

    jeedom.plugin.VerifiePresenceCookie(
    {
      id : plugin_id,
      forceRestart: 1,
      error: function (error)
      {
//On ne fait rien, on attend le cookie
	  },
      success:function(){
	CookiePresent=1;
      }
    });

		$('.bt_identificationCookie2').hide();
		$('.bt_identificationCookie2bis').show();
		setTimeout(VerifierSiCookieGenere1, 1000);
	}

function PopUpCentre(url, width, height) {
    var leftPosition, topPosition;
    //Allow for borders.
    leftPosition = (window.screen.width / 2) - ((width / 2) + 10);
    //Allow for title and status bars.
    topPosition = (window.screen.height / 2) - ((height / 2) + 50);
    //Open the window.
    nouvellefenetre=window.open(url, "Window2",
    "status=no,height=" + height + ",width=" + width + ",resizable=yes,left="
    + leftPosition + ",top=" + topPosition + ",screenX=" + leftPosition + ",screenY="
    + topPosition + ",toolbar=no,menubar=no,scrollbars=no,location=no,directories=no");
		
if(nouvellefenetre)
	{ //securit� pour fermer la fenetre si le focus est perdu
		window.onfocus=function()
		{
		nouvellefenetre.window.close();
		$('.bt_identificationCookie').hide();
		VerifierSiCookieGenere();
		}
	}
}

  var timeout_refreshDeamonCookieInfo = null;
  $('.bt_stopDeamonCookie').hide();
  $('.bt_identificationCookie').hide();
  $('.bt_identificationCookie2').hide();
  $('.bt_identificationCookie2bis').hide();
  $('.bt_identificationCookie2echec').hide();
  $('.bt_identificationCookie3').hide();

  // On appuie sur Le lancement du serveur... on lance "deamonCookieStart" via action=deamonCookieStart dans alexaapi.ajax.php
  $('.bt_startDeamonCookie').on('click',function()
  {
    clearTimeout(timeout_refreshDeamonInfo);
    jeedom.plugin.deamonCookieStart(
    {
      id : plugin_id,
      forceRestart: 1,
      error: function (error)
      {
        $('#div_alert').showAlert({message: error.message, level: 'danger'});
        refreshDeamonInfo();
        timeout_refreshDeamonInfo = setTimeout(refreshDeamonInfo, 5000);
      },
      success:function(){
        refreshDeamonInfo();
        $('.bt_startDeamonCookie').hide();
        $('.bt_identificationCookie').show();
        timeout_refreshDeamonInfo = setTimeout(refreshDeamonInfo, 1000);
		attendre();
}
    });
  });


function attendre() {
window.setTimeout(lancer, 2000);
}

function lancer() {
PopUpCentre("http://<?php print config::byKey('internalAddr')?>:3457", 480, 640);
}

  $('.bt_stopDeamonCookie').on('click',function()
  {
    clearTimeout(timeout_refreshDeamonInfo);
    jeedom.plugin.deamonCookieStop(
    {
      id : plugin_id,
      error: function (error)
      {
        $('#div_alert').showAlert({message: error.message, level: 'danger'});
        refreshDeamonInfo();
        timeout_refreshDeamonCookieInfo = setTimeout(refreshDeamonInfo, 5000);
      },
      success:function()
      {
        refreshDeamonInfo();
        $('.deamonCookieState').empty().append('<span class="label label-danger" style="font-size:1em;">{{NOK}}</span>');
        $('.bt_startDeamonCookie').show();
        $('.bt_stopDeamonCookie').hide();
        $('.bt_identificationCookie').hide();
        timeout_refreshDeamonInfo = setTimeout(refreshDeamonInfo, 5000);
      }
    });
  });

  $('.bt_identificationCookie').on('click',function()
  {
  });

</script>