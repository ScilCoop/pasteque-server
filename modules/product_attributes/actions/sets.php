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

namespace ProductAttributes;

$message = null;
$error = null;

if (isset($_GET['delete-set'])) {
	if (\Pasteque\AttributesService::deleteSet($_GET['delete-set'])) {
		$message = \i18n("Changes saved");
	} else {
		$error = \i18n("Unable to save changes");
	}
}

$sets = \Pasteque\AttributesService::getAll();
//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Attribute sets", PLUGIN_NAME)));
//Buttons
$buttons = \Pasteque\addButton(\i18n("Add an attribute set", PLUGIN_NAME),\Pasteque\get_module_url_action(PLUGIN_NAME, "set_edit"));
echo \Pasteque\buttonGroup($buttons);
//Information
\Pasteque\tpl_msg_box($message, $error);
//Counter
echo \Pasteque\row(\Pasteque\counterDiv(\i18n("%d sets", PLUGIN_NAME, count($sets))));

if (count($sets) == 0) {
	echo \Pasteque\row(\Pasteque\errorDiv(\i18n("No attribute set found", PLUGIN_NAME)));
}
else {
	$content[0][0] = \i18n("AttributeSet.label");
	$i = 1;
	foreach ($sets as $set) {
		$content[$i][0] = $set->label;
		$btn_group = \Pasteque\editButton(\i18n("Edit", PLUGIN_NAME), \Pasteque\get_module_url_action(PLUGIN_NAME, "set_edit", array("id" => $set->id)));
		$btn_group .= \Pasteque\deleteButton(\i18n("Delete", PLUGIN_NAME), \Pasteque\get_current_url() . "&delete-set=" . $set->id);
		$content[$i][0] .= \Pasteque\buttonGroup($btn_group, "pull-right");
		$i++;
	}
	echo \Pasteque\row(\Pasteque\standardTable($content));
}
?>
