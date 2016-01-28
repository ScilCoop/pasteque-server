<?php
//    Pastèque Web back office, Users module
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

// category_edit action

namespace BaseCustomers;

$message = null;
$error = null;
$discounts = false;
$tariffAreas = false;
$modules = \Pasteque\get_loaded_modules(\Pasteque\get_user_id());
if (in_array("customer_discountprofiles", $modules)) {
	$discounts = true;
}
if (in_array("product_tariffareas", $modules)) {
	$tariffAreas = true;
}

if (isset($_POST['id']) && isset($_POST['dispName'])) {
	$visible = isset($_POST['visible']) ? 1 : 0;
	if (!isset($_POST['number']) || $_POST['number'] == "") {
		$custSrv = new \Pasteque\CustomersService();
		$number = $custSrv->getNextNumber();
	} else {
		$number = $_POST['number'];
	}
	if (!isset($_POST['key']) || $_POST['key'] == "") {
		$key = $number . "-" . $_POST['dispName'];
	} else {
		$key = $_POST['key'];
	}
	$taxCatId = NULL;
	if (isset($_POST['custTaxId']) && $_POST['custTaxId'] != "") {
		$taxCatId = $_POST['custTaxId'];
	}
	$discountProfileId = null;
	if ($discounts && $_POST['discountProfileId'] !== "") {
		$discountProfileId = $_POST['discountProfileId'];
	}
	$tariffAreaId = null;
	if ($tariffAreas && $_POST['tariffAreaId'] !== "") {
		$tariffAreaId = $_POST['tariffAreaId'];
	}
	$currDebt = NULL;
	if (isset($_POST['currDebt']) && $_POST['currDebt'] != "") {
		$currDebt = $_POST['currDebt'];
	}
	$maxDebt = 0.0;
	if ($_POST['maxDebt'] !== "") {
		$maxDebt = $_POST['maxDebt'];
	}
	$debtDate = NULL;
	if (isset($_POST['debtDate']) && $_POST['debtDate'] != "") {
		$debtDate = $_POST['debtDate'];
		$debtDate = \i18nRevDateTime($debtDate);
	}
	$prepaid = 0.0;
	if ($_POST['prepaid'] != "") {
		$prepaid = $_POST['prepaid'];
	}
	$expireDate = NULL;
	if (isset($_POST['expireDate']) && $_POST['expireDate'] !== "") {
		$expireDate = $_POST['expireDate'];
		$expireDate = \i18nRevDate($expireDate);
	}
	$cust = \Pasteque\Customer::__build($_POST['id'], $number, $key,
			$_POST['dispName'], $_POST['card'], $taxCatId, $discountProfileId,
			$tariffAreaId, $prepaid, $maxDebt, $currDebt, $debtDate,
			$_POST['firstName'], $_POST['lastName'], $_POST['email'],
			$_POST['phone1'], $_POST['phone2'], $_POST['fax'], $_POST['addr1'],
			$_POST['addr2'], $_POST['zipCode'], $_POST['city'],
			$_POST['region'], $_POST['country'], $_POST['note'],
			$visible, $expireDate);
	if (\Pasteque\CustomersService::update($cust)) {
		$message = \i18n("Changes saved");
	} else {
		$error = \i18n("Unable to save changes");
	}
} else if (isset($_POST['dispName'])) {
	$visible = isset($_POST['visible']) ? 1 : 0;
	if (!isset($_POST['number']) || $_POST['number'] == "") {
		$custSrv = new \Pasteque\CustomersService();
		$number = $custSrv->getNextNumber();
	} else {
		$number = $_POST['number'];
	}
	if (!isset($_POST['key']) || $_POST['key'] == "") {
		$key = $number . "-" . $_POST['dispName'];
	} else {
		$key = $_POST['key'];
	}
	$taxCatId = null;
	if (isset($_POST['custTaxId']) && $_POST['custTaxId'] != "") {
		$taxCatId = $_POST['custTaxId'];
	}
	$discountProfileId = null;
	if ($discounts && $_POST['discountProfileId'] !== "") {
		$discountProfileId = $_POST['discountProfileId'];
	}
	$tariffAreaId = null;
	if ($tariffAreas && $_POST['tariffAreaId'] !== "") {
		$tariffAreaId = $_POST['tariffAreaId'];
	}
	$maxDebt = 0.0;
	if ($_POST['maxDebt'] !== "") {
		$maxDebt = $_POST['maxDebt'];
	}
	$prepaid = 0.0;
	if ($_POST['prepaid'] != "") {
		$prepaid = $_POST['prepaid'];
	}
	$expireDate = NULL;
	if (isset($_POST['expireDate']) && $_POST['expireDate'] != "") {
		$expireDate = $_POST['expireDate'];
		$expireDate = \i18nRevDate($expireDate);
	}
	$cust = new \Pasteque\Customer($number, $key,
			$_POST['dispName'], $_POST['card'], $taxCatId, $discountProfileId,
			$tariffAreaId, $prepaid, $maxDebt, $currDebt, $debtDate,
			$_POST['firstName'], $_POST['lastName'], $_POST['email'],
			$_POST['phone1'], $_POST['phone2'], $_POST['fax'], $_POST['addr1'],
			$_POST['addr2'], $_POST['zipCode'], $_POST['city'],
			$_POST['region'], $_POST['country'], $_POST['note'],
			$visible, $expireDate);
	$id = \Pasteque\CustomersService::create($cust);
	if ($id !== false) {
		$message = \i18n("Customer saved. <a href=\"%s\">Go to the customer page</a>.", PLUGIN_NAME, \Pasteque\get_module_url_action(PLUGIN_NAME, 'customer_edit', array('id' => $id)));
	} else {
		$error = \i18n("Unable to save changes");
	}
}

$cust = null;
$currDebt = "";
$prepaid = 0;
$str_debtDate = "";
$str_expireDate = "";
if (isset($_GET['id'])) {
	$cust = \Pasteque\CustomersService::get($_GET['id']);
	$currDebt = $cust->currDebt;
	$prepaid = $cust->prepaid;
	if ($cust->debtDate !== NULL) {
		$str_debtDate = \i18nDateTime($cust->debtDate);
	}
	if ($cust->expireDate !== NULL) {
		$str_expireDate = \i18nDate($cust->expireDate);
	}
}

//Title
echo \Pasteque\mainTitle(\i18n("Edit a customer", PLUGIN_NAME));
//Information
\Pasteque\tpl_msg_box($message, $error);

if ($cust !== null) {
	$buttons = \Pasteque\exportButton(\i18n("Customer's diary", PLUGIN_NAME),\Pasteque\get_report_url(PLUGIN_NAME,"customers_diary","display"));
	$buttons .= \Pasteque\exportButton(\i18n("Customer's prepaid diary", PLUGIN_NAME),\Pasteque\get_report_url(PLUGIN_NAME,"customers_prepaid_diary","display"));
	echo \Pasteque\row(\Pasteque\buttonGroup($buttons));
}
?>
<form class="edit" action="<?php echo \Pasteque\get_current_url(); ?>" method="post">
    <?php 
$content .= \Pasteque\form_hidden("edit", $cust, "id");
$label = \i18n("Keys", PLUGIN_NAME);
$displayData = \Pasteque\form_input("edit", "Customer", $cust, "number", "numeric");
$displayData .= \Pasteque\form_input("edit", "Customer", $cust, "key", "string");
$displayData .= \Pasteque\form_input("edit", "Customer", $cust, "dispName", "string", array("required" => true));
$displayData .= \Pasteque\form_text("barcode",$cust->card,\i18n("Customer.card"));
$displayData .= \Pasteque\row("<img src=\"\" id=\"barcodeImg\">\n" . \Pasteque\jsAddButton(\i18n("Generate"),"javascript:generateCard(); return false;"));
$displayData .= \Pasteque\form_date("expireDate",$str_expireDate,\i18n("Customer.expireDate"));
$displayData .= \Pasteque\form_input("edit", "Customer", $cust, "visible", "boolean");
$content .= \Pasteque\form_fieldset($legend,$displayData);

//Debts
$legend = \i18n("Debt", PLUGIN_NAME);
$displayData = \Pasteque\form_number("prepaid",$prepaid,\i18n("Customer.prepaid"),null,0,null,null,true);
$displayData .= \Pasteque\form_input("edit", "Customer", $cust, "maxDebt", "numeric");
$displayData .= \Pasteque\form_number("currDebt",$currDebt,\i18n("Customer.currDebt"),null,0,null,null,true);
$displayData .= \Pasteque\form_date("debtDate",$str_debtDate,\i18n("Customer.debtDate"),true);
$content .= \Pasteque\form_fieldset($legend,$displayData);

//Misc
$legend = \i18n("Miscellaneous", PLUGIN_NAME);
$displayData = \Pasteque\form_input("edit", "Customer", $cust, "note", "text");
$displayData .= \Pasteque\form_input("edit", "Customer", $cust, "custTaxId", "pick", array("model" => "CustTaxCat", "nullable" => true));
if ($discounts) {
	$displayData .= \Pasteque\form_input("edit", "Customer", $cust, "discountProfileId", "pick", array("model" => "DiscountProfile", "nullable" => true));
}
if ($tariffAreas) {
	$displayData .= \Pasteque\form_input("edit", "Customer", $cust, "tariffAreaId", "pick", array("model" => "TariffArea", "nullable" => true));
}
$content .= \Pasteque\form_fieldset($legend,$displayData);

//Personnal data
$legend = \i18n("Personnal data", PLUGIN_NAME);
$displayData = \Pasteque\form_input("edit", "Customer", $cust, "firstName", "string");
$displayData .= \Pasteque\form_input("edit", "Customer", $cust, "lastName", "string");
$displayData .= \Pasteque\form_input("edit", "Customer", $cust, "email", "string");
$displayData .= \Pasteque\form_input("edit", "Customer", $cust, "phone1", "string");
$displayData .= \Pasteque\form_input("edit", "Customer", $cust, "phone2", "string");
$displayData .= \Pasteque\form_input("edit", "Customer", $cust, "fax", "string");
$displayData .= \Pasteque\form_input("edit", "Customer", $cust, "addr1", "string");
$displayData .= \Pasteque\form_input("edit", "Customer", $cust, "addr2", "string");
$displayData .= \Pasteque\form_input("edit", "Customer", $cust, "zipCode", "string");
$displayData .= \Pasteque\form_input("edit", "Customer", $cust, "city", "string");
$displayData .= \Pasteque\form_input("edit", "Customer", $cust, "region", "string");
$displayData .= \Pasteque\form_input("edit", "Customer", $cust, "country", "string");
$content .= \Pasteque\form_fieldset($legend,$displayData);

$content .= \Pasteque\form_save();
echo \Pasteque\form_generate(\Pasteque\get_current_url(),"post",$content);
?>
<script type="text/javascript">
	updateBarcode = function() {
		var barcode = jQuery("#barcode").val();
		var src = "?<?php echo \Pasteque\PT::URL_ACTION_PARAM; ?>=img&w=custcard&code=" + barcode;
		jQuery("#barcodeImg").attr("src", src);
	}
	updateBarcode();
	generateCard = function() {
		var num = "" + jQuery("#edit-number").val();
		if (num == "") {
			num = "" + Math.floor(Math.random() * <?php echo pow(10, \Pasteque\Customer::CARD_SIZE); ?>);
		}
		while (num.length < <?php echo \Pasteque\Customer::CARD_SIZE; ?>) {
			num = "0" + num;
		}
		var barcode = "<?php echo \Pasteque\Customer::CARD_PREFIX; ?>" + num;
		jQuery("#barcode").val(barcode);
		updateBarcode();
	}
</script>
