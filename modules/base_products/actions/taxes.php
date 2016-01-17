<?php
//    Pastèque Web back office, Products module
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

// List all tax categories

namespace BaseProducts;

$message = NULL;
$error = NULL;
if (isset($_GET['delete-taxcat'])) {
	if (\Pasteque\TaxesService::deleteCat($_GET['delete-taxcat'])) {
		$message = \i18n("Changes saved");
	} else {
		$error = \i18n("Unable to delete tax. Tax cannot be deleted when in use.", PLUGIN_NAME);
	}
}

$taxes = \Pasteque\TaxesService::getAll();

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Taxes", PLUGIN_NAME)));
//Buttons
$buttons = \Pasteque\addButton(\i18n("Add a tax", PLUGIN_NAME), \Pasteque\get_module_url_action(PLUGIN_NAME, 'tax_edit'));
echo \Pasteque\row(\Pasteque\buttonGroup($buttons));
//Informations
\Pasteque\tpl_msg_box($message, $error);
//Counter
echo \Pasteque\row(\Pasteque\counterDiv(\i18n("%d taxes", PLUGIN_NAME, count($taxes))));

if (count($taxes) == 0) {
	echo \Pasteque\errorDiv(\i18n("No tax found", PLUGIN_NAME));
}
else {
	$content[0][0] = \i18n("TaxCat.label");
	$i = 1;
	foreach ($taxes as $tax) {
		$btn_group = \Pasteque\editButton(\i18n('Edit', PLUGIN_NAME), \Pasteque\get_module_url_action(PLUGIN_NAME, 'tax_edit', array("id" => $tax->id)));
		$btn_group .= \Pasteque\deleteButton(\i18n('Delete', PLUGIN_NAME), \Pasteque\get_current_url() . "&delete-taxcat=" . $tax->id);
		$content[$i][0] = $tax->label;
		$content[$i][0] .= \Pasteque\buttonGroup($btn_group, "pull-right");
		$i++;
	}
	echo \Pasteque\row(\Pasteque\standardTable($content));
}

?>
