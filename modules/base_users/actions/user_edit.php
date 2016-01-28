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

// category_edit action

namespace BaseUsers;

$message = null;
$error = null;
$srv = new \Pasteque\UsersService();
if (isset($_POST['id']) && isset($_POST['name'])) {
	$user = \Pasteque\User::__build($_POST['id'], $_POST['name'], null, null,
			$_POST['roleId'], true, false);
	if ($srv->update($user)) {
		$message = \i18n("Changes saved");
	} else {
		$error = \i18n("Unable to save changes");
	}
} else if (isset($_POST['name'])) {
	$user = new \Pasteque\User($_POST['name'], null, null, $_POST['roleId'],
			true, false);
	$id = $srv->create($user);
	if ($id !== false) {
		$message = \i18n("User saved. <a href=\"%s\">Go to the user page</a>.", PLUGIN_NAME, \Pasteque\get_module_url_action(PLUGIN_NAME, 'user_edit', array('id' => $id)));
	} else {
		$error = \i18n("Unable to save changes");
	}
}

$user = null;
if (isset($_GET['id'])) {
	$user = $srv->get($_GET['id']);
}

//Title
echo \Pasteque\mainTitle(\i18n("Edit an user", PLUGIN_NAME));
//Information
\Pasteque\tpl_msg_box($message, $error);

$content .= \Pasteque\form_hidden("edit", $user, "id");
$content .= \Pasteque\form_input("edit", "User", $user, "name", "string", array("required" => true));
$content .= \Pasteque\form_input("edit", "User", $user, "roleId", "pick", array("model" => "Role"));
$content .= \Pasteque\form_save();
echo \Pasteque\form_generate(\Pasteque\get_current_url(),"post",$content);
?>
