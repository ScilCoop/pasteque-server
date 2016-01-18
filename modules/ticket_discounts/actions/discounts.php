<?php
//    Pastèque Web back office, Products module
//
//    Copyright (C) 2015-2016 Scil (http://scil.coop)
//        Philippe Pary
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

// discounts action

namespace TicketProducts;

$message = NULL;
$error = NULL;

if (isset($_GET['delete-discount'])) {
	if (\Pasteque\DiscountsService::deleteDis($_GET['delete-discount'])) {
		$message = \i18n("Changes saved");
	} else {
		$error = \i18n("Unable to save changes");
	}
}

$discounts = \Pasteque\DiscountsService::getAll();

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Discounts", PLUGIN_NAME)));
//Buttons
$buttons = \Pasteque\addButton(\i18n("Add a discount campain", PLUGIN_NAME),\Pasteque\get_module_url_action(PLUGIN_NAME, "discount_edit"));
$buttons .= \Pasteque\importButton(\i18n("Import discounts", PLUGIN_NAME),\Pasteque\get_module_url_action(PLUGIN_NAME, "discountsManagement"));
echo \Pasteque\row(\Pasteque\buttonGroup($buttons));
//Information
\Pasteque\tpl_msg_box($message, $error);
//Counter
echo \Pasteque\row(\Pasteque\counterDiv(\i18n("%d discounts", PLUGIN_NAME, count($discounts))));

if(count($discounts) == 0) {
	echo \Pasteque\errorDiv(\i18n("No discount campain found", PLUGIN_NAME));
}
else {
	$content[0][0] = \i18n("label", PLUGIN_NAME);
	$content[0][1] = \i18n("startDate", PLUGIN_NAME);
	$content[0][2] = \i18n("endDate", PLUGIN_NAME);
	$content[0][3] = \i18n("rate", PLUGIN_NAME);
	$i = 1;
	foreach($discounts as $discount) {
		$content[$i][0] = $discount->label;
		$content[$i][1] = $discount->startDate;
		$content[$i][2] = $discount->endDate;
		$content[$i][3] = $discount->rate;
		$btn_group = \Pasteque\editButton(\i18n("Edit", PLUGIN_NAME), \Pasteque\get_module_url_action(PLUGIN_NAME, "discount_edit", array("id" => $discount->id)));
		$btn_group .= \Pasteque\deleteButton(\i18n("Delete", PLUGIN_NAME), \Pasteque\get_current_url() . "&delete-discount=" . $discount->id);
		$content[$i][3] .= \Pasteque\buttonGroup($btn_group, "pull-right");
		$i++;
	}
	echo \Pasteque\row(\Pasteque\standardTable($content));
}
?>
