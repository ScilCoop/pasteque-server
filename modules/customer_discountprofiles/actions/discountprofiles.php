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

namespace CustomerDiscountProfiles;

$message = null;
$error = null;
$srv = new \Pasteque\DiscountProfilesService();

if (isset($_GET['delete-profile'])) {
	if ($srv->delete($_GET['delete-profile'])) {
		$message = \i18n("Changes saved");
	} else {
		$error = \i18n("Unable to save changes");
	}
}

$profiles = $srv->getAll();

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Discount profiles", PLUGIN_NAME)));
//Buttons
$buttons = \Pasteque\addButton(\i18n("New discount profile", PLUGIN_NAME), \Pasteque\get_module_url_action(PLUGIN_NAME, "discountprofile_edit"));
echo \Pasteque\buttonGroup($buttons);
//Informations
\Pasteque\tpl_msg_box($message,$error);
//Counter
echo \Pasteque\row(\Pasteque\counterDiv(\i18n("%d profiles", PLUGIN_NAME, count($profiles))));

if (count($profiles) == 0) {
	echo \Pasteque\errorDiv(\i18n("No profile found", PLUGIN_NAME));
}
else {
	$content[0][0] = \i18n("DiscountProfile.label");
	$content[0][1] = \i18n("DiscountProfile.rate");
	$i = 1;
	foreach ($profiles as $profile) {
		$content[$i][0] = $profile->label;
		$content[$i][1] = $profile->rate;
		$btn_group = \Pasteque\editButton(\i18n('Edit'), \Pasteque\get_module_url_action(PLUGIN_NAME, 'discountprofile_edit', array("id" => $profile->id)));
		$btn_group .= \Pasteque\deleteButton(\i18n("Delete", PLUGIN_NAME), \Pasteque\get_current_url() . "&delete-profile=" . $profile->id);
		$content[$i][1] .= \Pasteque\buttonGroup($btn_group, "pull-right");
		$i++;
	}
	echo \Pasteque\row(\Pasteque\standardTable($content));
}
?>
