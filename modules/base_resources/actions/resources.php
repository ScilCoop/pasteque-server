<?php
//    Pastèque Web back office, Resources module
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

namespace BaseResources;

$resSrv = new \Pasteque\ResourcesService();
if (isset($_GET['delete-res'])) {
    $resSrv->delete($_GET['delete-res']);
}

$resources = $resSrv->getAll();
//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Resources", PLUGIN_NAME)));
//Buttons
$buttons = \Pasteque\addButton(\i18n("Add a resource", PLUGIN_NAME), \Pasteque\get_module_url_action(PLUGIN_NAME, "resource_edit"));
echo \Pasteque\row(\Pasteque\buttonGroup($buttons));
//Informations
\Pasteque\tpl_msg_box($message, $error);
//Counter
echo \Pasteque\row(\Pasteque\counterDiv(\i18n("%d resources", PLUGIN_NAME, count($resources))));

if (count($resources) == 0) {
	echo \Pasteque\errorDiv(\i18n("No resource found", PLUGIN_NAME));
}
else {
	$content[0][0] = \i18n("Resource.label");
	$i = 1;
	foreach ($resources as $res) {
		$btn_group = \Pasteque\editButton(\i18n('Edit', PLUGIN_NAME), \Pasteque\get_module_url_action(PLUGIN_NAME, 'resource_edit', array("id" => $res->id)));
		$btn_group .= \Pasteque\deleteButton(\i18n('Delete', PLUGIN_NAME), \Pasteque\get_current_url() . "&delete-resource=" . $res->id);
		$content[$i][0] = $res->label;
		$content[$i][0] .= \Pasteque\buttonGroup($btn_group, "pull-right");
		$i++;
	}
	echo \Pasteque\row(\Pasteque\standardTable($content));
}
