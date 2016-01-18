<?php
//    Pastèque Web back office, Product barcodes module
//
//    Copyright (C) 2015-2016 Scil (http://scil.coop)
//        Cédric Houbart, Philippe Pary
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

namespace ProductBarcodes;

$message = NULL;
$error = NULL;

$categories = \Pasteque\CategoriesService::getAll();
$allProducts = \Pasteque\ProductsService::getAll();
$products = array();
foreach ($allProducts as $product) {
	if ($product->barcode !== NULL && $product->barcode != "") {
		$products[] = $product;
	}
}

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Tags", PLUGIN_NAME)));
//Information
\Pasteque\tpl_msg_box($message, $error);

$content = \Pasteque\row(\Pasteque\form_number("start_from", "1", \i18n("Start from", PLUGIN_NAME), "1", "1", null, null));

$dir = opendir("modules/product_barcodes/print/templates/");
while($f = readdir($dir)) {
	if($f != "." && $f != ".." && $f != "index.php") {
		$values[] = substr($f,0,strpos($f,".php"));
	}
}
$content .= \Pasteque\row(\Pasteque\form_select("format", \i18n("Format", PLUGIN_NAME), $values, $values));

$content .= \Pasteque\row(\Pasteque\vanillaDiv("","catalog-picker"));
$table[0][0] = "";
$table[0][1] = \i18n("Product.reference");
$table[0][2] = \i18n("Product.label");
$table[0][3] = \i18n("Quantity");
$table[0][4] = "";
$content .= \Pasteque\row(\Pasteque\standardTable($table));

$content .= \Pasteque\row(\Pasteque\form_send());

echo \Pasteque\row(\Pasteque\form_generate("?" . \Pasteque\PT::URL_ACTION_PARAM . "=print&w=pdf&m=" . PLUGIN_NAME . "&n=tags","post",$content));
\Pasteque\init_catalog("catalog", "catalog-picker", "addProduct",
        $categories, $products);
?>
<script type="text/javascript">
	addProduct = function(productId) {
		var product = catalog.products[productId];
		if (jQuery("#line-" + productId).length > 0) {
			// Add quantity to existing line
			var qty = jQuery("#line-" + productId + "-qty");
			var currVal = qty.val();
			qty.val(parseInt(currVal) + 1);
		} else {
			// Add line
			var src;
			if (product['hasImage']) {
				src = "?p=img&w=product&id=" + product['id'];
			} else {
				src = "?p=img&w=product";
			}
			var html = "<tr id=\"line-" + product['id'] + "\">\n";
			html += "<td><img class=\"thumbnail\" src=\"" + src + "\" /></td>\n";
			html += "<td>" + product['reference'] + "</td>\n";
			html += "<td>" + product['label'] + "</td>\n";
			html += "<td class=\"qty-cell\"><input class=\"qty\" id=\"line-" + product['id'] + "-qty\" type=\"number\" name=\"qty-" + product['id'] + "\" value=\"1\" />\n";
			html += "\t<td><?php echo sprintf(\Pasteque\esc_js(\Pasteque\buttonGroup(\Pasteque\jsDeleteButton(\i18n("Delete"),"%s"))),"javascript:deleteLine('\" + product['id'] + \"');return false;"); ?></td>\n";
			html += "</tr>\n";
			jQuery("tbody").append(html);
		}
	}
	deleteLine = function(productId) {
		jQuery("#line-" + productId).detach();
	}
</script>
