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

namespace BaseProducts;

$message = NULL;
$error = NULL;
if (isset($_POST['id']) && isset($_POST['label'])) {
	if ($_FILES['image']['tmp_name'] !== "") {
		$output = $_FILES['image']['tmp_name'] . "thumb";
		\Pasteque\img_thumbnail($_FILES['image']['tmp_name'], $output);
		$img = file_get_contents($output);
	} else if ($_POST['clearImage']) {
		$img = NULL;
	} else {
		$img = "";
	}
	$parent_id = NULL;
	if ($_POST['parentId'] !== "") {
		$parent_id = $_POST['parentId'];
	}
	$dispOrder = 0;
	if ($_POST['dispOrder'] !== "") {
		$dispOrder = intval($_POST['dispOrder']);
	}
	$cat = \Pasteque\Category::__build($_POST['id'], $_POST['reference'],
			$parent_id, $_POST['label'], $img !== null, $dispOrder);
	if (\Pasteque\CategoriesService::updateCat($cat, $img)) {
		$message = \i18n("Changes saved");
	} else {
		$error = \i18n("Unable to save changes");
	}
} else if (isset($_POST['label'])) {
	if ($_FILES['image']['tmp_name'] !== "") {
		$img = file_get_contents($_FILES['image']['tmp_name']);
	} else {
		$img = NULL;
	}
	$parent_id = NULL;
	if ($_POST['parentId'] !== "") {
		$parent_id = $_POST['parentId'];
	}
	$dispOrder = 0;
	if ($_POST['dispOrder'] !== "") {
		$dispOrder = intval($_POST['dispOrder']);
	}
	$cat = new \Pasteque\Category($_POST['reference'], $parent_id, $_POST['label'], $img, $dispOrder);
	$id = \Pasteque\CategoriesService::createCat($cat, $img);
	if ($id !== FALSE) {
		$message = \i18n("Category saved. <a href=\"%s\">Go to the category page</a>.", PLUGIN_NAME, \Pasteque\get_module_url_action(PLUGIN_NAME, 'category_edit', array('id' => $id)));
	} else {
		$error = \i18n("Unable to save changes");
	}
}

$category = NULL;
if (isset($_GET['id'])) {
	$category = \Pasteque\CategoriesService::get($_GET['id']);
}

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Edit a category", PLUGIN_NAME)));
//Informations
\Pasteque\tpl_msg_box($message, $error);


$content = \Pasteque\form_hidden("edit", $category, "id");
$content .= \Pasteque\form_input("edit", "category", $category, "label", "string", array("required" => true));
$content .= \Pasteque\form_input("edit", "Category", $category, "reference", "string", array("required" => false));
$content .= \Pasteque\form_input("edit", "Category", $category, "parentId", "pick", array("model" => "Category", "nullable" => TRUE));
$content .= \Pasteque\form_input("edit", "category", $category, "dispOrder", "numeric");

$content .= \Pasteque\row(\Pasteque\form_file("image","image",\i18n("Image", PLUGIN_NAME)));
$content .= \Pasteque\form_value_hidden("clearImage", "clearImage", "0");
if ($category !== null && $category->hasImage) {
	$content .= \Pasteque\row("<img id=\"img\" class=\"image-preview\" src=\"?" . \Pasteque\PT::URL_ACTION_PARAM . "=img&w=category&id=" . $category->id . "\">");
	$buttons .= \Pasteque\jsDeleteButton(\i18n("Delete"), "javascript:clearImage();");
	$buttons .= \Pasteque\jsAddButton(\i18n("Restore"), "javascript:restoreImage();");
	$content .= \Pasteque\buttonGroup($buttons);
}
$fieldset_fields .= \Pasteque\form_input("edit", "category", $category, "visible", "boolean");
$content .= \Pasteque\form_fieldset($fieldset_legend,$fieldset_fields);
$content .= \Pasteque\form_save();
echo \Pasteque\row(\Pasteque\form_generate(\Pasteque\get_current_url(), "post", $content));
?>

<script type="text/javascript">
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
</script>
