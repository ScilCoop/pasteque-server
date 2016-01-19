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

namespace BaseStocks;

$modules = \Pasteque\get_loaded_modules(\Pasteque\get_user_id());
$multilocations = false;
$defaultLocationId = null;
if (in_array("stock_multilocations", $modules)) {
	$multilocations = true;
}

$locSrv = new \Pasteque\LocationsService();
$locations = $locSrv->getAll();
$locNames = array();
$locIds = array();
foreach ($locations as $location) {
	$locNames[] = $location->label;
	$locIds[] = $location->id;
}
$currLocation = null;
if (isset($_POST['location'])) {
	$currLocation = $_POST['location'];
} else {
	$currLocation = $locations[0]->id;
}
$products = \Pasteque\ProductsService::getAll();
$categories = \Pasteque\CategoriesService::getAll();
$prdCat = array();
// Link products to categories and don't track compositions
foreach ($products as $product) {
	if ($product->categoryId !== \Pasteque\CompositionsService::CAT_ID) {
		$prdCat[$product->categoryId][] = $product;
	}
}
$levels = \Pasteque\StocksService::getLevels($currLocation);
$prdLevel = array();
foreach ($levels as $level) {
	$prdLevel[$level->productId] = $level;
}

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Inventory", PLUGIN_NAME)));
//Buttons
$buttons = \Pasteque\exportButton(\i18n("Export inventory", PLUGIN_NAME), \Pasteque\get_report_url(PLUGIN_NAME, "inventory"));
echo \Pasteque\row(\Pasteque\buttonGroup($buttons));
//Information
\Pasteque\tpl_msg_box($message, $error);

if ($multilocations) {
	// Location picker
	$content = \Pasteque\row(\Pasteque\form_select("location", \i18n("Location"), $locIds, $locNames, $currLocation));
	$content .= \Pasteque\row(\Pasteque\form_send());
	echo \Pasteque\row(\Pasteque\form_generate(\Pasteque\get_current_url(), "post", $content));
}

unset($content);
foreach ($categories as $category) {
	if (isset($prdCat[$category->id])) {
		// Category header
		echo \Pasteque\row(\Pasteque\secondaryTitle($category->label));
		$content[0][0] = "";
		$content[0][1] = \i18n("Product.reference");
		$content[0][2] = \i18n("Product.label");
		$content[0][3] = \i18n("Quantity");
		$content[0][4] = \i18n("Stock.SellValue");
		$content[0][5] = \i18n("Stock.BuyValue");
		$content[0][6] = \i18n("QuantityMin");
		$content[0][7] = \i18n("QuantityMax");
		$i = 1;
		foreach ($prdCat[$category->id] as $product) {
			if (!isset($prdLevel[$product->id])) {
				continue;
			}
			// Level lines
			$prdRef = "";
			$prdLabel = "";
			$imgSrc = "";
			$prdSellPrice = 0;
			$prdBuyPrice = 0;
			$level = $prdLevel[$product->id];
			if ($product->hasImage) {
				$imgSrc = \Pasteque\PT::URL_ACTION_PARAM . "=img&w=product&id=" . $product->id;
			} else {
				$imgSrc = \Pasteque\PT::URL_ACTION_PARAM . "=img&w=product";
			}
			$prdLabel = $product->label;
			$prdRef = $product->reference;
			$prdSellPrice = $product->priceSell !== null ? $product->priceSell : 0;
			$prdBuyPrice = $product->priceBuy !== null ? $product->priceBuy : 0;
			$security = $level->security;
			$max = $level->max;
			$qty = $level->qty !== null ? $level->qty : 0;
			$class = "";
			$help = "";
			if ($security !== null && $qty < $security) {
				$class=" warn-level";
				$help = ' title="' . \Pasteque\esc_attr(\i18n("Stock is below security level!", PLUGIN_NAME)) . '"';
			}
			if ($qty < 0) {
				$class=" alert-level";
				$help = ' title="' . \Pasteque\esc_attr(\i18n("Stock is negative!", PLUGIN_NAME)) . '"';
			} else if ($max !== NULL && $qty > $max) {
				$class=" alert-level";
				$help = ' title="' . \Pasteque\esc_attr(\i18n("Overstock!", PLUGIN_NAME)) . '"';
			}
			if (!isset($security)) {
				$security = \i18n("Undefined");
			}
			if (!isset($max)) {
				$max = \i18n("Undefined");
			}
			$content[$i][0] = "<img class=\"thumbnail\" src=\"?" . \Pasteque\esc_attr($imgSrc) . "\">";
			$content[$i][1] = \Pasteque\esc_html($prdRef);
			$content[$i][2] = \Pasteque\esc_html($prdLabel);
			$content[$i][3] = \Pasteque\esc_html($qty);
			$content[$i][4] = \Pasteque\esc_html(\i18nCurr($prdSellPrice*$qty));
			$content[$i][5] = \Pasteque\esc_html(\i18nCurr($prdBuyPrice*$qty));
			$content[$i][6] = \Pasteque\esc_html($security);
			$content[$i][7] = \Pasteque\esc_html($max);
			$i++;
		}
		echo \Pasteque\row(\Pasteque\standardTable($content));
	}
}
