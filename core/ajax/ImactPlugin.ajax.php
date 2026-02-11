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

try {
  require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
  include_file('core', 'authentification', 'php');

  if (!isConnect('admin')) {
    throw new Exception(__('401 - Accès non autorisé', __FILE__));
  }


  /* Fonction permettant l'envoi de l'entête 'Content-Type: application/json'
    En V3 : indiquer l'argument 'true' pour contrôler le token d'accès Jeedom
    En V4 : autoriser l'exécution d'une méthode 'action' en GET en indiquant le(s) nom(s) de(s) action(s) dans un tableau en argument
  */
  ajax::init();
  // $led_template=file_get_contents('./desktop/json/led_template.json');
  if (init('action') == 'addLEDS') {
    $leds = json_decode(init('leds'), true);
    include_file('core', 'virtual', 'class', 'virtual');
    $ledCreated = 0;

    foreach ($leds as $led) {
      $virtual = new virtual();
      $virtual->setEqType_name('virtual');
      $virtual->setName($led['name']);
      $virtual->setLogicalId('led_' . uniqid());
      $virtual->setObject_id(2); // Objet parent
      $virtual->setIsEnable(1);
      $virtual->setIsVisible(1);
      $virtual->save();

      $cmd = new virtualCmd();
      $cmd->setName('Etat');
      $cmd->setEqLogic_id($virtual->getId());
      $cmd->setType('info');
      $cmd->setLogicalId('etat');
      $cmd->setSubType('binary');
      $cmd->setIsVisible(0);
      $cmd->setIsHistorized(1);
      $cmd->save();

      $cmdOn = new virtualCmd();
      $cmdOn->setName('On');
      $cmdOn->setEqLogic_id($virtual->getId());
      $cmdOn->setType('action');
      $cmdOn->setSubType('other');
      $cmdOn->setConfiguration('actionReturnCmd', array(
        array(
          'cmd' => $cmdEtat->getId(),
          'value' => 1
        )
      ));
      $cmdOn->save();

      $cmdOff = new virtualCmd();
      $cmdOff->setName('Off');
      $cmdOff->setEqLogic_id($virtual->getId());
      $cmdOff->setType('action');
      $cmdOff->setLogicalId('off');
      $cmdOff->setSubType('other');
      $cmdOff->setValue($cmd->getId());
      $cmdOff->setConfiguration('updateCmdId', $cmd->getId());
      $cmdOff->setConfiguration('updateCmdToValue', 0);
      $cmdOff->setIsVisible(1);
      $cmdOff->save();

      $ledCreated++;

    }
    ajax::success($ledCreated . ' objet(s) créé(s) avec succès');
  }

  throw new Exception(__('Aucune méthode correspondante à', __FILE__) . ' : ' . init('action'));
  /*     * *********Catch exeption*************** */
} catch (Exception $e) {
  ajax::error(displayException($e), $e->getCode());
}
