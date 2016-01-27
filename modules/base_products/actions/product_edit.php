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

// products action

namespace BaseProducts;

$stocks = FALSE;
$discounts = FALSE;
$attributes = FALSE;
$providers = FALSE;
$modules = \Pasteque\get_loaded_modules(\Pasteque\get_user_id());
if (in_array("product_discounts", $modules)) {
	$discounts = TRUE;
}
if (in_array("product_attributes", $modules)) {
	$attributes = TRUE;
}
if (in_array("product_providers", $modules)) {
	$providers = TRUE;
}

$message = NULL;
$error = NULL;

if (isset($_POST['id'])) {
	if (isset($_POST['reference']) && isset($_POST['label'])
			&& isset($_POST['realsell']) && isset($_POST['categoryId'])
			&& isset($_POST['taxCatId'])) {
		$catId = $_POST['categoryId'];
		$provId = $_POST['providerId'];
		$disp_order = $_POST['dispOrder'] == "" ? NULL : $_POST['dispOrder'];
		$taxCatId = $_POST['taxCatId'];
		if ($_FILES['image']['tmp_name'] !== "") {
			$output = $_FILES['image']['tmp_name'] . "thumb";
			\Pasteque\img_thumbnail($_FILES['image']['tmp_name'], $output);
			$img = file_get_contents($output);
		} else if ($_POST['clearImage']) {
			$img = NULL;
		} else {
			$img = "";
		}
		$scaled = isset($_POST['scaled']) ? 1 : 0;
		$visible = isset($_POST['visible']) ? 1 : 0;
		$discount_enabled = FALSE;
		$discount_rate = 0.0;
		if (isset($_POST['discountRate'])) {
			$discount_enabled = isset($_POST['discountEnabled']) ? 1 : 0;
			$discount_rate = $_POST['discountRate'];
		}
		$attr = null;
		if (isset($_POST['attributeSetId']) && $_POST['attributeSetId'] !== "") {
			$attr = $_POST['attributeSetId'];
		}
		$prd = \Pasteque\Product::__build($_POST['id'], $_POST['reference'],
				$_POST['label'], $_POST['realsell'], $catId, $provId, $disp_order,
				$taxCatId, $visible, $scaled, $_POST['priceBuy'], $attr,
				$_POST['barcode'], $img != null,
				$discount_enabled, $discount_rate);
		if (\Pasteque\ProductsService::update($prd, $img)) {
			$message = \i18n("Changes saved");
		} else {
			$error = \i18n("Unable to save changes");
		}
	}
} else if (isset($_POST['reference'])) {
	if (isset($_POST['reference']) && isset($_POST['label'])
			&& isset($_POST['realsell']) && isset($_POST['categoryId'])
			&& isset($_POST['taxCatId'])) {
		$catId = $_POST['categoryId'];
		$provId = $_POST['providerId'];
		$disp_order = $_POST['dispOrder'] == "" ? NULL : $_POST['dispOrder'];
		$taxCatId = $_POST['taxCatId'];
		if ($_FILES['image']['tmp_name'] !== "") {
			$img = file_get_contents($_FILES['image']['tmp_name']);
		} else {
			$img = NULL;
		}
		$scaled = isset($_POST['scaled']) ? 1 : 0;
		$visible = isset($_POST['visible']) ? 1 : 0;
		$discount_enabled = FALSE;
		$discount_rate = 0.0;
		if (isset($_POST['discountRate'])) {
			$discount_enabled = isset($_POST['discountEnabled']) ? 1 : 0;
			$discount_rate = $_POST['discountRate'];
		}
		$attr = null;
		if (isset($_POST['attributeSetId']) && $_POST['attributeSetId'] !== "") {
			$attr = $_POST['attributeSetId'];
		}
		$prd = new \Pasteque\Product($_POST['reference'], $_POST['label'],
				$_POST['realsell'], $catId, $provId, $disp_order, $taxCatId,
				$visible, $scaled, $_POST['priceBuy'], $attr, $_POST['barcode'],
				$img !== null, $discount_enabled, $discount_rate);
		$id = \Pasteque\ProductsService::create($prd, $img);
		if ($id !== FALSE) {
			$message = \i18n("Product saved. <a href=\"%s\">Go to the product page</a>.", PLUGIN_NAME, \Pasteque\get_module_url_action(PLUGIN_NAME, 'product_edit', array('id' => $id)));
		} else {
			$error = \i18n("Unable to save changes");
		}
	}
}

$product = NULL;
$vatprice = "";
$price = "";
if (isset($_GET['id'])) {
	$product = \Pasteque\ProductsService::get($_GET['id']);
	$taxCat = \Pasteque\TaxesService::get($product->taxCatId);
	$tax = $taxCat->getCurrentTax();
	$vatprice = $product->priceSell * (1 + $tax->rate);
	$price = sprintf("%.2f", $product->priceSell);
	$priceBuy = sprintf("%.2f",$product->priceBuy);
}
$taxes = \Pasteque\TaxesService::getAll();
$categories = \Pasteque\CategoriesService::getAll();
$providers = \Pasteque\ProvidersService::getAll();

$level = NULL;
if ($stocks === TRUE && $product != NULL) {
	$level = \Pasteque\StocksService::getLevel($product->id);
}

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Edit a product", PLUGIN_NAME)));
//Information
\Pasteque\tpl_msg_box($message, $error);

$form = \Pasteque\form_hidden("edit", $product, "id");

// Display data
$legend = \i18n("Display", PLUGIN_NAME);
$displayData = "\t".\Pasteque\form_input("edit", "Product", $product, "label", "string", array("required" => true));
$displayData .= "\t".\Pasteque\form_input("edit", "Product", $product, "categoryId", "pick", array("model" => "Category"));
if($providers) {
	$displayData .= \Pasteque\form_input("edit", "Product", $product, "providerId", "pick", array("model" => "Provider"));
}
$displayData .= \Pasteque\form_input("edit", "Product", $product, "visible", "boolean");
$form .= \Pasteque\form_fieldset($legend,$displayData);

// Image
$legend = \i18n("Image");
$displayData = \Pasteque\row(\Pasteque\form_file("image","image",\i18n("Image", PLUGIN_NAME)));
$form .= \Pasteque\form_value_hidden("clearImage", "clearImage", "0");
if ($product !== null && $product->hasImage === true) {
	$displayData .= \Pasteque\row("<img id=\"img\" class=\"image-preview\" src=\"?" . \Pasteque\PT::URL_ACTION_PARAM . "=img&w=product&id=" . $product->id . "\">");
	$buttons .= \Pasteque\jsDeleteButton(\i18n("Delete"), "javascript:clearImage();");
	$buttons .= \Pasteque\jsAddButton(\i18n("Restore"), "javascript:restoreImage();");
	$displayData .= \Pasteque\buttonGroup($buttons);
}
$form .= \Pasteque\form_fieldset($legend,$displayData);

// Pricing
$legend = \i18n("Price", PLUGIN_NAME);
$displayData = \Pasteque\form_value_hidden("realsell","realsell",$product->priceSell);
$displayData .= \Pasteque\form_input("edit", "Product", $product, "scaled", "boolean", array("default" => FALSE));
$displayData .= \Pasteque\form_input("edit", "Product", $product, "taxCatId", "pick", array("model" => "TaxCategory"));
$displayData .= \Pasteque\form_number("sellvat",$vatprice,\i18n("Sell price + taxes", PLUGIN_NAME),"0.01",0);
$displayData .= \Pasteque\form_number("sell",$price,\i18n("Product.priceSell"),null,0,null,null,true);
$displayData .= \Pasteque\form_number("priceBuy", $priceBuy, \i18n("Product.priceBuy"),"0.1");
$displayData .= \Pasteque\form_number("margin", "",\i18n("Margin"),null,0,null,null,true);
if ($discounts) {
	$displayData .= \Pasteque\form_input("edit", "Product", $product, "discountEnabled", "boolean", array("default" => FALSE));
	$displayData .= \Pasteque\form_input("edit", "Product", $product, "discountRate", "numeric");
}
$form .= \Pasteque\form_fieldset($legend,$displayData);

// Referencing
$legend = \i18n("Referencing", PLUGIN_NAME);
$displayData = \Pasteque\form_input("edit", "Product", $product, "reference", "string", array("required" => true));
$displayData .= \Pasteque\form_text("barcode",$product->barcode,\i18n("Product.barcode"));
$displayData .= "<img src=\"\" id=\"barcodeImg\">\n";
$displayData .= \Pasteque\jsAddButton(\i18n("Generate"),"javascript:generateBarcode(); return false;");
if ($attributes) {
	$displayData .= \Pasteque\form_input("edit", "Product", $product, "attributeSetId", "pick", array("model" => "AttributeSet", "nullable" => true));
}
$form .= \Pasteque\form_fieldset($legend,$displayData);

// That's all folks
$form .= \Pasteque\form_save();
echo \Pasteque\form_generate(\Pasteque\get_current_url(),"post",$form);

?>
<script type="text/javascript">
var tax_rates = new Array();
<?php foreach ($taxes as $tax) {
	echo "\ttax_rates['" . $tax->id . "'] = " . $tax->getCurrentTax()->rate . ",\n";
} ?>

updateSellPrice = function() {
	var sellvat = jQuery("#sellvat").val();
	var rate = tax_rates[jQuery("#edit-taxCatId").val()];
	var sell = sellvat / (1 + rate);
	jQuery("#realsell").val(sell);
	jQuery("#sell").val(sell.toFixed(2));
	updateMargin();
}
updateSellVatPrice = function() {
	// Update sellvat price
	var sell = jQuery("#sell").val();
	var rate = tax_rates[jQuery("#edit-taxCatId").val()];
	var sellvat = sell * (1 + rate);
	// Round to 2 decimals and refresh sell price to avoid unrounded payments
	sellvat = sellvat.toFixed(2);
	jQuery("#sellvat").val(sellvat);
	updateSellPrice();
	updateMargin();
}
updateMargin = function() {
	var sell = jQuery("#realsell").val();
	var buy = jQuery("#priceBuy").val();
	var ratio = sell / buy - 1;
	var margin = (ratio * 100).toFixed(2) + "%";
	var rate = (sell / buy).toFixed(2);
	jQuery("#margin").val(margin + "\t\t" + rate);
}
updateMargin();

jQuery("#sellvat").change(function() {
		var val = jQuery(this).val().replace(",", ".");
		jQuery(this).val(val);
		updateSellPrice();
		});
jQuery("#edit-taxCatId").change(function() {
		var val = jQuery(this).val().replace(",", ".");
		jQuery(this).val(val);
		updateSellPrice()
		});
jQuery("#sell").change(function() {
		var val = jQuery(this).val().replace(",", ".");
		jQuery(this).val(val);
		updateSellVatPrice()
		});
jQuery("#priceBuy").change(function() {
		var val = jQuery(this).val().replace(",", ".");
		jQuery(this).val(val);
		updateMargin()
		});
jQuery("#edit-discountRate").change(function() {
		var val = jQuery(this).val().replace(",", ".");
		jQuery(this).val(val);
		updateMargin()
		});

clearImage = function() {
	jQuery("#img").hide();
	jQuery("#clear").hide();
	jQuery("#restore").show();
	jQuery("#clearImage").val(1);
}
restoreImage = function() {
	jQuery("#img").show();
	jQuery("#clear").show();
	jQuery("#restore").hide();
	jQuery("#clearImage").val(0);
}

updateBarcode = function() {
	var barcode = jQuery("#barcode").val();
	var src = "?<?php echo \Pasteque\PT::URL_ACTION_PARAM; ?>=img&w=barcode&code=" + barcode;
	jQuery("#barcodeImg").attr("src", src);
}
updateBarcode();

jQuery("#barcode").change(updateBarcode);

generateBarcode = function() {
	var first = Math.floor(Math.random() * 9) + 1;
	var code = new Array();
	code.push(first);
	for (var i = 0; i < 11; i++) {
		var num = Math.floor(Math.random() * 10);
		code.push(num);
	}
	var checksum = 0;
	for (var i = 0; i < code.length; i++) {
		var weight = 1;
		if (i % 2 == 1) {
			weight = 3;
		}
		checksum = checksum + weight * code[i];
	}
	checksum = checksum % 10;
	if (checksum != 0) {
		checksum = 10 - checksum;
	}
	code.push(checksum);
	var barcode = code.join("");
	jQuery("#barcode").val(barcode);
	updateBarcode();
}
</script>
