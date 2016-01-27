<?php
//    Pastèque Web back office, Products module
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

namespace ProductAttributes;

$message = null;
$error = null;
// Check saves
if (isset($_POST['id'])) {
	// Update attribute
	$set = \Pasteque\AttributeSet::__build($_POST['id'], $_POST['label']);
	foreach ($_POST['id-attr'] as $attrId) {
		if ($attrId !== null && $attrId !== "") {
			$attr = \Pasteque\Attribute::__build($attrId, "unused", null);
			$set->addAttribute($attr, null);
		}
	}
	\Pasteque\AttributesService::updateSet($set);
} else if (isset($_POST['label'])) {
	// Create attribute
	$set = new \Pasteque\AttributeSet($_POST['label']);
	foreach ($_POST['id-attr'] as $attrId) {
		if ($attrId !== null && $attrId !== "") {
			$attr = \Pasteque\Attribute::__build($attrId, "unused", null);
			$set->addAttribute($attr, null);
		}
	}
	\Pasteque\AttributesService::createSet($set);
}

$set = null;
if (isset($_GET['id'])) {
	$set = \Pasteque\AttributesService::get($_GET['id']);
}

//Title
echo \Pasteque\mainTitle(\i18n("Edit attribute set", PLUGIN_NAME));
//Information
\Pasteque\tpl_msg_box($message, $error);

$content = \Pasteque\form_hidden("edit", $set, "id");
$content .= \Pasteque\form_fieldset(\i18n("Attribute set", PLUGIN_NAME), \Pasteque\form_input("edit", "AttributeSet", $set, "label", "string", array("required" => true)));
$table[0][0] = \i18n("AttributeSet.label");
$i = 1;
if ($set !== null) {
	foreach ($set->attributes as $value) {
		$table[$i][0] = \Pasteque\form_input("attr", "Attribute", $value, "id", "pick", array("model" => "Attribute", "nullable" => true, "nolabel" => true, "array" => true, "nameid" => true));
		$i++;
	}
}
$table[$i][0] = \Pasteque\form_input("attr", "Attribute", null, "id", "pick", array("model" => "Attribute", "nullable" => true, "nolabel" => true, "array" => true, "nameid" => true));
$content .= \Pasteque\standardTable($table);
$content .= \Pasteque\form_save();

echo \Pasteque\form_generate(\Pasteque\get_current_url(), "post", $content);
?>
