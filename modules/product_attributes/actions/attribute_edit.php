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
	$attr = \Pasteque\Attribute::__build($_POST['id'], $_POST['label'], null);
	\Pasteque\AttributesService::updateAttribute($attr);
	// edit values
	$taxValues = array();
	foreach ($_POST as $key => $value) {
		if (strpos($key, "value-") === 0 && $key != "value-new") {
			$id = substr($key, 6);
			$val = \Pasteque\AttributeValue::__build($id, $value);
			if ($value == "") {
				\Pasteque\AttributesService::deleteValue($val->id);
			}
			else {
				\Pasteque\AttributesService::updateValue($val);
			}
		}
	}
	if (isset($_POST['delete'])) {
		foreach ($_POST['delete'] as $del) {
			\Pasteque\AttributesService::deleteValue($del);
		}
	}
	// new values?
	foreach ($_POST['value-new'] as $newVal) {
		if ($newVal !== null && $newVal !== "") {
			$newValObj = new \Pasteque\AttributeValue($newVal);
				echo "create: " . $newVal.  "<br>";
			\Pasteque\AttributesService::createValue($newValObj, $_POST['id']);
		}
	}
} else if (isset($_POST['value'])) {
	// Create attribute
	$attr = new \Pasteque\Attribute($_POST['value'], null);
	\Pasteque\AttributesService::createAttribute($attr);
	foreach ($_POST['value-new'] as $newVal) {
		if ($newVal !== null && $newVal !== "") {
			$newValObj = new \Pasteque\AttributeValue($newVal);
			\Pasteque\AttributesService::createValue($newValObj, $attr->id);
		}
	}
}

$attribute = null;
if (isset($_GET['id'])) {
	$attribute = \Pasteque\AttributesService::getAttribute($_GET['id']);
}

//Title
echo \Pasteque\mainTitle(\i18n("Attribute",PLUGIN_NAME));
//Informations
\Pasteque\tpl_msg_box($message,$error);

$content = \Pasteque\form_hidden("edit", $attribute, "id");
$content .= \Pasteque\form_fieldset(\i18n("Attribute", PLUGIN_NAME), \Pasteque\form_input("edit", "Attribute", $attribute, "label", "string", array("required" => true)));
$table[0][0] = \i18n("AttributeValue.value");
$i = 1;
if ($attribute !== null) {
	foreach ($attribute->values as $value) {
		$table[$i][0] = \Pasteque\form_input($value->id, "AttributeValue", $value, "value", "string", array("required" => false, "nolabel" => true, "nameid" => true));
		$i++;
	}
}
$table[$i][0] = \Pasteque\form_input("new", "AttributeValue", null, "value", "string", array("nolabel" => true, "nameid" => true, "array" => true));
$content .= \Pasteque\standardTable($table);
$content .= \Pasteque\form_save();
echo \Pasteque\form_generate(\Pasteque\get_current_url(), "post", $content);
?>
<script type="text/javascript">
del = function(id) {
	jQuery("#line-" + id).remove();
	jQuery("form.edit").append("<input type=\"hidden\" name=\"delete[]\" value=\"" + id + "\" />");
}
</script>
