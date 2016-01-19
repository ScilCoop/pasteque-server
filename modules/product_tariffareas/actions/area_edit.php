<?php
//    Pastèque Web back office, Stocks module
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

namespace ProductTariffAreas;

$message = null;
$error = null;
$srv = new \Pasteque\TariffAreasService();

if (isset($_POST['id'])) {
	// Edit the area
	$area = \Pasteque\TariffArea::__build($_POST['id'], $_POST['label'],
			$_POST['dispOrder']);
	foreach ($_POST as $key => $value) {
		if (strpos($key, "price-") === 0) {
			$productId = substr($key, 6);
			$product = \Pasteque\ProductsService::get($productId);
			$taxCat = \Pasteque\TaxesService::get($product->taxCatId);
			$tax = $taxCat->getCurrentTax();
			$price = $value / (1 + $tax->rate);
			$area->addPrice($productId, $price);
		}
	}
	if ($srv->update($area)) {
		$message = \i18n("Changes saved");
	} else {
		$error = \i18n("Unable to save changes");
	}
} else if (isset($_POST['label'])) {
	$area = new \Pasteque\TariffArea($_POST['label'],
			$_POST['dispOrder']);
	foreach ($_POST as $key => $value) {
		if (strpos($key, "price-") === 0) {
			$productId = substr($key, 6);
			$product = \Pasteque\ProductsService::get($productId);
			$taxCat = \Pasteque\TaxesService::get($product->taxCatId);
			$tax = $taxCat->getCurrentTax();
			$price = $value / (1 + $tax->rate);
			$area->addPrice($productId, $price);
		}
	}
	if ($srv->create($area)) {
		$message = \i18n("Changes saved");
	} else {
		$error = \i18n("Unable to save changes");
	}
}


$area = null;
if (isset($_GET['id'])) {
	$area = $srv->get($_GET['id']);
}
$categories = \Pasteque\CategoriesService::getAll();
$products = \Pasteque\ProductsService::getAll();

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Tariff area", PLUGIN_NAME)));
//Information
\Pasteque\tpl_msg_box($message, $error);

$content = \Pasteque\form_hidden("edit", $area, "id");
$content .= \Pasteque\form_input("edit", "TariffArea", $area, "label", "string", array("required" => true));
$content .= \Pasteque\form_input("edit", "TariffArea", $area, "dispOrder", "numeric", array("required" => true));

$content .= \Pasteque\vanillaDiv("", "catalog-picker");

$table[0][0] = "";
$table[0][1] = \i18n("Product.reference");
$table[0][2] = \i18n("Product.label");
$table[0][3] = \i18n("Price", PLUGIN_NAME);
$table[0][4] = \i18n("Area price", PLUGIN_NAME);
$content .= \Pasteque\standardTable($table);

$content .= \Pasteque\form_send();

echo \Pasteque\row(\Pasteque\form_generate(\Pasteque\get_current_url(), "post", $content));

\Pasteque\init_catalog("catalog", "catalog-picker", "addProduct",
        $categories, $products);
?>
<script type="text/javascript">

	addProduct = function(productId) {
		var product = catalog.products[productId];
		initProduct(productId, product['vatSell']);
	}

	deleteLine = function(productId) {
		jQuery("#line-" + productId).detach();
	}

    initProduct = function(productId, price) {
    	var product = catalog.products[productId];
		if (jQuery("#line-" + productId).length > 0) {
			// Already there
			return;
		} else {
			// Add line
			var src = "?p=img&w=product";
			if (product['hasImage']) {
			    src += "&id=" + product['id'];
			}
			var html = "<tr id=\"line-" + product['id'] + "\">\n";
			html += "\t<td><img class=\"thumbnail\" src=\"" + src + "\"></td>\n";
			html += "\t<td>" + product['reference'] + "</td>\n";
			html += "\t<td>" + product['label'] + "</td>\n";
			html += "<td>" + product['vatSell'] + "</td>\n";
			html += "<td class=\"price-cell\"><input class=\"price\" id=\"line-" + product['id'] + "\" type=\"numeric\" name=\"price-" + product['id'] + "\" value=\"" + price + "\" />\n";
			html += "<td><a class=\"btn-delete\" href=\"\" onClick=\"javascript:deleteLine('" + product['id'] + "');return false;\"><?php \pi18n("Delete"); ?></a></td>\n";
			html += "</tr>\n";
			jQuery("tbody").append(html);
		}
    }

    jQuery(document).ready(function() {
<?php if ($area !== null) foreach ($area->getPrices() as $price) {
    $product = \Pasteque\ProductsService::get($price->productId);
    $taxCat = \Pasteque\TaxesService::get($product->taxCatId);
    $tax = $taxCat->getCurrentTax();
    $vatPrice = $price->price * (1 + $tax->rate);
    echo "\t\tinitProduct(\"" . $price->productId . "\", " . $vatPrice . ");\n";
} ?>
    });
</script>
