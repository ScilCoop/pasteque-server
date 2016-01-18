<?php
//    Pastèque Web back office, Products module
//
//    Copyright (C) 2013-2016 Scil (http://scil.coop)
//        Cédric Houbart, Philippe Pary philippe@scil.coop
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

namespace ProductTariffAreas;

$message = null;
$error = null;
$srv = new \Pasteque\TariffAreasService();
if (isset($_GET['delete-area'])) {
	if ($srv->delete($_GET['delete-area'])) {
		$message = \i18n("Changes saved");
	} else {
		$error = \i18n("Unable to save changes");
	}
}

$areas = $srv->getAll();

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Tariff areas", PLUGIN_NAME)));
//Buttons
$buttons = \Pasteque\addButton(\i18n("Add an area", PLUGIN_NAME),\Pasteque\get_module_url_action(PLUGIN_NAME, "area_edit"));
$buttons .= \Pasteque\importButton(\i18n("Import areas", PLUGIN_NAME),\Pasteque\get_module_url_action(PLUGIN_NAME, "areas_import"));
//Information
\Pasteque\tpl_msg_box($message, $error);
//Counter
echo \Pasteque\row(\Pasteque\counterDiv(\i18n("%d tariff areas", PLUGIN_NAME, count($areas))));

if (count($areas) == 0) {
	echo \Pasteque\errorDiv(\i18n("No area found", PLUGIN_NAME));
}
else {
	$content[0][0] = \i18n("TariffArea.label");
	$i = 1;
	foreach ($areas as $area) {
		$content[$i][0] = $area->label;
		$btn_group = \Pasteque\editButton(\i18n("Edit", PLUGIN_NAME), \Pasteque\get_module_url_action(PLUGIN_NAME, "area_edit", array("id" => $area->id)));
		$btn_group .= \Pasteque\deleteButton(\i18n("Delete", PLUGIN_NAME), \Pasteque\get_current_url() . "&delete-area=" . $area->id);
		$content[$i][0] .= \Pasteque\buttonGroup($btn_group, "pull-right");
	}
	echo \Pasteque\row(\Pasteque\standardTable($content));
}
?>
