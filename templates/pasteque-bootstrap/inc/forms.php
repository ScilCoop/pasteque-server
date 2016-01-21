<?php
//    Pastèque Web back office
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
namespace Pasteque;

/** Escape data to be used inside html attribute */
function esc_attr($value) {
	return htmlspecialchars($value);
}
/** Escape data to be used in html content */
function esc_html($value) {
	return htmlspecialchars($value, ENT_NOQUOTES);
}
/** Escape a JS variable to be enclosed in double quotes */
function esc_js($value) {
	return addslashes($value);
}

function form_hidden($form_id, $object, $field) {
	if ($object != NULL && isset($object->{$field})) {
		return "<input type=\"hidden\" name=\"" . esc_attr($field) . "\" value=\""
			. esc_attr($object->{$field}) . "\"/>\n";
	}
}

function form_value_hidden($form_id, $name, $value) {
	return "<input id=\"" . $form_id . "\" type=\"hidden\" name=\"" . esc_attr($name)
		. "\" value=\"" . esc_attr($value) . "\"/>\n";
}

function form_number($id, $value=null, $label=null, $step=null, $min=null, $max=null, $class=null, $readonly=false) {
	$ret = "";
	if($label !== null) {
		$ret = "<label for=\"" . $id . "\" class=\"control-label\">" . $label . "</label>\n";
	}
	$ret .= "<input type=\"number\" class=\"form-control";
	if($class !== null) {
		$ret .= " " .$class;
	}
	$ret .= "\"";
	if($value !== null) {
		$ret .= " value=\"" . $value . "\"";
	}
	if($step !== null) {
		$ret .= " step=\"" . $step . "\"";
	}
	if($min !== null) {
		$ret .= " min=\"" . $min . "\"";
	}
	if($max !== null) {
		$ret .= " max=\"" . $max . "\"";
	}
	if($readonly) {
		$ret .= " readonly=\"true\"";
	}
	$ret .= ">\n";
	return $ret;
}

function form_date($id, $value, $label=null, $format=null, $class=null) {
	$ret = "";
	if($label !== null) {
		$ret = "<label for=\"". $id ."\" class=\"control-label\">" . $label . "</label>\n";
	}
	$ret .= "<div data-date-format=\"";
	if($format !== null) {
		$ret .= $format;
	}
	else {
		$ret .= "yyyy-mm-dd";
	}
	$ret .= "\" class=\"input-group date";
	if($class !== null) {
		$ret .= $class;
	}
	$ret .= "\">\n";
	$ret .= "\t<input type=\"text\" class=\"form-control\" name=\"";
	$ret .= $id . "\" id=\"" . $id . "\" value=\"" . $value . "\">\n";
	$ret .= "\t<div class=\"input-group-addon\">\n";
	$ret .= "\t\t<span class=\"glyphicon glyphicon-th\"></span>\n";
	$ret .= "\t</div>\n";
	$ret .= "</div>\n";
	return $ret;
}

function form_file($id, $name="file", $label=null, $class=null) {
	$ret = "";
	if($label !== null) {
		$ret = "<label for=\"". $id ."\" class=\"control-label\">" . $label . "</label>\n";
	}
	$ret .= "<input id=\"" . $id . "\" type=\"file\" name=\"" . $name . "\">\n";
	return $ret;

}

function form_input($form_id, $class, $object, $field, $type, $args = array()) {
	$ret = "";
	if (!isset($args['nolabel']) || $args['nolabel'] === false) {
		$ret .= "<div class=\"form-group\">\n";
	}
	if (isset($args['nameid']) && $args['nameid'] == true) {
		$name = $field . "-" . $form_id;
	} else {
		$name = $field;
	}
	if (isset($args['array']) && $args['array'] == true) {
		$name = $name . "[]";
	}
	if ($type != "pick_multiple") {
		if (!isset($args['nolabel']) || $args['nolabel'] === false) {
			$ret .= "\t<label for=\"" . esc_attr($form_id . "-" . $field) . "\" class=\"control-label\">";
			$fieldLabel = $field;
			if (substr($field, -2) == "Id") {
				$fieldLabel = substr($field, 0, -2);
			}
			$ret .= esc_html(\i18n($class . "." . $fieldLabel));
			$ret .= "</label>\n";
		}
	}
	$required = "";
	if (isset($args['required']) && $args['required']) {
		$required = " required=\"true\"";
	}
	switch ($type) {
		case 'string':
			$ret .= "\t<input class=\"form-control\" id=\"" . esc_attr($form_id . "-" . $field)
				. "\" type=\"text\" name=\"" . esc_attr($name) . "\"";
			if ($object != NULL) {
				$ret .= " value=\"" . esc_attr($object->{$field}) . "\"";
			}
			$ret .= "$required />\n";
			break;
		case 'text':
			$ret .= "\t<textarea class=\"form-control\" id=\"" . esc_attr($form_id . "-" . $field)
				. "\" name=\"" . esc_attr($name) . "\">";
			if ($object != NULL) {
				$ret .= esc_html($object->{$field});
			}
			$ret .= "</textarea>";
			break;
		case 'numeric':
			$ret .= "\t<input class=\"form-control\" id=\"" . esc_attr($form_id . "-" . $field)
				. "\" type=\"numeric\" name=\"" . esc_attr($name) . "\"";
			if ($object != NULL) {
				$ret .= " value=\"" . esc_attr($object->{$field}) . "\"";
			}
			$ret .= $required . ">\n";
			break;
		case 'boolean':
			$ret .= "\t<input class=\"form-control\" id=\"" . esc_attr($form_id . "-" . $field)
				. "\" type=\"checkbox\" name=\"" . esc_attr($name) . "\"";
			if ($object != NULL) {
				if ($object->{$field}){
					$ret .= " checked=\"checked\"";
				}
			} else {
				if (!isset($args['default']) || $args['default'] == TRUE) {
					$ret .= " checked=\"checked\"";
				}
			}
			$ret .= ">\n";
			break;
		case 'float':
			if (!isset($args['step'])) {
				$step = 0.01;
			} else {
				$step = $args['step'];
			}
			$ret .= "\t<input class=\"form-control\" id=\"" . esc_attr($form_id . "-" . $field)
				. "\" type=\"number\" step=\"" . esc_attr($step)
				. "\" min=\"0.00\" name=\"" . esc_attr($name) . "\"";
			if ($object != NULL) {
				$ret .= " value=\"" . esc_attr($object->{$field}) . "\"";
			}
			$ret .= $required . ">\n";
			break;
		case 'date':
			// Class dateinput will be catched to show js date picker
			$ret .= "\t<input class=\"form-control\" id=\"" . esc_attr($form_id . "-" . $field)
				. "\" type=\"text\" class=\"dateinput\" name=\"" . esc_attr($name) . "\"";
			if ($object !== null) {
				if (isset($args['dataformat'])) {
					if ($args['dataformat'] == 'standard') {
						$timestamp = stdtimefstr($object->{$field});
					} else {
						$timestamp = timefstr($args['dataformat'],
								$object->{$field});
					}
				} else {
					$timestamp = $object->{$field};
				}
				$ret .= " value=\"" . esc_attr(\i18nDate($timestamp)) . "\"";
			}
			$ret .= $required . ">\n";
			break;
		case 'pick':
			$model = $args['model'];
			$data = $args['data'];
			if ($model !== null) {
				switch ($model) {
					case 'Category':
						$data = CategoriesService::getAll(false);
						break;
					case 'Provider':
						$data = ProvidersService::getAll();
						break;
					case 'TaxCategory':
						$data = TaxesService::getAll();
						break;
					case 'Tax':
						$cats = TaxesService::getAll();
						$data = array();
						foreach ($cats as $cat) {
							$data[] = $cat->getCurrentTax();
						}
						break;
					case 'CustTaxCat':
						$data = CustTaxCatsService::getAll();
						break;
					case 'Role':
						$data = RolesService::getAll();
						break;
					case 'Attribute':
						$data = AttributesService::getAllAttrs();
						break;
					case 'AttributeSet':
						$data = AttributesService::getAll();
						break;
					case 'Location':
						$locSrv = new LocationsService();
						$data = $locSrv->getAll();
						break;
					case 'DiscountProfile':
						$profSrv = new DiscountProfilesService();
						$data = $profSrv->getAll();
						break;
					case 'TariffArea':
						$areaSrv = new TariffAreasService();
						$data = $areaSrv->getAll();
						break;
				}
			}
			$ret .= "\t<select class=\"form-control\" id=\"" . esc_attr($form_id . "-" . $field)
				. "\" name=\"" . esc_attr($name) . "\">\n";
			if (isset($args['nullable']) && $args['nullable']) {
				$ret .= "\t\t<option value=\"\"></option>\n";
			}
			foreach ($data as $r) {
				$selected = "";
				$r_id = $r->id;
				$r_label = $r->label;
				if ($model == null) {
					$r_id = $r['id'];
					$r_label = $r['label'];
				}
				if ($object != NULL && ($object->{$field} == $r_id
							|| (is_object($object->{$field}) && $object->{$field}->id == $r_id))) {
					$selected = " selected=\"true\"";
				}
				$ret .= "\t\t<option value=\"" . esc_attr($r_id) . "\"" . $selected . ">"
					. esc_html($r_label) . "</option>\n";
			}
			$ret .= "</select>\n";
			break;
		case 'pick_multiple':
			$model = $args['model'];
			switch ($model) {
				case 'Category':
					$data = CategoriesService::getAll();
					break;
			}
			foreach ($data as $r) {
				$selected = "";
				if ($object != NULL
						&& (array_search($r->id, $object->{$field}) !== FALSE)) {
					$selected = ' checked="true"';
				}
				$id = $form_id . "-" . $field . "-" .$r->id;
				$ret .= "\t<label for=\"" . esc_attr($id) . "\" class=\"control-label\">" . esc_html($r->label) . "</label>\n";
				$ret .= "\t<input class=\"form-control\" id=\"" . esc_attr($id) . "\" type=\"checkbox\" name=\""
					. esc_attr($name) . '[]" value="' . esc_attr($r->id) . '"'
					. $selected . "/>\n";
			}
			break;
	}
	if (!isset($args['nolabel']) || $args['nolabel'] === false) {
		$ret .= "</div>\n";
	}
	return $ret;
}

/** Create a select with given labels. For relation in a model use form_input
 * with type pick */
function form_select($id, $label, $values, $labels, $currentValue=null) {
	$ret =  "<label for=\"" . esc_attr($id) ."\" class=\"control-label\">" . esc_html($label) . "</label>\n";
	$ret .=  "<select id=\"" . esc_attr($id) . "\" class=\"form-control\" name=\"" . esc_attr($id) . "\">\n";
	for ($i = 0; $i < count($values); $i++) {
		$selected = "";
		if ($values[$i] === $currentValue) {
			$selected = ' selected="true"';
		}
		$ret .=  "<option value=\"" . esc_attr($values[$i]) . "\"" . $selected . ">"
			. esc_html($labels[$i]) . "</option>\n";
	}
	$ret .=  "</select>\n";
	return $ret;
}

function form_generate($action,$method,$content) {
	$ret = sprintf("<form class=\"form-horizontal\" action=\"%s\" method=\"%s\" enctype=\"multipart/form-data\">\n",$action,$method);
	$ret .= sprintf("%s",$content);
	$ret .= "</form>\n";
	return $ret;
}

function form_fieldset($legend,$content) {
	return sprintf("<fieldset>\n\t<legend>%s</legend>\n%s\n</fieldset>\n",$legend,$content);
}

function form_button($text,$class=null) {
	if($class !== null) {
		return "<button class=\"btn btn-primary btn-send " . $class . "\" type=\"submit\">" . $text . "</button>\n";
	}
	else {
		return "<button class=\"btn btn-primary btn-send\" type=\"submit\">" . $text . "</button>\n";
	}
}

function form_send() {
	return "<button class=\"btn btn-primary btn-send\" type=\"submit\">" . \i18n("Send") . "</button>\n";
}

function form_save() {
	return "<button class=\"btn btn-primary btn-send\" type=\"submit\">" . \i18n("Save") . "</button>\n";
}

function form_delete($what, $id, $img_src = NULL) {
	$ret =  '<input type="hidden" name="delete-' . esc_attr($what)
		. '" value="' . esc_attr($id) . '" />';
	if ($img_src == NULL) {
		$ret .=  '<button onclick="return confirm(\'' . \i18n('confirm deletion') . '\');return false;" type="submit"><a class="btn btn-delete">' . \i18n('Delete') . '</a></button>\n';
	} else {
		$ret .=  '<button onclick="return confirm(\'' . \i18n('confirm deletion') . '\');return false;" type="submit"><a class="btn btn-delete"><img src="' . esc_attr($img_src)
			. '" alt="' . esc_attr(\i18n('Delete'))
			. '" title="' . esc_attr(\i18n('Delete')) . '" /></a></button>\n';
	}
	return $ret;
}
