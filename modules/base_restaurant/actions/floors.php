<?php
//    Pastèque Web back office, Restaurant module
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

namespace BaseRestaurant;

$message = null;
$error = null;

if (isset($_POST['floorData'])) {
	$js = json_decode($_POST['floorData']);
	foreach ($js as $jsFloor) {
		if ($jsFloor->status == "NEW") {
			$floor = new \Pasteque\Floor($jsFloor->label, false);
			foreach ($jsFloor->places as $jsPlace) {
				$place = new \Pasteque\Place($jsPlace->label, $jsPlace->x,
						$jsPlace->y, null);
				$floor->addPlace($place);
			}
			$floor->id = \Pasteque\PlacesService::createFloor($floor, null);
			foreach ($floor->places as $place) {
				$place->floorId = $floor->id;
				$place->id = \Pasteque\PlacesService::createPlace($place);
			}
		} else if ($jsFloor->status == "DELETE") {
			if (\Pasteque\PlacesService::deleteFloor($jsFloor->id)) {
				$message = \i18n("Changes saved", PLUGIN_NAME);
			} else {
				$error = \i18n("Unable to save some changes", PLUGIN_NAME);
			}
		} else {
			$floor = \Pasteque\Floor::__build($jsFloor->id, $jsFloor->label,
					false);
			if (\Pasteque\PlacesService::updateFloor($floor, null)) {
				$success = true;
				foreach ($jsFloor->places as $jsPlace) {
					if ($jsPlace->status == "NEW") {
						$place = new \Pasteque\Place($jsPlace->label,
								$jsPlace->x, $jsPlace->y, $floor->id);
						$floor->addPlace($place);
						if (!\Pasteque\PlacesService::createPlace($place)) {
							$success = false;
						}
					} else if ($jsPlace->status == "DELETE") {
						if (!\Pasteque\PlacesService::deletePlace($jsPlace->id)) {
							$success = false;
						}
					} else {
						$place = \Pasteque\Place::__build($jsPlace->id,
								$jsPlace->label, $jsPlace->x,
								$jsPlace->y, $floor->id);
						if (!\Pasteque\PlacesService::updatePlace($place)) {
							$success = false;
						}
					}
				}
				if ($success) {
					$message = \i18n("Changes saved", PLUGIN_NAME);
				} else {
					$error = \i18n("Unable to save some changes", PLUGIN_NAME);
				}
			} else {
				$error = \i18n("Unable to save changes", PLUGIN_NAME);
			}
		}
	}
	if ($error) {
		$message = null;
	}
}

$floors = \Pasteque\PlacesService::getAllFloors();
//Title
\Pasteque\row(\Pasteque\mainTitle(\i18n("Floors configuration", PLUGIN_NAME)));
//Informations
\Pasteque\tpl_msg_box($message, $error);
?>
<form class="edit" method="post" onsubmit="javascript:save()" action="<?php echo \Pasteque\get_current_url();?>">
<?
	$legend = \i18n("Floor", PLUGIN_NAME);
	$content = "
	<div class=\"row\">
	<label for=\"listFloors\">" . \i18n("Floors", PLUGIN_NAME) . "</label>
	<select id=\"listFloors\" onchange=\"showFloor()\"></select>
	</div>
	<div class=\"row\">
	<label for=\"floorLabel\">" . \i18n('Floor.label') . "</label>
	<input type=\"text\" id=\"floorLabel\" onchange=\"javascript:updateFloor();\">
	</div>
	<div class=\"row actions\">";
	$buttonGroup = \Pasteque\jsAddButton(\i18n("Add a floor", PLUGIN_NAME), "newFloor()");
	$buttonGroup .= \Pasteque\jsDeleteButton(\i18n("Delete floor", PLUGIN_NAME),"deleteCurrentFloor()");
	$content .= \Pasteque\buttonGroup($buttonGroup);
	$content .= "</div>";
	echo \Pasteque\form_fieldset($legend,$content);

	$legend = \i18n("Place", PLUGIN_NAME);
	$content = "
	<div class=\"row\">
	<label for=\"placeLabel\">" . \i18n('Place.label') . "</label>
	<input type=\"text\" id=\"placeLabel\" onchange=\"javascript:updatePlaceLabel();\" onkeyup=\"javascript:updatePlaceLabel();\">
	</div>
	<div class=\"row actions\">";
	$buttonGroup = \Pasteque\jsAddButton(\i18n("Add place", PLUGIN_NAME), "newPlace()");
	$buttonGroup .= \Pasteque\jsDeleteButton(\i18n("Delete place", PLUGIN_NAME), "deletePlace()");
	$content .= \Pasteque\buttonGroup($buttonGroup);
	$content .= "</div>";
	echo \Pasteque\form_fieldset($legend,$content);
?>
	<div id="floorDivContainer" class="row"></div>
	<div class="row actions">
		<input id="floorData" name="floorData" type="hidden">
		<?php echo \Pasteque\form_save();?>
	</div>
</form>

<script src="<?php echo \Pasteque\get_module_action(PLUGIN_NAME, "jquery-ui-1.10.3.custom.js")?>" type="text/javascript"></script>
<script src="<?php echo \Pasteque\get_module_action(PLUGIN_NAME, "moteur.js")?>" type="text/javascript"></script>

<script type="text/javascript">
var floorInit;
var newPlaceLabel = "<?php \pi18n("New place", PLUGIN_NAME); ?>";
<?php

// insert all floors and all places:
foreach ($floors as $floor) {
	echo "floorInit = registerFloor(\"" . $floor->id . "\", \"" . $floor->label . "\");\n";
	foreach ($floor->places as $place) {
		echo "floorInit.registerPlace(\"" . $place->id . "\", \"" . $place->label
			. "\", " . $place->x . ", " . $place->y. ");\n";
	}
}
?>
showFloor();
</script>
