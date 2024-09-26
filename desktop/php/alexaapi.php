<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

// Obtenir l'identifiant du plugin
$plugin = plugin::byId('alexaapi');
// Charger le javascript
sendVarToJS('eqType', $plugin->getId());
//sendVarToJS('serveurtest', 'lionel dans alexaapi.php');

// Accéder aux données du plugin
$eqLogics = eqLogic::byType($plugin->getId());
$logicalIdToHumanReadable = array();
foreach ($eqLogics as $eqLogic) {
    $logicalIdToHumanReadable[$eqLogic->getLogicalId()] = $eqLogic->getHumanName(true, false);
}
?>
<script>
    var logicalIdToHumanReadable = <?php echo json_encode($logicalIdToHumanReadable); ?>

    function printEqLogic(data) {

        // On masque la ligne "Activer le widget Playlist" si c'est pas un player
        var str = data.logicalId
        if (str.substring(str.length - 6, str.length) != "player")
            $('#widgetPlayListEnable').parent().hide();
        else
            $('#widgetPlayListEnable').parent().show();


        //if (data.configuration.family === undefined)
        //{
        //	 $('#family').hide(); //ajouté, masque Famille si c'est vide
        // }

        // Traitement de Multiroom sur les infos du device
        $('#multiroom-members').empty();
        if (data.configuration.members === undefined) {
            //$('#multiroom-members').append('Configuration incomplete.'); //supprimé
            $('#multiroom-members').parent().hide(); //ajouté
            return;
        }
        if (data.configuration.members.length === 0) {
            $('#multiroom-members').parent().hide();
            return;
        }
        var html = '<ul style="list-style-type: none;">';
        for (var i in data.configuration.members) {
            var logicalId = data.configuration.members[i];
            if (logicalId in logicalIdToHumanReadable)
                html += '<li style="margin-top: 5px;">' + logicalIdToHumanReadable[logicalId] + '</li>';
            else
                html += '<li style="margin-top: 5px;"><span class="label label-default" style="text-shadow : none;"><i>(Non configuré)</i></span> ' + logicalId + '</li>';
        }
        html += '</ul>';
        $('#multiroom-members').parent().show();
        $('#multiroom-members').append(html);
    }
</script>

<!-- Container global (Ligne bootstrap) -->
<div class="row row-overflow">
    <!-- Container des listes de commandes / éléments -->
    <div class="col-xs-12 eqLogicThumbnailDisplay">
        <legend><i class="fas fa-cog"></i> {{Gestion}}</legend>
        <div class="eqLogicThumbnailContainer">
            <!-- Bouton de scan des objets -->
            <div class="cursor logoPrimary" id="bt_scan">
                <i class="fas fa-bullseye" style="font-size: 3em;color: #00caff;"></i>
                <br />
                <span style="color: #00caff;">{{Scan}}</span>
            </div>

            <!-- Bouton d accès à la configuration -->
            <div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
                <i class="fas fa-wrench" style="font-size : 5em;"></i>
                <br />
                <span>{{Configuration}}</span>
            </div>

            <div class="cursor logoSecondary" id="bt_sante">
                <i class="fas fa-medkit" style="font-size : 5em;"></i>
                <br />
                <span>{{Santé}}</span>
            </div>


            <div class="cursor logoSecondary" id="bt_routines">
                <i class="fas divers-viral" style="font-size : 5em;"></i>
                <br />
                <span>{{Routines}}</span>
            </div>


            <div class="cursor logoSecondary" id="bt_reminders">
                <i class="fas fa-clock" style="font-size : 5em;"></i>
                <br />
                <span>{{Rappels/Alarmes}}</span>
            </div>


            <div class="cursor logoSecondary" id="bt_history">
                <i class="fas fa-list-alt" style="font-size : 5em;"></i>
                <br />
                <span>{{Historique}}</span>
            </div>
			
            <div class="cursor logoSecondary" onclick="window.open('https://www.sigalou-domotique.fr/alexa-api-documentation')">
			<i class="fas fa-book" style="font-size : 5em;"></i>
                <br />
                <span>{{Documentation}}</span>
            </div>            
			
			<?php


            echo '</div>';


            ?>
            <!-- Début de la liste des objets -->
            <legend><i class="fas fa-table"></i> {{Mes Amazon Echo}}</legend>
            <div class="input-group" style="margin-bottom:5px;">
                <input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
                <div class="input-group-btn">
                    <a id="bt_resetSearch" class="btn" style="width:30px"><i class="fas fa-times"></i>
                    </a><a class="btn roundedRight hidden" id="bt_pluginDisplayAsTable" data-coreSupport="1" data-state="0"><i class="fas fa-grip-lines"></i></a>
                </div>
            </div>
            <!-- Container de la liste -->
            <div class="panel">
                <div class="panel-body">
                    <div class="eqLogicThumbnailContainer prem">
                        <?php
                        foreach ($eqLogics as $eqLogic) {

                            //	if (($eqLogic->getConfiguration('devicetype') != "Smarthome") && ($eqLogic->getConfiguration('devicetype') != "Player") && ($eqLogic->getConfiguration('devicetype') != "PlayList")) {

                            $opacity = ($eqLogic->getIsEnable()) ? '' : ' disableCard';
                            echo '<div style="position: relative;" class="eqLogicDisplayCard cursor prem ' . $opacity . '" data-eqLogic_id="' . $eqLogic->getId() . '" >';
                            echo '<span class="hidden hiddenAsCard displayTableRight">';
                            if ($eqLogic->getConfiguration('autorefresh', '') != '') {
                                echo '<span class="label label-info">' . $eqLogic->getConfiguration('autorefresh') . '</span>';
                            }
                            echo ($eqLogic->getIsVisible() == 1) ? '<i class="fas fa-eye" title="{{Equipement visible}}"></i>' : '<i class="fas fa-eye-slash" title="{{Equipement non visible}}"></i>';
                            echo '</span>';


                            $datetimecreation = new DateTime($eqLogic->getConfiguration('createtime'));
                            $datetimeaujourdhui = new DateTime(date('Y-m-d'));
                            $interval = $datetimecreation->diff($datetimeaujourdhui);


                            if (($eqLogic->getStatus('online') != 'true') && (!strstr($eqLogic->getName(), "Alexa Apps")) && (!strstr($eqLogic->getName(), "Ultimate Alexa"))) {
                                //echo '<i class="fas fa-power-off" style="color: red;text-shadow: 4px 4px 4px #ccc;float:right" title="Offline"></i>';
                                echo '<span class="badge-alexaapi badge-danger">Off</span>';
                            } else {
                                if ($interval->format('%a') < 1) {
                                    echo '<span class="badge-alexaapi badge-success">Nouveau</span>';
                                }
                            }

                            $alternateImg = $eqLogic->getConfiguration('type');
                            if (file_exists(dirname(__FILE__) . '/../../core/config/devices/' . $alternateImg . '.png'))
                                echo '<img class="lazy" src="plugins/alexaapi/core/config/devices/' . $alternateImg . '.png" style=" !important;" />';
                            elseif (file_exists(dirname(__FILE__) . '/../../core/config/devices/' . $eqLogic->getConfiguration('family') . '.png'))
                                echo '<img class="lazy" src="plugins/alexaapi/core/config/devices/' . $eqLogic->getConfiguration('family') . '.png" style=" !important;" />';
                            elseif (file_exists(dirname(__FILE__) . '/../../core/config/devices/default.png'))
                                echo '<img class="lazy" src="plugins/alexaapi/core/config/devices/default.png" style=" !important;" />';
                            else
                                echo '<img class="lazy" src="' . $plugin->getPathImgIcon() . '" style=" !important;" />';

                            echo "<br />";
                            echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';

                            echo '</div>';
                            //}
                        }
                        ?>
                    </div>
                </div>
            </div>


            <!-- Début de la liste des objets -->
            <legend><i class="fas fa-table"></i> {{Plugins d'Alexa-API}}</legend>
            <!-- Container de la liste -->
            <div class="panel">
                <div class="panel-body">
                    <div class="eqLogicThumbnailContainer">
                        <?php
                        $compteNombrePlayeurs = 0;
                        foreach (alexaapi::listePluginsAlexaArray(false, true, true, true) as $pluginAlexaUnparUn) {
                            //echo $pluginAlexaUnparUn;
                            //$nomPlugin="inconnu";
                            /*if ($pluginAlexaUnparUn['pluginId']=='alexaamazonmusic') $nomPlugin=$pluginAlexaUnparUn['nom'];
                          if ($pluginAlexaUnparUn=='alexadeezer') $nomPlugin='Deezer';
                          if ($pluginAlexaUnparUn=='alexaspotify') $nomPlugin='Spotify';*/
                            //eqLogicDisplayCard cursor prem
                            echo '<div class="cursor eqLogicAction logoSecondary" style="position: relative;';

                            if ($pluginAlexaUnparUn['actif'] != true) {
                                echo ' filter: grayscale(70%); opacity: 0.35;';
                            } else
                                if (($pluginAlexaUnparUn['pluginId'] != 'alexafiretv') && ($pluginAlexaUnparUn['pluginId'] != 'alexasmarthome')) $compteNombrePlayeurs++;

                            echo '">';
                            if ($pluginAlexaUnparUn['nb'] != "0") echo '<span class="badge-alexaapi">' . $pluginAlexaUnparUn['nb'] . '</span>';
                            echo '<a href="';

                            if ($pluginAlexaUnparUn['install'] == true)
                                echo 'index.php?v=d&m=' . $pluginAlexaUnparUn['pluginId'] . '&p=' . $pluginAlexaUnparUn['pluginId'];
                            else
                                echo 'https://www.jeedom.com/market/index.php?v=d&p=market&type=plugin&plugin_id=' . $pluginAlexaUnparUn['idMarket'] . '" target="_blank';

                            echo '"><img src="plugins/alexaapi/plugin_info/' . $pluginAlexaUnparUn['pluginId'] . '_icon.png" width="75" height="75" style=" !important;" />
			<br /><span >{{' . $pluginAlexaUnparUn['nom'] . '}}</span></a>
		</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>


            <?php
            if ($compteNombrePlayeurs > 1) {
            ?>

                <div class="alert-danger bg-success">
                    Vous avez plus d'un playeur activé, cela ne fonctionnera pas !!! Merci de n'en activer qu'un à la
                    fois.
                </div>
            <?php
            }


            if (config::byKey('utilisateurExperimente', 'alexaapi', 0) != "0") :
            ?>
                <!-- Début de la liste des objets -->
                <legend><i class="fas fa-table"></i> {{Outils Utilisateurs expérimentés}}</legend>
                <!-- Container de la liste -->
                <div class="panel">
                    <div class="panel-body">
                        <div class="eqLogicThumbnailContainer">

                            <div class="cursor logoSecondary" id="bt_req">
                                <i class="fas fa-key" style="font-size : 5em;"></i>
                                <br />
                                <span>{{Requêteur Infos}}</span>
                            </div>

                            <div class="cursor logoSecondary" id="bt_req2">
                                <i class="fas fa-key" style="font-size : 5em;"></i>
                                <br />
                                <span>{{Requêteur Actions}}</span>
                            </div>


                        </div>
                    </div>
                </div>

            <?php
            endif; ?>

        </div>
        <!-- Container du panneau de contrôle -->
        <div class="col-lg-12 eqLogic" style="display: none;">
            <div class="input-group pull-right" style="display:inline-flex">
                <span class="input-group-btn">
                    <!-- Les balises <a></a> sont volontairement fermées à la ligne suivante pour éviter les espaces entre les boutons. Ne pas modifier -->
                    <a class="btn btn-sm btn-default eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i><span class="hidden-xs"> {{Configuration avancée}}</span>
                    </a><a class="btn btn-warning btn-sm" id="bt_forcerDefaultCmd" title="{{Recharger configuration par défaut}}"><i class="fas fa-search"></i><span class="hidden-xs"> {{Recharger configuration par défaut}}</span>
                    </a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}
                    </a><a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}
                    </a>
                </span>
            </div>
            <!-- Onglets -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
                <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
                <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list-alt"></i> {{Commandes}}</a></li>
            </ul>
            <!-- Container du contenu des onglets -->
            <div class="tab-content">
                <!-- Onglet de configuration de l'équipement -->
                <div role="tabpanel" class="tab-pane active" id="eqlogictab">
                    <br />
                    <!-- Partie gauche de l'onglet "Equipements" -->
                    <!-- Paramètres généraux de l'équipement -->
                    <form class="form-horizontal">
                        <fieldset>
                            <div class="col-lg-6">
                                <legend><i class="fas fa-wrench"></i> {{Paramètres généraux}}</legend>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{Nom de l'équipement Jeedom}}</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement}}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{Nom de l'équipement Amazon}}</label>
                                    <div class="col-sm-6">
                                        <span style="position:relative;top:+5px;left:+5px;" class="eqLogicAttr" data-l1key="configuration" data-l2key="device"> </span>
                                    </div>
                                </div>
                                <!-- Onglet "Objet Parent" -->
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{Objet parent}}</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                                        <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                                            <option value="">{{Aucun}}</option>
                                            <?php
                                            $options = '';
                                            foreach ((jeeObject::buildTree(null, false)) as $object) {
                                                $options .= '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber')) . $object->getName() . '</option>';
                                            }
                                            echo $options;
                                            ?>
                                        </select>
                                    </div>
                                    <!-- Catégorie" -->
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">{{Catégorie}}</label>
                                        <div class="col-sm-6">
                                            <?php
                                            foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                                                echo '<label class="checkbox-inline">';
                                                echo '  <input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                                                echo '</label>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <!-- Onglet "Active Visible" -->
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">{{Options}}</label>
                                        <div class="col-sm-6">
                                            <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked>{{Activer}}</label>
                                            <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked>{{Visible}}</label>
                                            <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="interactionjeedom" />{{Interactions Avec Jeedom}}</label>
                                        </div>
                                    </div>

                                    <legend><i class="fas fa-cogs"></i> {{Paramètres spécifiques}}</legend>

                                    <!-- Onglet "Device Playlist" -->
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">{Option Playlist}</label>
                                        <div class="col-sm-6" id="widgetPlayListEnable">
                                            <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="widgetPlayListEnable" />{{Activer
                                                le widget Playlist}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Partie droite de l'onglet "Équipement" -->
                            <!-- Affiche un champ de commentaire par défaut mais vous pouvez y mettre ce que vous voulez -->
                            <div class="col-lg-6">
                                <legend><i class="fas fa-info"></i> {{Informations}}</legend>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">{{ID}}</label>
                                    <div class="col-sm-8">
                                        <span style="position:relative;top:+5px;left:+5px;" class="eqLogicAttr" data-l1key="logicalId"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">{{Type}}</label>
                                    <div class="col-sm-8">
                                        <span style="position:relative;top:+5px;left:+5px;" class="eqLogicAttr" data-l1key="configuration" data-l2key="type"></span>
                                    </div>
                                </div>
                                <div class="form-group" id="family">
                                    <label class="col-sm-2 control-label">{{Famille}}</label>
                                    <div class="col-sm-8">
                                        <span style="position:relative;top:+5px;left:+5px;" class="eqLogicAttr" data-l1key="configuration" data-l2key="family"></span>
                                    </div>
                                </div>
                                <label class="col-sm-2 control-label">{{Fonctionnalités}}</label>
                                <div class="col-sm-8">
                                    <span style="position:relative;top:+5px;left:+5px;font-size: 10px;" class="eqLogicAttr" data-l1key="configuration" data-l2key="capabilities"></span>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-10">
                                        <center>
                                            <img src="core/img/no_image.gif" data-original=".jpg" id="img_device" class="img-responsive" style="max-height : 250px;" onerror="this.src='plugins/alexaapi/core/config/devices/default.png'" />
                                        </center>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">{{Multiroom}}</label>
                                    <div class="col-sm-8" id="multiroom-members">
                                        <!-- Liste des membres du multiroom -->

                                    </div>
                                </div>
                            </div>


                        </fieldset>
                    </form>

                    <div class="row">


                        <!--
            <div class="cursor" id="bt_media" data-l1key="logicalId" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
                <center>
                <i class="fa loisir-musical7" style="font-size : 6em;color:#767676;"></i>
                </center>
            <span style="font-size : 1.1em;position:relative; top : 25px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Info Média}}</center></span>
            </div>
                     Castré par Nebz et HadesDT
            <div class="cursor" id="bt_test" data-l1key="logicalId" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
            <center>
            <i class="fa loisir-musical7" style="font-size : 6em;color:#767676;"></i>
            </center>
            <span style="font-size : 1.1em;position:relative; top : 25px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Test}}</center></span>
            </div>
            -->
                        <?php

                        //if ($eqLogic->getConfiguration('devicetype')!="Smarthome")
                        //{
                        ?>


                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="commandtab">
                    <?php
                    if (config::byKey('utilisateurExperimente', 'alexaapi', 0) != "0") {
                    ?>
                        <a class="pull-right btn btn-success btn-sm cmdAction" id="bt_addespeasyAction"><i class="fa fa-plus-circle"></i> {{Ajouter une commande action}}</a>
                    <?php
                    }
                    ?>

                    <table id="table_cmd" class="table table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th class="hidden-xs" style="min-width:50px;width:70px;">ID</th>
                                <th style="min-width:200px;width:350px;">{{Nom}}</th>
                                <th>{{Type}}</th>
                                <th style="width: 600px;">{{Commande & Variable}}</th>
				<th>{{Valeurs}}</th>
                                <th style="min-width:360px;">{{Options}}</th>
                                <th style="min-width:80px;width:200px;">{{Actions}}</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>


            </div>
        </div>
    </div>

    <?php include_file('desktop', 'alexaapi', 'js', 'alexaapi'); ?>
    <?php include_file('desktop', 'alexaapi', 'css', 'alexaapi'); ?>
    <?php include_file('core', 'plugin.template', 'js'); ?>
    <script>
        $('#in_searchEqlogic').off('keyup').keyup(function() {
            var search = $(this).value().toLowerCase();
            search = search.normalize('NFD').replace(/[\u0300-\u036f]/g, "")
            if (search == '') {
                $('.eqLogicDisplayCard.prem').show();
                $('.eqLogicThumbnailContainer.prem').packery();
                return;
            }
            $('.eqLogicDisplayCard.prem').hide();
            $('.eqLogicDisplayCard.prem .name').each(function() {
                var text = $(this).text().toLowerCase();
                text = text.normalize('NFD').replace(/[\u0300-\u036f]/g, "")
                if (text.indexOf(search) >= 0) {
                    $(this).closest('.eqLogicDisplayCard.prem').show();
                }
            });
            $('.eqLogicThumbnailContainer.prem').packery();
        });
    </script>
