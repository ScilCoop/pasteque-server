<?php
//    Pastèque Web back office, Customers module
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

namespace BaseCustomers;

if (isset($_GET['delete-custtax'])) {
	\Pasteque\CustTaxCatsService::delete($_GET['delete-custtax']);
}
$custTaxCats = \Pasteque\CustTaxCatsService::getAll();

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Customer's tax categories", PLUGIN_NAME)));
//Buttons
$buttons = \Pasteque\addButton(\i18n("Add a tax category", PLUGIN_NAME), \Pasteque\get_module_url_action(PLUGIN_NAME, "cust_tax_edit"));
echo \Pasteque\row(\Pasteque\buttonGroup($buttons));
//Information
\Pasteque\tpl_msg_box($message, $error);
//Counter
echo \Pasteque\row(\Pasteque\counterDiv(\i18n("%d tax categories", PLUGIN_NAME, count($custTaxCats))));

if (count($custTaxCats) == 0) {
	echo \Pasteque\errorDiv(\i18n("No customer tax found", PLUGIN_NAME));
}
else {
	$content[0][0] = \i18n("CustTaxCat.label");
	$i = 1;
	foreach ($custTaxCats as $custTaxCat) {
		$btn_group = \Pasteque\editButton(\i18n("Edit"), \Pasteque\get_module_url_action(PLUGIN_NAME, "cust_tax_edit", array("id" => $custTaxCat->id)));
		$btn_group .= \Pasteque\deleteButton(\i18n("Delete"), \Pasteque\get_current_url()."&delete-custtax=" . $custTaxCat->id);
		$content[$i][0] = $custTaxCat->label;
		$content[$i][0] .= \Pasteque\buttonGroup($btn_group, "pull-right");
		$i++;
	}
	echo \Pasteque\row(\Pasteque\standardTable($content));
}
?>
