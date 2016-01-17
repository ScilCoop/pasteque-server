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

$dateStr = isset($_POST['date']) ? $_POST['date'] : \i18nDate(time());
$time = \i18nRevDate($dateStr);
if (isset($_POST['reason']) && !isset($_POST['sendCsv'])) {
	$reason = $_POST['reason'];
	if ($multilocations) {
		$locationId = $_POST['location'];
	} else {
		$locationId = $defaultLocationId;
	}
	foreach ($_POST as $key => $value) {
		if (strpos($key, "qty-") === 0) {
			$productId = substr($key, 4);
			$product = \Pasteque\ProductsService::get($productId);
			switch ($reason) {
				case \Pasteque\StockMove::REASON_OUT_SELL:
				case \Pasteque\StockMove::REASON_IN_REFUND:
					$price = $product->priceSell;
					break;
				case \Pasteque\StockMove::REASON_IN_BUY:
				case \Pasteque\StockMove::REASON_OUT_BACK:
				case \Pasteque\StockMove::REASON_IN_MOVEMENT:
				case \Pasteque\StockMove::REASON_OUT_REFUND:
				case \Pasteque\StockMove::REASON_OUT_MOVEMENT:
				case \Pasteque\StockMove::REASON_RESET:
					if ($product->priceBuy !== null) {
						$price = $product->priceBuy;
					} else {
						$price = 0.0;
					}
					break;
				case \Pasteque\StockMove::REASON_TRANSFERT:
					$price = 0.0;
					break;
			}
			$qty = $value;
			if ($reason == \Pasteque\StockMove::REASON_TRANSFERT) {
				$destId = $_POST['destination'];
				$move = new \Pasteque\StockMove($time,
						\Pasteque\StockMove::REASON_OUT_MOVEMENT, $productId,
						$locationId, null, $qty, $price);
				$move2 = new \Pasteque\StockMove($time,
						\Pasteque\StockMove::REASON_IN_MOVEMENT, $productId,
						$destId, null, $qty, $price);
				if (\Pasteque\StocksService::addMove($move)
						&& \Pasteque\StocksService::addMove($move2)) {
					$message = \i18n("Changes saved");
				} else {
					$error = \i18n("Unable to save changes");
				}
			} else if ($reason == \Pasteque\StockMove::REASON_RESET) {
				$level = \Pasteque\StocksService::getLevel($productId,
						$locationId, null);
				$move = new \Pasteque\StockMove($time, $reason, $productId,
						$locationId, null, -$level->qty, $price);
				$move2 = new \Pasteque\StockMove($time, $reason, $productId,
						$locationId, null, $qty, $price);
				if (\Pasteque\StocksService::addMove($move)
						&& \Pasteque\StocksService::addMove($move2)) {
					$message = \i18n("Changes saved");
				} else {
					$error = \i18n("Unable to save changes");
				}
			} else {
				$move = new \Pasteque\StockMove($time, $reason, $productId,
						$locationId, null, $qty, $price);
				if (\Pasteque\StocksService::addMove($move)) {
					$message = \i18n("Changes saved");
				} else {
					$error = \i18n("Unable to save changes");
				}
			}
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
				$error[] = \i18n("Unable to find product %s", PLUGIN_NAME, $tab['Reference']);
			}
			if ($tab['Quantity'] === "0" || intval($tab['Quantity']) !== 0) {
				$quantityOk = true;
			} else {
				if ($error === null) {
					$error = array();
				}
				$error[] = \i18n("Undefined quantity for product %s", PLUGIN_NAME, $tab['Reference']);
			}
			if ($productOk && $quantityOk) {
				echo "setProduct(\"" . \Pasteque\esc_js($product->id) . "\", \""
					. \Pasteque\esc_js($product->reference) . "\", "
					. ($product->hasImage ? "1" : "0") . ", \""
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
$products = \Pasteque\ProductsService::getAll(FALSE);

$locNames = array();
$locIds = array();
foreach ($locations as $location) {
	$locNames[] = $location->label;
	$locIds[] = $location->id;
}
$reasonIds = array(\Pasteque\StockMove::REASON_IN_BUY,
		\Pasteque\StockMove::REASON_OUT_SELL,
		\Pasteque\StockMove::REASON_OUT_BACK,
		\Pasteque\StockMove::REASON_TRANSFERT,
		\Pasteque\StockMove::REASON_RESET);
$reasonNames = array(\i18n("Buy", PLUGIN_NAME),
		\i18n("Sell", PLUGIN_NAME),
		\i18n("Return to supplier", PLUGIN_NAME),
		\i18n("Transfert", PLUGIN_NAME),
		\i18n("Reset", PLUGIN_NAME));
if (!$multilocations) {
	array_splice($reasonIds, 3, 1);
	array_splice($reasonNames, 3, 1);
}

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Stock move", PLUGIN_NAME)));
//Informations
\Pasteque\tpl_msg_box($message, $error);

$content = \Pasteque\row(\Pasteque\form_select("reason", \i18n("Operation", PLUGIN_NAME), $reasonIds, $reasonNames, null));
if ($multilocations) {
	$content .= \Pasteque\row(\Pasteque\form_select("location", \i18n("Location"), $locIds, $locNames, null));
	$content .= \Pasteque\row(\Pasteque\form_select("destination", \i18n("Destination"), $locIds, $locNames, null));
}
$content .= \Pasteque\row(\Pasteque\form_date("date",$dateStr,\i18n("Date", PLUGIN_NAME)));
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
        $categories, $products); ?>
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
			html += "\t<td><img class=\"thumbnail\" src=\"" + src + "\"></td>\n";
			html += "\t<td>" + product['reference'] + "</td>\n";
			html += "\t<td>" + product['label'] + "</td>\n";
			html += "\t<td class=\"qty-cell\"><input class=\"qty\" id=\"line-" + product['id'] + "-qty\" type=\"numeric\" name=\"qty-" + product['id'] + "\" value=\"1\"></td>\n";
			html += "\t<td><?php echo sprintf(\Pasteque\esc_js(\Pasteque\buttonGroup(\Pasteque\jsDeleteButton(\i18n("Delete"),"%s"))),"javascript:deleteLine('\" + product['id'] + \"');return false;"); ?></td>\n";
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
		jQuery("tobody").append(html);
    }

	deleteLine = function(productId) {
		if(confirm("<?php \pi18n('confirm'); ?>")) {
			jQuery("#line-" + productId).detach();
		}
	}

<?php if ($multilocations) { ?>
    reasonChange = function() {
        var reason = jQuery("#reason").val();
        if (reason == <?php echo \Pasteque\StockMove::REASON_TRANSFERT; ?>) {
            jQuery("#destination").prop("disabled", false);
        } else {
            jQuery("#destination").prop("disabled", true);
        }
    }
    jQuery("#reason").change(function() { reasonChange(); });
    reasonChange();
<?php } ?>
</script>
