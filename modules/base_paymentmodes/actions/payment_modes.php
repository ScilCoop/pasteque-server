<?php
//    Pastèque Web back office, Payment modes module
//
//    Copyright (C) 2015-2016 Scil (http://scil.coop)
//       Pierre Ducroquet, Philippe Pary
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

namespace BasePaymentModes;

$message = NULL;
$error = NULL;
$modeSrv = new \Pasteque\PaymentModesService();
if (isset($_POST['toggle-paymentmode'])) {
	if ($modeSrv->toggle($_POST['toggle-paymentmode'])) {
		$message = \i18n("Changes saved");
	} else {
		$error = \i18n("Unable to save changes");
	}
}

$paymentModes = $modeSrv->getAll();

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Payement modes", PLUGIN_NAME)));
//Information
\Pasteque\tpl_msg_box($message, $error);

$content[0][0] = \i18n("PaymentMode.code");
$content[0][1] = \i18n("PaymentMode.label");
$content[0][2] = \i18n("PaymentMode.backLabel");
$content[0][3] = "";

$i = 1;
foreach ($paymentModes as $paymentMode) {
	if ($paymentMode->system) {
		continue;
	}
	$content[$i][0] = $paymentMode->code;
	$content[$i][1] = $paymentMode->label;
	$content[$i][2] = $paymentMode->backLabel;
	$content[$i][3] = "<form action=\"" . \Pasteque\get_current_url() . "\" method=\"post\" enctype=\"multipart/form-data\">\n<input type=\"hidden\" name=\"toggle-paymentmode\" value=\"" . $paymentMode->id ."\">";
	if ($paymentMode->active === true) {
		$action = \i18n("Disable");
		$content[$i][3] .= \Pasteque\form_button($action,"btn-delete");
	}
	else {
		$action = \i18n("Enable");
		$content[$i][3] .= \Pasteque\form_button($action);
	}
	$content[$i][3] .= "</form>";
	$i++;
}
echo \Pasteque\row(\Pasteque\standardTable($content));
?>
