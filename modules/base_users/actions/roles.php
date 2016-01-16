<?php
//    Pastèque Web back office, Roles module
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

$srv = new \Pasteque\RolesService();
if (isset($_GET['delete-role'])) {
	$srv->delete($_GET['delete-role']);
}

$roles = $srv->getAll();

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Roles", PLUGIN_NAME)));
//Buttons
$buttons = \Pasteque\addButton(\i18n("Add a role", PLUGIN_NAME), \Pasteque\get_module_url_action(PLUGIN_NAME, "role_edit"));
echo \Pasteque\row(\Pasteque\buttonGroup($buttons));
//Information
\Pasteque\tpl_msg_box($message, $error);
// Counter
echo \Pasteque\row(\Pasteque\counterDiv(\i18n("%d roles", PLUGIN_NAME, count($roles))));

if(count($roles) == 0) {
	echo \Pasteque\errorDiv(\i18n("No role found", PLUGIN_NAME));
}
else {
	$content[0][0] = \i18n("Role.name");
	$i = 1;
	foreach ($roles as $role) {
		$btn_group = \Pasteque\editButton(\i18n('Edit', PLUGIN_NAME), \Pasteque\get_module_url_action(PLUGIN_NAME, 'role_edit', array("id" => $role->id)));
		$btn_group .= \Pasteque\deleteButton(\i18n('Delete', PLUGIN_NAME), \Pasteque\get_current_url() . "&delete-role=" . $role->id);
		$content[$i][0] = $role->name;
		$content[$i][0] .= \Pasteque\buttonGroup($btn_group, "pull-right");
		$i++;
	}
	echo \Pasteque\row(\Pasteque\standardTable($content));
}
?>
