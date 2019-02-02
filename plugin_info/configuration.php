<?php

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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
  include_file('desktop', '404', 'php');
  die();
}
?>


<form class="form-horizontal">
  <div class="form-group">
    <fieldset>

      <div class="form-group">
        <label class="col-lg-4 control-label">{{IP Controleur de l'API Alexa}} :</label>
        <div class="col-lg-4">
          <?php
            echo config::byKey('internalAddr');
          ?>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-4 control-label">{{Port Controleur à utiliser}} :</label>
        <div class="col-lg-4">
          3456
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-4 control-label">{{Exemple de commande }} :</label>
        <div class="col-lg-4"><?php
            echo config::byKey('internalAddr');
          ?>:3456/speak?device=salon&text=coucou
        </div>
      </div>



    </fieldset>
  </form>


</div>
