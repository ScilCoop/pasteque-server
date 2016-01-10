<?php
//    Pastèque Web back office, Products module
//
//    Copyright (C) 2013 Scil (http://scil.coop)
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

$message = null;
$error = null;
if (isset($_GET['delete-product'])) {
	if (\Pasteque\ProductsService::delete($_GET['delete-product'])) {
		$message = \i18n("Changes saved") ;
	}
	else {
		$message = "Le produit a été placé en archive (car déjà vendu ou en stock)";
	}
}

if(!isset($_GET["start"])) {
	$start = 0;
}
else {
	$start = $_GET["start"];
}
if(!isset($_GET["range"])) {
	$range = 50;
}
else {
	$range = $_GET["range"];
}
if(!isset($_GET["hidden"])) {
	$hidden = true;
}
else {
	$hidden = $_GET["hidden"];
}

if($range == "all") {
	$products = \Pasteque\ProductsService::getAll($hidden);
	$totalProducts = \Pasteque\ProductsService::getTotal($hidden);
}
else if(isset($_GET["category"])) {
	$products = \Pasteque\ProductsService::getByCategory($_GET["category"]);
	$totalProducts = \Pasteque\ProductsService::getTotalByCategory($_GET["category"],$hidden);
}
else {
	$products = \Pasteque\ProductsService::getRange($range,$start,$hidden);
	$totalProducts = \Pasteque\ProductsService::getTotal($hidden);
}
$categories = \Pasteque\CategoriesService::getAll();
$prdCat = array();
$archivesCat = array();
foreach ($products as $product) {
	if ($product->categoryId !== \Pasteque\CompositionsService::CAT_ID) {
		$prdCat[$product->categoryId][] = $product;
	}
	// Archive will be filled on display loop
}

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Products", PLUGIN_NAME)));
//Buttons
$buttons = \Pasteque\addButton(\i18n("Add a product", PLUGIN_NAME),\Pasteque\get_module_url_action(PLUGIN_NAME, "product_edit"));
$buttons .= \Pasteque\importButton(\i18n("Import products", PLUGIN_NAME),\Pasteque\get_module_url_action(PLUGIN_NAME, "productsManagement"));
$buttons .= \Pasteque\exportButton(\i18n("Export products", PLUGIN_NAME),\Pasteque\get_module_url_action(PLUGIN_NAME,"products_export"));
echo \Pasteque\row(\Pasteque\buttonGroup($buttons));
//Information
\Pasteque\tpl_msg_box($message, $error); ?>

<div id="search">
<div class="title"><?php \pi18n("Search"); ?></div>
<h5><? \pi18n("by category", PLUGIN_NAME); ?></h5>
<?php
    \Pasteque\tpl_form('select', 'category', \Pasteque\CategoriesService::getAll());
?>
</div>

<?php
//Counter
echo \Pasteque\row(\Pasteque\counterDiv(\i18n("%d products", PLUGIN_NAME, $totalProducts)));
?></p>

<?php \Pasteque\tpl_pagination($totalProducts,$range,$start); ?>

<?php
$archive = false;
foreach ($categories as $category) {
	if (isset($prdCat[$category->id])) {
		$content[0][0] = "";
		$content[0][1] = \i18n("Product.reference");
		$content[0][2] = \i18n("Product.label");
		$i = 1;
		foreach ($prdCat[$category->id] as $product) {
			if ($product->visible) {
				if ($product->hasImage) {
					$imgSrc = \Pasteque\PT::URL_ACTION_PARAM . "=img&w=product&id=" . $product->id;
				} else {
					$imgSrc = \Pasteque\PT::URL_ACTION_PARAM . "=img&w=product";
				}
				$content[$i][0] = "<img class=\"img img-thumbnail thumbnail\" src=\"?" . $imgSrc . "\">";
				$content[$i][1] = $product->reference;
				$content[$i][2] = $product->label;
				$btn_group = \Pasteque\editButton(\i18n('Edit', PLUGIN_NAME), \Pasteque\get_module_url_action(PLUGIN_NAME, 'product_edit', array("id" => $product->id)));
				$btn_group .= \Pasteque\deleteButton(\i18n('Delete', PLUGIN_NAME), \Pasteque\get_current_url() . "&delete-product=" . $product->id);
				$content[$i][2] .= \Pasteque\buttonGroup($btn_group, "pull-right");
				$i++;
			}
			else {
				$archive = true;
				$archivesCat[$category->id][] = $product;
			}
		}
		if(sizeof($content) > 1) {
			echo \Pasteque\row(\Pasteque\secondaryTitle(\Pasteque\esc_html($category->label)));
			echo \Pasteque\row(\Pasteque\standardTable($content));
		}
		unset($content);
	}
}

if ($archive) {
	foreach ($categories as $category) {
		$content[0][0] = "";
		$content[0][1] = \i18n("Product.reference");
		$content[0][2] = \i18n("Product.label");
		$i = 1;
		if (isset($archivesCat[$category->id])) {
			foreach ($archivesCat[$category->id] as $product) {
				if (!$product->visible) {
					if ($product->hasImage) {
						$imgSrc = \Pasteque\PT::URL_ACTION_PARAM . "=img&w=product&id=" . $product->id;
					} else {
						$imgSrc = \Pasteque\PT::URL_ACTION_PARAM . "=img&w=product";
					}
					$content[$i][0] = "<img class=\"img img-thumbnail thumbnail\" src=\"?" . $imgSrc . "\">";
					$content[$i][1] = $product->reference;
					$content[$i][2] = $product->label;
					$btn_group = \Pasteque\editButton(\i18n('Edit', PLUGIN_NAME), \Pasteque\get_module_url_action(PLUGIN_NAME, 'product_edit', array("id" => $product->id)));
					$content[$i][2] .= \Pasteque\buttonGroup($btn_group, "pull-right");
					$i++;
				}
			}
		}
		if(sizeof($content) > 1) {
			echo \Pasteque\row(\Pasteque\secondaryTitle(\Pasteque\esc_html($category->label) . "&nbsp;-&nbsp;" . \i18n("Archived", PLUGIN_NAME)));
			echo \Pasteque\row(\Pasteque\standardTable($content));
		}
		unset($content);
	}
}

if (count($products) == 0) {
	echo \Pasteque\errorDiv(\i18n("No product found", PLUGIN_NAME));
}
?>
