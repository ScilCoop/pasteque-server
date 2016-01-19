<?php
//    Pastèque Web back office, Products module
//
//    Copyright (C) 2013-2016 Scil (http://scil.coop)
//          Philippe Pary
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

namespace ProductProviders;

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
	$dispOrder = 0;
	if ($_POST['dispOrder'] !== "") {
		$dispOrder = intval($_POST['dispOrder']);
	}
	$prov = \Pasteque\provider::__build($_POST['id'], $_POST['label'], $img !== null,
			$_POST['firstName'], $_POST['lastName'], $_POST['email'],
			$_POST['phone1'], $_POST['phone2'], $_POST['website'], $_POST['fax'],
			$_POST['addr1'],  $_POST['addr2'], $_POST['zipCode'], $_POST['city'],
			$_POST['region'], $_POST['country'], $_POST['notes'], $_POST['visible'],
			$dispOrder);
	if (\Pasteque\providersService::updateprov($prov, $img)) {
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
	$dispOrder = 0;
	if ($_POST['dispOrder'] !== "") {
		$dispOrder = intval($_POST['dispOrder']);
	}

	$prov = new \Pasteque\Provider($_POST['label'], $img !== null, 
			$_POST['firstName'], $_POST['lastName'], $_POST['email'],
			$_POST['phone1'], $_POST['phone2'], $_POST['website'], $_POST['fax'],
			$_POST['addr1'],  $_POST['addr2'], $_POST['zipCode'], $_POST['city'],
			$_POST['region'], $_POST['country'], $_POST['notes'], $_POST['visible'],
			$dispOrder);
	$id = \Pasteque\providersService::createprov($prov, $img);
	if ($id !== FALSE) {
		$message = \i18n("provider saved. <a href=\"%s\">Go to the provider page</a>.", PLUGIN_NAME, \Pasteque\get_module_url_action(PLUGIN_NAME, 'provider_edit', array('id' => $id)));
	} else {
		$error = \i18n("Unable to save changes");
	}
}

$provider = NULL;
if (isset($_GET['id'])) {
	$provider = \Pasteque\providersService::get($_GET['id']);
}

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Edit a provider", PLUGIN_NAME)));
//Informations
\Pasteque\tpl_msg_box($message, $error);

$content = \Pasteque\form_hidden("edit", $provider, "id");
$content .= \Pasteque\form_input("edit", "Provider", $provider, "label", "string", array("required" => true));
$content .= \Pasteque\form_input("edit", "Provider", $provider, "dispOrder", "numeric");

$content .= \Pasteque\row(\Pasteque\form_file("image","image",\i18n("Image", PLUGIN_NAME)));
$content .= \Pasteque\form_value_hidden("clearImage", "clearImage", "0");
if ($provider !== null && $provider->hasImage) {
	$content .= \Pasteque\row("<img id=\"img\" class=\"image-preview\" src=\"?" . \Pasteque\PT::URL_ACTION_PARAM . "=img&w=provider&id=" . $provider->id . "\">");
	$buttons .= \Pasteque\jsDeleteButton(\i18n("Delete"), "javascript:clearImage();");
	$buttons .= \Pasteque\jsAddButton(\i18n("Restore"), "javascript:restoreImage();");
	$content .= \Pasteque\buttonGroup($buttons);
}
$fieldset_legend = \i18n("Contact data", PLUGIN_NAME);
$fieldset_fields .= \Pasteque\form_input("edit", "Provider", $provider, "firstName", "string");
$fieldset_fields .= \Pasteque\form_input("edit", "Provider", $provider, "lastName", "string");
$fieldset_fields .= \Pasteque\form_input("edit", "Provider", $provider, "email", "string");
$fieldset_fields .= \Pasteque\form_input("edit", "Provider", $provider, "phone1", "string");
$fieldset_fields .= \Pasteque\form_input("edit", "Provider", $provider, "phone2", "string");
$fieldset_fields .= \Pasteque\form_input("edit", "Provider", $provider, "website", "string");
$fieldset_fields .= \Pasteque\form_input("edit", "Provider", $provider, "fax", "string");
$fieldset_fields .= \Pasteque\form_input("edit", "Provider", $provider, "addr1", "string");
$fieldset_fields .= \Pasteque\form_input("edit", "Provider", $provider, "addr2", "string");
$fieldset_fields .= \Pasteque\form_input("edit", "Provider", $provider, "zipCode", "string");
$fieldset_fields .= \Pasteque\form_input("edit", "Provider", $provider, "city", "string");
$fieldset_fields .= \Pasteque\form_input("edit", "Provider", $provider, "region", "string");
$fieldset_fields .= \Pasteque\form_input("edit", "Provider", $provider, "country", "string");
$fieldset_fields .= \Pasteque\form_input("edit", "Provider", $provider, "notes", "text");
$fieldset_fields .= \Pasteque\form_input("edit", "Provider", $provider, "visible", "boolean");
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
