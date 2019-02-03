<?php
/* This file is part of Plugin openzwave for jeedom.
 *
 * Plugin openzwave for jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Plugin openzwave for jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Plugin openzwave for jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

if (!isConnect('admin')) {
	throw new Exception('401 Unauthorized');
}
?>
<span class="pull-left alert" id="span_state" style="background-color : #dff0d8;color : #3c763d;height:35px;border-color:#d6e9c6;display:none;margin-bottom:0px;"><span style="position:relative; top : -7px;">{{Demande envoyée}}</span></span>
<br/><br/>

<div id='div_backupAlert' style="display: none;"></div>
<form class="form-horizontal">
    <fieldset>
        <div class="form-group">
            <label class="col-sm-4 col-xs-6 control-label">{{Etape 1 : }}</label>
            <div class="col-sm-4 col-xs-6">
                <a class="btn btn-default" id="bt_createCookie"><i class="fa fa-floppy-o"></i> {{ Lancez le serveur d'identification}}</a>

            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 col-xs-6 control-label">{{Etape 2 : }}</label>
            <div class="col-sm-4 col-xs-6">
                <a class="btn btn-default" href="http://192.168.0.21:3457"  onclick="open('http://192.168.0.21:3457', 'Popup', 'scrollbars=1,resizable=1,height=560,width=770'); return false;" ><i class="fa fa-cogs"></i> Identifiez vous sur Amazon</a>

            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 col-xs-6 control-label">{{Etape 3 : }}</label>
            <div class="col-sm-4 col-xs-6">
                <a class="btn btn-default" id="bt_closeCookie"><i class="fa fa-floppy-o"></i> {{ Stoppez le serveur d'identification}}</a>

            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 col-xs-6 control-label">{{Etape 4 : }}</label>
            <div class="col-sm-4 col-xs-6">
                Allez lancer le Démon principal en cliquant sur la fleche verte "(Re)Démarrer"

            </div>
        </div>


        </fieldset>
    </form>
</div>

<?php


include_file('core', 'alexaapi', 'class.js', 'alexaapi');
include_file('desktop', 'cookie', 'js', 'alexaapi');
?>



