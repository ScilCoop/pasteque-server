<?php
//    Pastèque Web back office, Products module
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

// category_edit action

namespace BaseCashRegisters;

$message = null;
$error = null;
$srv = new \Pasteque\CashRegistersService();
if (isset($_POST['id']) && isset($_POST['label'])) {
	// Update cash register
	$cashReg = \Pasteque\CashRegister::__build($_POST['id'], $_POST['label'],
			$_POST['locationId']);
	if ($srv->update($cashReg)) {
		$message = \i18n("Changes saved");
	} else {
		$error = \i18n("Unable to save changes");
	}
} else if (isset($_POST['label'])) {
	// New cash register
	$cashReg = new \Pasteque\CashRegister($_POST['label'],
			$_POST['locationId']);
	$id = $srv->create($cashReg);
	if ($id !== false) {
		$message = \i18n("Cash register saved. <a href=\"%s\">Go to the cash register page</a>.", PLUGIN_NAME, \Pasteque\get_module_url_action(PLUGIN_NAME, 'cashregister_edit', array('id' => $id)));
	} else {
		$error = \i18n("Unable to save changes");
	}
}

$cashReg = null;
if (isset($_GET['id'])) {
	$cashReg = $srv->get($_GET['id']);
}

//Title
echo \Pasteque\mainTitle(\i18n("Edit a cash register", PLUGIN_NAME));
//Information
\Pasteque\tpl_msg_box($message, $error);

$content = \Pasteque\form_hidden("edit", $cashReg, "id");
$content .= \Pasteque\form_input("edit", "CashRegister", $cashReg, "label", "string", array("required" => true));
$content .= \Pasteque\form_input("edit", "CashRegister", $cashReg, "locationId", "pick", array("model" => "Location"));
$content .= \Pasteque\form_save();
echo \Pasteque\form_generate(\Pasteque\get_current_url(),"post",$content);
?>
<form class="edit" action="<?php echo \Pasteque\get_current_url(); ?>" method="post" enctype="multipart/form-data">
    <?php \Pasteque\form_hidden("edit", $cashReg, "id"); ?>
	<?php \Pasteque\form_input("edit", "CashRegister", $cashReg, "label", "string", array("required" => true)); ?>
	<?php \Pasteque\form_input("edit", "CashRegister", $cashReg, "locationId", "pick", array("model" => "Location")); ?>

	<div class="row actions">
		<?php \Pasteque\form_save(); ?>
	</div>
</form>
