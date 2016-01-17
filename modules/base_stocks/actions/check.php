<?php
//    Pastèque Web back office, Stocks module
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

namespace BaseStocks;

$message = null;
$error = null;
$modules = \Pasteque\get_loaded_modules(\Pasteque\get_user_id());
$multilocations = false;
$locSrv = new \Pasteque\LocationsService();
$locations = $locSrv->getAll();
$defaultLocationId = $locations[0]->id;
if (in_array("stock_multilocations", $modules)) {
	$multilocations = true;
}

$countedStock = null;
$locationId = null;
if (isset($_POST['send']) && !isset($_POST['sendCsv'])) {
	$countedStock = array();
	if (isset($_POST['location'])) {
		$locationId = $_POST['location'];
	} else {
		$locationId = $defaultLocationId;
	}
	foreach ($_POST as $key => $value) {
		if (strpos($key, "qty-") === 0) {
			$productId = substr($key, 4);
			$qty = $value;
			$countedStock[$productId] = $qty;
		}
	}
} else if (isset($_POST['sendCsv'])) {
	$key = array('Quantity', 'Reference');
	$csv = new \Pasteque\Csv($_FILES['csv']['tmp_name'], $key, array(),
			PLUGIN_NAME);
	if (!$csv->open()) {
		$error = $csv->getErrors();
	} else {
		//manage empty string
		$csv->setEmptyStringValue("Quantity", "0");
		echo "<script type=\"text/javascript\">\n";
		echo "jQuery(document).ready(function() {\n";
		while ($tab = $csv->readLine()) {
			$productOk = false;
			$quantityOk = false;
			$product = \Pasteque\ProductsService::getByRef($tab['Reference']);
			if ($product !== null) {
				$productOk = true;
			} else {
				if ($error === null) {
					$error = array();
				}
				$error[] = \i18n("Unable to find product %s", PLUGIN_NAME,
						$tab['Reference']);
				continue;
			}
			if ($tab['Quantity'] === "0" || intval($tab['Quantity']) !== 0) {
				$quantityOk = true;
			} else {
				if ($error === null) {
					$error = array();
				}
				$error[] = \i18n("Undefined quantity for product %s",
						PLUGIN_NAME, $tab['Reference']);
				continue;
			}
			if ($productOk && $quantityOk) {
				echo "setProduct(\"" . \Pasteque\esc_js($product->id) . "\", \""
					. \Pasteque\esc_js($product->reference) . "\", "
					. ($product->hasImage ? "1":"0") . ", \""
					. \Pasteque\esc_js($product->label) . "\", "
					. $tab['Quantity'] . ");\n";
			}
		}
		echo "});\n";
		echo "</script>\n\n";
		$csv->close();
	}
}

$categories = \Pasteque\CategoriesService::getAll();
$products = \Pasteque\ProductsService::getAll();

$locNames = array();
$locIds = array();
foreach ($locations as $location) {
	$locNames[] = $location->label;
	$locIds[] = $location->id;
}

$prdCat = array();
$levels = array();
if ($countedStock !== null) {
	// Build listing by categories
	foreach ($products as $product) {
		if ($product->categoryId !== \Pasteque\CompositionsService::CAT_ID) {
			$prdCat[$product->categoryId][] = $product;
		}
	}
	// Get stock to compare with counted stock
	$rawLevels = \Pasteque\StocksService::getLevels($locationId);
	foreach ($rawLevels as $level) {
		$levels[$level->productId] = $level;
	}
}

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Stock check", PLUGIN_NAME)));
//Buttons
$action = \Pasteque\get_report_url(PLUGIN_NAME, "check");
foreach($_POST as $key => $value) {
	$action .= "&" . $key . "=" . $value;
}
$buttons = \Pasteque\exportButton(\i18n("Export"),$action);
echo \Pasteque\row(\Pasteque\buttonGroup($buttons));
//Information
\Pasteque\tpl_msg_box($message, $error);

if ($multilocations) {
	$content .= \Pasteque\row(\Pasteque\form_select("location", \i18n("Location"), $locIds, $locNames, null));
	$content .= \Pasteque\row(\Pasteque\form_select("destination", \i18n("Destination"), $locIds, $locNames, null));
}
$content .= \Pasteque\row(\Pasteque\form_file("file","csv",\i18n("Load csv file", PLUGIN_NAME)));
$content .= \Pasteque\row(\Pasteque\form_button(\i18n("Load", PLUGIN_NAME)));

$content .= \Pasteque\row(\Pasteque\vanillaDiv("","catalog-picker"));

$table[0][0] = "";
$table[0][1] = \i18n("Product.reference");
$table[0][2] = \i18n("Product.label");
$table[0][3] = \i18n("Quantity");
$table[0][4] = "";
$content .= \Pasteque\row(\Pasteque\standardTable($table));

$content .= \Pasteque\row(\Pasteque\form_save());

echo \Pasteque\row(\Pasteque\form_generate(\Pasteque\get_current_url(),"post",$content));
\Pasteque\init_catalog("catalog", "catalog-picker", "addProduct",
        $categories, $products);

if ($countedStock !== null) {
	foreach ($categories as $category) {
		$printed = false;
		if (isset($prdCat[$category->id])) {
			foreach ($prdCat[$category->id] as $product) {
				$counted = 0;
				if (isset($countedStock[$product->id])) {
					$counted = $countedStock[$product->id];
				}
				$actual = 0;
				if (isset($levels[$product->id])) {
					$actual = $levels[$product->id]->qty;
				}
				if ($counted !== $actual) {
					if ($product->hasImage) {
						$imgSrc = \Pasteque\PT::URL_ACTION_PARAM . "=img&w=product&id=" . $product->id;
					} else {
						$imgSrc = \Pasteque\PT::URL_ACTION_PARAM . "=img&w=product";
					}
					if (!$printed) {
						$printed = true;
						?>
							<h3><?php echo \Pasteque\esc_html($category->label); ?></h3>
							<table cellpadding="0" cellspacing="0">
							<thead>
							<tr>
							<th></th>
							<th><?php \pi18n("Product.reference"); ?></th>
							<th><?php \pi18n("Product.label"); ?></th>
							<th><?php \pi18n("Counted stock", PLUGIN_NAME); ?></th>
							<th><?php \pi18n("Actual stock", PLUGIN_NAME); ?></th>
							<th><?php \pi18n("Difference", PLUGIN_NAME); ?></th>
							</tr>
							</thead>
							<tbody>
							<?php
					}
					?>
						<tr>
						<td><img class="thumbnail" src="?<?php echo $imgSrc ?>" />
						<td><?php echo $product->reference; ?></td>
						<td><?php echo $product->label; ?></td>
						<td><?php echo $counted ?></td>
						<td><?php echo $actual; ?></td>
						<td><?php echo $counted - $actual; ?></td>
						</tr>
						<?php

				}
			}
			?>
				</tbody>
				</table>
				<?php

		}
	}
} // end of stock comparison

?>
<script type="text/javascript">
	addProduct = function(productId) {
		var product = catalog.getProduct(productId);
		if (jQuery("#line-" + productId).length > 0) {
			// Add quantity to existing line
			var qty = jQuery("#line-" + productId + "-qty");
			var currVal = qty.val();
			qty.val(parseInt(currVal) + 1);
		} else {
			// Add line
			var src = "?p=img&w=product";
			if (product['hasImage']) {
			    src += "&id=" + product['id'];
			}
			var html = "<tr id=\"line-" + product['id'] + "\">\n";
			html += "<td><img class=\"thumbnail\" src=\"" + src + "\" /></td>\n";
			html += "<td>" + product['reference'] + "</td>\n";
			html += "<td>" + product['label'] + "</td>\n";
			html += "<td class=\"qty-cell\"><input class=\"qty\" id=\"line-" + product['id'] + "-qty\" type=\"numeric\" name=\"qty-" + product['id'] + "\" value=\"1\" />\n";
			html += "<td><a class=\"btn-delete\" href=\"\" onClick=\"javascript:deleteLine('" + product['id'] + "');return false;\"><?php \pi18n("Delete"); ?></a></td>\n";
			html += "</tr>\n"; 
			jQuery("tbody").append(html);
		}
	}

	/** Set a new line with given quantity. Use only at start. */
	setProduct = function(productId, productRef, hasImage, productLabel, qty) {
		var src = "?p=img&w=product";
		if (hasImage == 1) {
		    src += "&id=" + productId;
		}
		var html = "<tr id=\"line-" + productId + "\">\n";
		html += "<td><img class=\"thumbnail\" src=\"" + src + "\" /></td>\n";
		html += "<td>" + productRef + "</td>\n";
		html += "<td>" + productLabel + "</td>\n";
		html += "<td class=\"qty-cell\"><input class=\"qty\" id=\"line-" + productId + "-qty\" type=\"numeric\" name=\"qty-" + productId + "\" value=\"" + qty + "\" />\n";
		html += "<td><a class=\"btn-delete\" href=\"\" onClick=\"javascript:deleteLine('" + productId + "');return false;\"><?php \pi18n("Delete"); ?></a></td>\n";
		html += "</tr>\n";
		jQuery("tbody").append(html);
    }

	deleteLine = function(productId) {
		jQuery("#line-" + productId).detach();
	}
</script>
