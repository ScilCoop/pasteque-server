<?php
//    Pastèque Web back office, Users module
//
//    Copyright (C) 2013-2016 Scil (http://scil.coop)
//        Philippe Pary philippe@scil.coop
//
//    This file is part of Pastèque.
//
//    Pastèque is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    Pastèque is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with Pastèque.  If not, see <http://www.gnu.org/licenses/>.

namespace BaseCashRegisters;

$message = null;
$error = null;
$srv = new \Pasteque\CashRegistersService();

if (isset($_GET['delete-cashreg'])) {
	if ($srv->delete($_GET['delete-cashreg'])) {
		$message = \i18n("Changes saved");
	} else {
		$error = \i18n("Unable to save changes");
	}
}

$cashRegs = $srv->getAll();
//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Cash registers", PLUGIN_NAME)));
//Buttons
$buttons = \Pasteque\tpl_btn('btn', \Pasteque\get_module_url_action(PLUGIN_NAME, "cashregister_edit"),
        \i18n('New cash register', PLUGIN_NAME), 'img/btn_add.png');
echo \Pasteque\row(\Pasteque\buttonGroup($buttons));
//Information
\Pasteque\tpl_msg_box($message, $error);
//Counter
echo \Pasteque\row(\Pasteque\counterDiv(\i18n("%d cash registers", PLUGIN_NAME, count($cashRegs))));

$content[0][0] = \i18n("CashRegister.label");

$i = 1;
foreach ($cashRegs as $cashReg) {
	$btn_group = \Pasteque\editButton(\i18n("Edit", PLUGIN_NAME), \PAsteque\get_module_url_action(PLUGIN_NAME, "cashregister_edit", array("id" => $cashReg->id)));
	$btn_group .= \Pasteque\deleteButton(\i18n("Delete", PLUGIN_NAME), \Pasteque\get_current_url() . "&delete-cashreg=". $cashReg->id);
	$content[$i][0] = $cashReg->label;
	$content[$i][0] .= \Pasteque\buttonGroup($btn_group, "pull-right");
	$i++;
}
echo \Pasteque\row(\Pasteque\standardTable($content));
?>
