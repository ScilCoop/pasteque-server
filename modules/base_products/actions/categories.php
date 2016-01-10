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

// categories action

namespace BaseProducts;

$message = NULL;
$error = NULL;
if (isset($_POST['delete-cat'])) {
    if (\Pasteque\CategoriesService::deleteCat($_POST['delete-cat'])) {
        $message = \i18n("Changes saved");
    } else {
        $error = \i18n("Unable to save changes");
        $error .= " " . \i18n("Only empty category can be deleted", PLUGIN_NAME);
    }
}

$categories = \Pasteque\CategoriesService::getAll();

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Categories", PLUGIN_NAME)));
//Buttons
$buttons = \Pasteque\addButton(\i18n('Add a category', PLUGIN_NAME),\Pasteque\get_module_url_action(PLUGIN_NAME, "category_edit"));
$buttons .= \Pasteque\importButton(\i18n('Import categories', PLUGIN_NAME),\Pasteque\get_module_url_action(PLUGIN_NAME, "categoriesManagement"));
echo \Pasteque\row(\Pasteque\buttonGroup($buttons));
//Information
\Pasteque\tpl_msg_box($message, $error); 
//Counter
echo \Pasteque\row(\Pasteque\counterDiv(\i18n("%d categories", PLUGIN_NAME, count($categories))));

if (count($categories) == 0) {
	echo \Pasteque\errorDiv(\i18n("No category found", PLUGIN_NAME));
}
else {
	$content[0][0] = \i18n("Category.label");
	$i = 1;
	foreach ($categories as $category) {
		if ($category->hasImage) {
			$imgSrc = \Pasteque\PT::URL_ACTION_PARAM . "=img&w=category&id=" . $category->id;
		} else {
			$imgSrc = \Pasteque\PT::URL_ACTION_PARAM . "=img&w=category";
		}
		$btn_group = \Pasteque\editButton(\i18n('Edit', PLUGIN_NAME), \Pasteque\get_module_url_action(PLUGIN_NAME, 'category_edit', array("id" => $category->id)));
		$btn_group .= \Pasteque\deleteButton(\i18n('Delete', PLUGIN_NAME), \Pasteque\get_current_url() . "&delete-cat=" . $category->id);
		$content[$i][0] .= "<img class=\"img img-thumbnail thumbnail pull-left\" src=\"?" . $imgSrc . "\">";
		$content[$i][0] .= $category->label;
		$content[$i][0] .= \Pasteque\buttonGroup($btn_group, "pull-right");
		$i++;
	}
	echo \Pasteque\row(\Pasteque\standardTable($content));
}
?>
