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

namespace StockMultilocations;

$message = null;
$error = null;
$srv = new \Pasteque\LocationsService();

if (isset($_POST['delete-location'])) {
	if ($srv->delete($_POST['delete-location'])) {
		$message = \i18n("Changes saved");
	} else {
		$error = \i18n("Unable to delete location. A location cannot be deleted when stock is assigned to it.", PLUGIN_NAME);
	}
}

$locations = $srv->getAll();

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Locations", PLUGIN_NAME)));
//Buttons
$buttons = \Pasteque\addButton(\i18n("New location", PLUGIN_NAME), \Pasteque\get_module_url_action(PLUGIN_NAME, 'location_edit'));
echo \Pasteque\row(\Pasteque\buttonGroup($buttons));
//Informations
\Pasteque\tpl_msg_box($message, $error);
//Counter
echo \Pasteque\row(\Pasteque\counterDiv(\i18n("%d locations", PLUGIN_NAME, count($locations))));

if (count($locations) == 0) {
	echo \Pasteque\errorDiv(\i18n("No location found", PLUGIN_NAME));
}
else {
	$content[0][0] = \i18n("Location.label");
	$i = 1;
	foreach ($locations as $location) {
		$content[$i][0] = $location->label;
		$btn_group = \Pasteque\editButton(\i18n('Edit', PLUGIN_NAME), \Pasteque\get_module_url_action(PLUGIN_NAME, 'location_edit', array("id" => $location->id)));
		$content[$i][0] .= \Pasteque\buttonGroup($btn_group, "pull-right");
		$i++;
	}
	echo \Pasteque\row(\Pasteque\standardTable($content));
}
?>
