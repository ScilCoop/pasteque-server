<?php
//    Pastèque Web back office, Resources module
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

namespace BaseResources;

$message = null;
$error = null;
$resSrv = new \Pasteque\ResourcesService();
if (isset($_POST['id'])) {
	if ($_FILES['file']['tmp_name'] !== "") {
		$content = file_get_contents($_FILES['file']['tmp_name']);
	} else if ($_POST['type'] == \Pasteque\Resource::TYPE_TEXT) {
		$content = $_POST['text'];
	}
	$res = \Pasteque\Resource::__build($_POST['id'], $_POST['label'],
			$_POST['type'], $content);
	if ($resSrv->update($res)) {
		$message = \i18n("Changes saved");
	} else {
		$error = \i18n("Unable to save changes");
	}
} else if (isset($_POST['label'])) {
	if ($_FILES['file']['tmp_name'] !== "") {
		$content = file_get_contents($_FILES['file']['tmp_name']);
	} else if ($_POST['type'] == \Pasteque\Resource::TYPE_TEXT) {
		$content = $_POST['text'];
	}
	$res = new \Pasteque\Resource($_POST['label'],
			$_POST['type'], $content);;
	if ($resSrv->create($res) !== false) {
		$message = \i18n("Changes saved");
	} else {
		$error = \i18n("Unable to save changes");
	}
}

$resource = null;
$txtContent = "";
$imgContent = "";
if (isset($_GET['id'])) {
	$resource = $resSrv->get($_GET['id']);
	switch ($resource->type) {
		case \Pasteque\Resource::TYPE_TEXT:
			$txtContent = $resource->content;
			break;
		case \Pasteque\Resource::TYPE_IMAGE:
			$imgContent = \Pasteque\PT::URL_ACTION_PARAM . "=img&w=resource&id=" . $resource->id;
			break;
	}
}

//Title
echo \Pasteque\mainTitle(\i18n("Resources", PLUGIN_NAME));
//Information
\Pasteque\tpl_msg_box($message, $error);

$content = \Pasteque\form_hidden("edit", $resource, "id");
$content .= \Pasteque\form_input("edit", "Resource", $resource, "label", "string", array("required" => true));
$displayData = \Pasteque\form_select("type-selector",\i18n("Type"),[\Pasteque\Resource::TYPE_TEXT,\Pasteque\Resource::TYPE_IMAGE],[\i18n("Text", PLUGIN_NAME),\i18n("Image", PLUGIN_NAME)]);
$displayData .= \Pasteque\jsAddButton(\i18n("OK"),"javascript:selected();return false;");
$content .= \Pasteque\vanillaDiv($displayData,"selector");
$displayData = \Pasteque\form_textarea("text",\Pasteque\esc_attr($txtContent));
$displayData .= "<div class=\"col-sm-12\"><img id=\"preview\" name=\"image\" src=\"?" . \Pasteque\esc_attr($imgContent) . "\" /></div>\n";
$displayData .= \Pasteque\form_file("file");
$displayData .= \Pasteque\form_save();
$content .= \Pasteque\vanillaDiv($displayData,"editor");
echo \Pasteque\form_generate(\Pasteque\get_current_url(),"post",$content);
?>
<script type="text/javascript">
	typed = function(type) {
		jQuery("form").append('<input type="hidden" name="type" value="' + type + '" />');
		switch (parseInt(type)) {
		case <?php echo \Pasteque\Resource::TYPE_TEXT; ?>:
			jQuery("#text").show();
			jQuery("#preview").hide();
			break;
		case <?php echo \Pasteque\Resource::TYPE_IMAGE; ?>:
			jQuery("#text").hide();
			jQuery("#preview").show();
			break;
		}
		jQuery("#editor").show();
	}

<?php if ($resource === NULL) { ?>
	jQuery("#editor").hide();
<?php } else { ?>
	jQuery("#selector").hide();
	typed(<?php echo $resource->type; ?>);
<?php } ?>
	selected = function() {
		jQuery("#selector").hide();
		typed(jQuery("#type-selector").val());
	}
</script>
