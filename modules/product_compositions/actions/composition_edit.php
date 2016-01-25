<?php
//    Pastèque Web back office, Products module
//
//    Copyright (C) 2013-2016 Scil (http://scil.coop)
//          Cédric Houbart, Philippe Pary
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

namespace ProductCompositions;

$error = null;
$message = null;
$modules = \Pasteque\get_loaded_modules(\Pasteque\get_user_id());
$discounts = false;
if (in_array("product_discounts", $modules)) {
	$discounts = true;
}

$categories = \Pasteque\CategoriesService::getAll();
$products = \Pasteque\ProductsService::getAll();
$taxes = \Pasteque\TaxesService::getAll();

function parseSubgroups($data, $products) {
	$jsSubgroups = json_decode($data);
	$subgroups = array();
	foreach ($jsSubgroups as $jsSubgroup) {
		$subgroup = new \Pasteque\Subgroup(null, $jsSubgroup->label,
				$jsSubgroup->dispOrder, false);
		foreach ($jsSubgroup->prodIds as $prdId) {
			$dispOrder = 0;
			foreach ($products as $product) {
				if ($product->id == $prdId) {
					$dispOrder = $product->dispOrder;
					break;
				}
			}
			$subgroupProd = new \Pasteque\SubgroupProduct($prdId, null, $dispOrder);
			$subgroup->addProduct($subgroupProd);
		}
		$subgroups[] = $subgroup;
	}
	return $subgroups;
}

if (isset($_POST['id'])) {
	// Update composition
	$catId = \Pasteque\CompositionsService::CAT_ID;
	$dispOrder = $_POST['dispOrder'] == "" ? NULL : $_POST['dispOrder'];
	$taxCatId = $_POST['taxCatId'];
	if ($_FILES['image']['tmp_name'] !== "") {
		$img = file_get_contents($_FILES['image']['tmp_name']);
	} else if ($_POST['clearImage']) {
		$img = NULL;
	} else {
		$img = "";
	}
	$scaled = isset($_POST['scaled']) ? 1 : 0;
	$visible = isset($_POST['visible']) ? 1 : 0;
	$discountEnabled = FALSE;
	$discountRate = 0.0;
	if (isset($_POST['discountRate'])) {
		$discountEnabled = isset($_POST['discountEnabled']) ? 1 : 0;
		$discountRate = $_POST['discountRate'];
	}
	$cmp = \Pasteque\Composition::__build($_POST['id'], $_POST['reference'],
			$_POST['label'], $_POST['realsell'], $catId, null, $dispOrder,
			$taxCatId, $visible, $scaled, $_POST['priceBuy'], null,
			$_POST['barcode'], $img != null,
			$discountEnabled, $discountRate);
	$cmp->groups = parseSubgroups($_POST['subgroupData'], $products);
	if (\Pasteque\CompositionsService::update($cmp, $img, null)) {
		$message = \i18n("Changes saved", PLUGIN_NAME);
	} else {
		$error = \i18n("Unable to save changes", PLUGIN_NAME);
	}
} else if (isset($_POST['reference'])) {
	// Create composition
	$catId = \Pasteque\CompositionsService::CAT_ID;
	$dispOrder = $_POST['dispOrder'] == "" ? NULL : $_POST['dispOrder'];
	$taxCatId = $_POST['taxCatId'];
	if ($_FILES['image']['tmp_name'] !== "") {
		$img = file_get_contents($_FILES['image']['tmp_name']);
	} else {
		$img = NULL;
	}
	$scaled = isset($_POST['scaled']) ? 1 : 0;
	$visible = isset($_POST['visible']) ? 1 : 0;
	$discountEnabled = FALSE;
	$discountRate = 0.0;
	if (isset($_POST['discountRate'])) {
		$discountEnabled = isset($_POST['discountEnabled']) ? 1 : 0;
		$discountRate = $_POST['discountRate'];
	}
	$cmp = new \Pasteque\Product($_POST['reference'], $_POST['label'],
			$_POST['sell'], $catId, null, $dispOrder, $taxCatId,
			$visible, $scaled, $_POST['priceBuy'], null, $_POST['barcode'],
			$img !== null, $discountEnabled, $discountRate);
	$cmp->groups = parseSubgroups($_POST['subgroupData'], $products);
	if (\Pasteque\CompositionsService::create($cmp, $img, null)) {
		$message = \i18n("Changes saved", PLUGIN_NAME);
	} else {
		$error = \i18n("Unable to save changes", PLUGIN_NAME);
	}
}

if (isset($_GET['productId'])) {
	$composition  = \Pasteque\CompositionsService::get($_GET['productId']);
	$taxCat = \Pasteque\TaxesService::get($composition->taxCatId);
	$tax = $taxCat->getCurrentTax();
	$vatprice = $composition->priceSell * (1 + $tax->rate);
	$price = sprintf("%.2f", $composition->priceSell);
	$priceBuy = sprintf("%.2f",$composition->priceBuy);
} else {
	$vatprice = "";
	$price = "";
	$priceBuy = "";
	$composition = NULL;
}

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Composition edit", PLUGIN_NAME)));
//Information
\Pasteque\tpl_msg_box($message, $error);

$content = "";
if ($composition !== null) {
	$content .= \Pasteque\form_hidden("id", $composition, "id");
}

$legend = \i18n("Display", PLUGIN_NAME);
$displayData = \Pasteque\form_input("edit", "Product", $composition, "label", "string", array("required" => true));
$displayData .= \Pasteque\row(\Pasteque\form_file("image","image",\i18n("Image", PLUGIN_NAME)));
$displayData .= \Pasteque\form_value_hidden("clearImage", "clearImage", "0");
if ($composition !== null && $composition->hasImage === true) {
	$displayData .= \Pasteque\row("<img id=\"img\" class=\"image-preview\" src=\"?" . \Pasteque\PT::URL_ACTION_PARAM . "=img&w=product&id=" . $composition->id . "\">");
	$buttons .= \Pasteque\jsDeleteButton(\i18n("Delete"), "javascript:clearImage();");
	$buttons .= \Pasteque\jsAddButton(\i18n("Restore"), "javascript:restoreImage();");
	$displayData .= \Pasteque\buttonGroup($buttons);
}
$content .= \Pasteque\form_fieldset($legend,$displayData);

$legend = \i18n("Price", PLUGIN_NAME);
$displayData = \Pasteque\form_input("edit", "Product", $composition, "taxCatId", "pick", array("model" => "TaxCategory"));
$displayData .= \Pasteque\form_number("sellvat", $vatprice, \i18n("Sell price + taxes", PLUGIN_NAME),"0.1");
$displayData .= \Pasteque\form_hidden("realsell","realsell",$composition->priceSell);
$displayData .= \Pasteque\form_number("sell", $price, \i18n("Product.priceSell"), null, null, null, null, true);
$displayData .= \Pasteque\form_number("priceBuy", $priceBuy, \i18n("Product.priceBuy"),"0.1");
$displayData .= \Pasteque\form_number("margin", "", \i18n("Margin", PLUGIN_NAME), null, null, null, null, true);
if ($discounts) {
	$displayData .= \Pasteque\form_input("edit", "Product", $composition, "discountEnabled", "boolean", array("default" => FALSE));
	$displayData .= \Pasteque\form_input("edit", "Product", $composition, "discountRate", "numeric");
}
$content .= \Pasteque\form_fieldset($legend,$displayData);

$legend = \i18n("Referencing", PLUGIN_NAME);
$displayData = \Pasteque\form_input("edit", "Product", $composition, "reference", "string", array("required" => true));
if ($composition != null) {
	$displayData .= \Pasteque\form_text("barcode",$composition->barcode,\i18n("Product.barcode"));
}
else {
	$displayData .= \Pasteque\form_text("barcode",null,\i18n("Product.barcode"));
}
$displayData .= "<img src=\"\" id=\"barcodeImg\">\n";
$displayData .= \Pasteque\jsAddButton(\i18n("Generate"),"javascript:generateBarcode(); return false;");
$content .= \Pasteque\form_fieldset($legend,$displayData);

$legend = \i18n("SubGroups",PLUGIN_NAME);
$row = \Pasteque\label_for(\i18n("SubGroups", PLUGIN_NAME),"listSubGr");
$row .= "<select id=\"listSubGr\" onchange=\"showSubgroup();\"></select>\n";
$displayData = \Pasteque\row($row);
$content .= \Pasteque\form_fieldset($legend,$displayData);
$content .= \Pasteque\form_text("edit-sgName",null,\i18n("Subgroup.label"),null,"javascript:editSubgroup();");
$content .= \Pasteque\form_number("edit-sgOrder",null,\i18n("Subgroup.dispOrder"),1,0);
$buttons = \Pasteque\jsAddButton(\i18n("Add subgroup", PLUGIN_NAME),"newSubgroup();");
$buttons .= \Pasteque\jsDeleteButton(\i18n("Delete subgroup", PLUGIN_NAME), "delSubgroup();");
$content .= \Pasteque\buttonGroup($buttons);
$content .= "<div id=\"product-sub-container\" class=\"product-container\"></div>\n";
$content .= \Pasteque\form_fieldSet(\i18n("Product", PLUGIN_NAME),\Pasteque\vanillaDiv(null,"catalog-picker"));
$content .= \Pasteque\form_value_hidden("subgroupData","subgroupData",null);
$content .= \Pasteque\form_save();

echo \Pasteque\form_generate(\Pasteque\get_current_url(),"post",$content,"return submitData();");

\Pasteque\init_catalog("catalog", "catalog-picker", "productPicked",
        $categories, $products);
?>

<script src="<?php echo \Pasteque\get_module_action(PLUGIN_NAME, "control.js")?>" type="text/javascript"></script>

<script type="text/javascript">
var tax_rates = new Array();
<?php foreach ($taxes as $tax) {
	echo "\ttax_rates[\"" . \Pasteque\esc_js($tax->id) . "\"] = " . $tax->getCurrentTax()->rate . ";\n";
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

/**Replace ',' by '.' and call function 'fonction'
 * @param id the id of HTML input element
 * @param fonction the function after replace*/
function changeVal(id, fonction) {
	var val = $("#" + id).val().replace(",", ".");
	jQuery(id).val(val);
	fonction();
}

jQuery("#sellvat").change(function() {changeVal(this.id, updateSellPrice)});

jQuery("#edit-taxCatId").change(function() {changeVal(this.id, updateSellPrice)});

jQuery("#sell").change(function() {changeVal(this.id, updateSellVatPrice);});

jQuery("#priceBuy").change(function() {changeVal(this.id, updateMargin);});

jQuery("#edit-discountRate").change(function() {changeVal(this.id, updateMargin);});

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

/** Add all product contain in the category */
addAllPrd = function() {
	var prdCat = catalog.productsByCategory[catalog.currentCategoryId];
	for (var i = 0; i < prdCat.length; i++) {
		productPicked(prdCat[i]);
	}
}
</script>

<script type="text/javascript">
<?php
foreach ($products as $product) {
	echo "registerProduct(\"" . \Pasteque\esc_js($product->id) . "\", \"" . \Pasteque\esc_js($product->label) . "\");\n";
}
if ($composition !== null) {
	foreach($composition->groups as $group) {
		echo "var id = addSubgroup(\"" . \Pasteque\esc_js($group->label) . "\", " . \Pasteque\esc_js($group->dispOrder) . ");\n";
		foreach($group->choices as $prod) {
			echo "addProduct(id, \"" . \Pasteque\esc_js($prod->productId) . "\");";
		}
	}
	echo("showSubgroup();\n");
} else {
	echo("addSubgroup(\"\", \"\");\n");
	echo("showSubgroup();\n");
}
?>
</script>
