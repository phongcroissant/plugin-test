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

      // Création de l'équipement virtuel
      $virtual = new virtual();
      $virtual->setEqType_name('virtual');
      $virtual->setName($led['name']);
      $virtual->setLogicalId('led_' . uniqid());
      $virtual->setObject_id(2);
      $virtual->setIsEnable(1);
      $virtual->setIsVisible(1);
      $virtual->save();

      // 1) Création de la commande info
      $cmdEtat = new virtualCmd();
      $cmdEtat->setEqLogic_id($virtual->getId());
      $cmdEtat->setName('Etat');
      $cmdEtat->setType('info');
      $cmdEtat->setSubType('binary');
      $cmdEtat->setLogicalId('etat');
      $cmdEtat->setIsVisible(1);
      $cmdEtat->setConfiguration('virtualInfo', '');
      $cmdEtat->save();

      // 2) IMPORTANT : recharger l'équipement après la création de la commande info
      $virtual = virtual::byId($virtual->getId());

      // 3) Création de la commande action ON
      $cmdOn = new virtualCmd();
      $cmdOn->setEqLogic_id($virtual->getId());
      $cmdOn->setName('On');
      $cmdOn->setType('action');
      $cmdOn->setSubType('other');
      $cmdOn->setLogicalId('on');
      $cmdOn->setIsVisible(1);
      $cmdOn->setConfiguration('virtualAction', '#etat#');
      $cmdOn->setValue($cmdEtat->getId());
      $cmdOn->save();


      // // Commande action OFF
      // $cmdOff = new virtualCmd();
      // $cmdOff->setEqLogic_id($virtual->getId());
      // $cmdOff->setName('Off');
      // $cmdOff->setType('action');
      // $cmdOff->setSubType('other');
      // $cmdOff->setLogicalId('off');
      // $cmdOff->setValue($cmdEtat->getId());
      // $cmdOff->save();

      $ledCreated++;
    }
    ajax::success($ledCreated . ' objet(s) créé(s) avec succès');
  }

  throw new Exception(__('Aucune méthode correspondante à', __FILE__) . ' : ' . init('action'));
  /*     * *********Catch exeption*************** */
} catch (Exception $e) {
  ajax::error(displayException($e), $e->getCode());
}
