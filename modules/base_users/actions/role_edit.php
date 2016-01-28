<?php
//    Pastèque Web back office, Users module
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

namespace BaseUsers;

$ALL_PERMS = array(
	"fr.pasteque.pos.sales.JPanelTicketSales",
	"fr.pasteque.pos.sales.JPanelTicketEdits",
	"fr.pasteque.pos.customers.CustomersPayment",
	"fr.pasteque.pos.panels.JPanelPayments",
	"fr.pasteque.pos.panels.JPanelCloseMoney",
	"sales.EditLines",
	"sales.EditTicket",
	"sales.RefundTicket",
	"sales.PrintTicket",
	"sales.Total",
	"sales.ChangeTaxOptions",
	"payment.cash",
	"payment.cheque",
	"payment.paper",
	"payment.magcard",
	"payment.free",
	"payment.debt",
	"payment.prepaid",
	"refund.cash",
	"refund.cheque",
	"refund.paper",
	"refund.magcard",
	"refund.prepaid",
	"Menu.ChangePassword",
	"Menu.BackOffice",
	"fr.pasteque.pos.panels.ReprintZTicket",
	"fr.pasteque.pos.panels.JPanelPrinter",
	"fr.pasteque.pos.config.JPanelConfiguration",
	"button.print",
	"button.opendrawer",
	"button.openmoney",
);

$message = null;
$error = null;
$srv = new \Pasteque\RolesService();
if (isset($_POST['id'])) {
	if (isset($_POST['name'])) {
		$permissions = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<permissions>\n";
		if (isset($_POST['permissions'])) {
			foreach($_POST['permissions'] as $perm) {
				$permissions .= "    <class name=\"" . $perm . "\"/>\n";
			}
		}
		if (isset($_POST['permissions-sup'])) {
			foreach(preg_split("/((\r?\n)|(\r\n?))/",$_POST['permissions-sup']) as $perm) {
				$permissions .= "    <class name=\"" . $perm . "\"/>\n";
			}
		}
		$permissions .= "</permissions>";
		$role = \Pasteque\Role::__build($_POST['id'], $_POST['name'], $permissions);
		if ($srv->update($role)) {
			$message = \i18n("Changes saved");
		} else {
			$error = \i18n("Unable to save changes");
		}
	}
} else if (isset($_POST['name'])) {
	if (isset($_POST['name'])) {
		$permissions = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<permissions>\n";
		if (isset($_POST['permissions'])) {
			foreach($_POST['permissions'] as $perm) {
				$permissions .= "    <class name=\"" . $perm . "\"/>\n";
			}
		}
		if (isset($_POST['permissions-sup'])) {
			foreach(preg_split("/((\r?\n)|(\r\n?))/",$_POST['permissions-sup']) as $perm) {
				$permissions .= "    <class name=\"" . $perm . "\"/>\n";
			}
		}
		$permissions .= "</permissions>";
		$role = new \Pasteque\Role($_POST['name'], $permissions);
		$id = $srv->create($role);
		if ($id !== FALSE) {
			$message = \i18n("Role saved. <a href=\"%s\">Go to the role page</a>.", PLUGIN_NAME, \Pasteque\get_module_url_action(PLUGIN_NAME, 'role_edit', array('id' => $id)));
		} else {
			$error = \i18n("Unable to save changes");
		}
	}
}

$role = null;
if (isset($_GET['id'])) {
	$role = $srv->get($_GET['id']);
}

//Title
echo \Pasteque\mainTitle(\i18n("Edit a role", PLUGIN_NAME));
\Pasteque\tpl_msg_box($message, $error);

$content .= \Pasteque\form_hidden("edit", $role, "id");
$content .= \Pasteque\form_input("edit", "Role", $role, "name", "string", array("required" => true));
$label = \i18n("Permissions", PLUGIN_NAME);
$displayData = "";
foreach ($ALL_PERMS as $perm) {
	$checked = (isset($role) && $role->hasPermission($perm)) ? true : false;
	$displayData .= \Pasteque\form_checkbox("perm" . $perm, $checked, \i18n($perm, PLUGIN_NAME));
}
$content .= \Pasteque\form_fieldset($label,$displayData);
$label = \i18n("New permissions", PLUGIN_NAME);
$displayData = \Pasteque\form_textarea("permissions-sup");
//$content .= \Pasteque\form_fieldset($label,$displayData);
$content .= \Pasteque\form_save();
echo \Pasteque\form_generate(\Pasteque\get_current_url(), "post", $content);
?>
