<?php
//    Pastèque Web back office, Users module
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

namespace BaseUsers;

if (isset($_GET['delete-user'])) {
	\Pasteque\UsersService::delete($_GET['delete-user']);
}

$srv = new \Pasteque\UsersService();
$users = $srv->getAll();

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Users", PLUGIN_NAME)));
//Buttons
$buttons = \Pasteque\addButton(\i18n("Add an user", PLUGIN_NAME), \Pasteque\get_module_url_action(PLUGIN_NAME, "user_edit"));
echo \Pasteque\row(\Pasteque\buttonGroup($buttons));
//Information
\Pasteque\tpl_msg_box($message, $error);
//Counter
echo \Pasteque\row(\Pasteque\counterDiv(\i18n("%d users", PLUGIN_NAME, count($users))));

if (count($users) == 0) {
	echo \Pasteque\errorDiv(\i18n("No user found", PLUGIN_NAME));
}
else {
	$content[0][0] = \i18n("User.name");
	$i = 1;
	foreach ($users as $user) {
		$btn_group = \Pasteque\editButton(\i18n('Edit', PLUGIN_NAME), \Pasteque\get_module_url_action(PLUGIN_NAME, 'user_edit', array("id" => $user->id)));
		$btn_group .= \Pasteque\deleteButton(\i18n('Delete', PLUGIN_NAME), \Pasteque\get_current_url() . "&delete-user=" . $user->id);
		$content[$i][0] = $user->name;
		$content[$i][0] .= \Pasteque\buttonGroup($btn_group, "pull-right");
		$i++;
	}
	echo \Pasteque\row(\Pasteque\standardTable($content));
}
?>
