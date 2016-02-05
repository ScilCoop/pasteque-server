<?php
//    Pastèque API
//
//    Copyright (C) 2012-2016 Scil (http://scil.coop)
//    Cédric Houbart, Philippe Pary
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

namespace BaseProducts;

// open csv return null if the file selected had not extension "csv"
// or user not selected file
function init_csv() {
	if ($_FILES['csv']['tmp_name'] === NULL) {
		return NULL;
	}
	$ext = strchr($_FILES['csv']['type'], "/");
	$ext = strtolower($ext);

	if($ext !== "/csv" && $ext !== "/plain") {
		return NULL;
	}

	$key = array('label', 'reference', 'parent', 'dispOrder');

	$csv = new \Pasteque\Csv($_FILES['csv']['tmp_name'], $key);

	if (!$csv->open()) {
		return $csv;
	}

	return $csv;
}

function import_csv($csv) {
	$error_mess = array();
	$update = 0;
	$create = 0;
	$error=0;

	while ($tab = $csv->readLine()) {
		$parentOk = false;
		if ($tab['parent'] !== NULL) {
			$parent = \Pasteque\CategoriesService::getByName($tab['parent']);
			$image = NULL;
			if ($parent) {
				$parentOk = true;
				$tab['parent'] = $parent->id;
			}
		} else {
			// Category isn't subCategory
			$parentOk = true;
		}

		if ($parentOk) {
			$cat = new \Pasteque\Category($tab['reference'], $tab['parent'], $tab['label'],
					$image, $tab['dispOrder']);
			$category_exist = \Pasteque\CategoriesService::getByName($cat->label);
			//UPDATE category
			if ($category_exist) {
				$cat->id = $category_exist->id;
				if (\Pasteque\CategoriesService::updateCat($cat)) {
					$update++;
				} else {
					$error++;
					$error_mess[] = \i18n("On line %d: Cannot update category: '%s'", PLUGIN_NAME,
							$csv->getCurrentLineNumber(), $tab['label']);
				}
				//CREATE category
			} else {
				$id = \Pasteque\CategoriesService::createCat($cat);
				if ($id) {
					$create++;
				} else {
					$error++;
					$error_mess[] = \i18n("On line %d: Cannot create category: '%s'", PLUGIN_NAME,
							$csv->getCurrentLineNumber(), $tab['label']);
				}
			}
		} else {
			$error++;
			$error_mess[] = \i18n("On line %d: Category parent doesn't exist",
					PLUGIN_NAME, $csv->getCurrentLineNumber());
		}
	}

	$message = \i18n("%d line(s) inserted, %d line(s) modified, %d error(s)",
			PLUGIN_NAME, $create, $update, $error );

	$csv->close();
	\Pasteque\tpl_msg_box($message, $error_mess);
}

if (isset($_FILES['csv'])) {
	$dateStr = isset($_POST['date']) ? $_POST['date'] : \i18nDate(time());
	$dateStr = \i18nRevDate($dateStr);
	$date = \Pasteque\stdstrftime($dateStr);

	$csv = init_csv();
	if ($csv === NULL) {
		\Pasteque\tpl_msg_box(NULL, \i18n("Selected file empty or bad format", PLUGIN_NAME));
	} else if (!$csv->isOpen()) {
		$err = array();
		foreach ($csv->getErrors() as $mess) {
			$err[] = \i18n($mess);
		}
		\Pasteque\tpl_msg_box(NULL, $err);
	} else {
		import_csv($csv, $date);
	}
}

echo \Pasteque\mainTitle(\i18n("Import category from csv file", PLUGIN_NAME));

$content = \Pasteque\form_file("csv","csv",\i18n("File", PLUGIN_NAME));
$content .= \Pasteque\form_send();
echo \Pasteque\form_generate(\Pasteque\get_current_url(), "post", $content);

?>
