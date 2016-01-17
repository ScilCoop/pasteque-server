<?php
//    Pastèque Web back office, Currencies module
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

// categories action

namespace BaseCurrencies;

$message = NULL;
$error = NULL;
$currSrv = new \Pasteque\CurrenciesService();
if (isset($_GET['delete-currency'])) {
	if ($currSrv->delete($_GET['delete-currency'])) {
		$message = \i18n("Changes saved");
	} else {
		$error = \i18n("Unable to save changes");
	}
}

$currencies = $currSrv->getAll();

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Currencies", PLUGIN_NAME)));
//Button
$buttons = \Pasteque\addButton(\i18n("Add a currency", PLUGIN_NAME), \Pasteque\get_module_url_action(PLUGIN_NAME, "currency_edit"));
echo \Pasteque\row(\Pasteque\buttonGroup($buttons));
//Information
\Pasteque\tpl_msg_box($message, $error);

if(count($currencies) == 0) {
	echo \Pasteque\errorDiv(\i18n("No currency found", PLUGIN_NAME));
}
else {
	$content[0][0] = \i18n("Currency.label");
	$content[0][1] = \i18n("Currency.rate");
	$i = 1;
	foreach ($currencies as $currency) {
		$content[$i][0] = $currency->label;
		if ($currency->isMain) {
			$content[$i][1] = \i18n("Main", PLUGIN_NAME);
		} else {
			$content[$i][1] = $currency->rate;
		}
		$btn_group = \Pasteque\editButton(\i18n('Edit', PLUGIN_NAME), \Pasteque\get_module_url_action(PLUGIN_NAME, 'currency_edit', array("id" => $currency->id)));
		$btn_group .= \Pasteque\deleteButton(\i18n('Delete', PLUGIN_NAME), \Pasteque\get_current_url() . "&delete-currency=" . $tax->id);
		$content[$i][1] .= \Pasteque\buttonGroup($btn_group, "pull-right");
	}
	echo \Pasteque\row(\Pasteque\standardTable($content));
}
?>
