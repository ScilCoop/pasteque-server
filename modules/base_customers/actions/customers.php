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

$srv = new \Pasteque\CustomersService();
if (isset($_GET['delete-customer'])) {
	$srv->delete($_GET['delete-customer']);
}
$customers = $srv->getAll(true);

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Customers", PLUGIN_NAME)));
//Buttons
$buttons = \Pasteque\addButton(\i18n('Add a customer', PLUGIN_NAME),\Pasteque\get_module_url_action(PLUGIN_NAME, "customer_edit"));
echo \Pasteque\row(\Pasteque\buttonGroup($buttons));
//Information
\Pasteque\tpl_msg_box($message, $error);
//Counter
echo \Pasteque\row(\Pasteque\counterDiv(\i18n("%d customers", PLUGIN_NAME, count($customers))));

if (count($customers) == 0) {
	echo \Pasteque\errorDiv(\i18n("No customer found", PLUGIN_NAME));
}
else {
	$content[0][0] = \i18n("Customer.number");
	$content[0][1] = \i18n("Customer.key");
	$content[0][2] = \i18n("Customer.dispName");
	$content[0][3] = "";
	$i = 1;
	foreach ($customers as $cust) {
		$btn_group = \Pasteque\editButton(\i18n('Edit', PLUGIN_NAME), \Pasteque\get_module_url_action(PLUGIN_NAME, 'customer_edit', array("id" => $cust->id)));
		$btn_group .= \Pasteque\deleteButton(\i18n('Delete', PLUGIN_NAME), \Pasteque\get_current_url() . "&delete-customer=" . $cust->id);
		$content[$i][0] = $cust->number;
		$content[$i][1] = $cust->key;
		$content[$i][2] = $cust->dispName;
		$content[$i][3] = \Pasteque\buttonGroup($btn_group, "pull-right");
		$i++;
	}
	echo \Pasteque\row(\Pasteque\standardTable($content));
}
?>
