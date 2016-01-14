<?php
//    Pastèque Web back office
//
//    Copyright (C) 2016 Scil (http://scil.coop)
//          Philippe Pary philippe@scil.coop
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

function buttonGroup($content,$style=null) {
	$buttonGroup = "<div class=\"btn-group";
	if($style !== null) {
		$buttonGroup .= sprintf(" %s",$style);
	}
	$buttonGroup .= sprintf("\" role=\"group\">%s</div>",$content);
	return $buttonGroup;
}

function addButton($text,$action) {
	return sprintf("<a class=\"btn btn-add\" href=\"%s\">%s</a>",$action,$text);
}

function editButton($text,$action) {
	return sprintf("<a class=\"btn btn-edit\" href=\"%s\">%s</a>",$action,$text);
}

function deleteButton($text,$action) {
	return sprintf("<a class=\"btn btn-delete\" onclick=\"return confirm('" . \i18n('confirm') ."');return false;\" href=\"%s\">%s</a>",$action,$text);
}

function importButton($text,$action) {
	return sprintf("<a class=\"btn btn-import\" href=\"%s\">%s</a>",$action,$text);
}

function exportButton($text,$action) {
	return sprintf("<a class=\"btn btn-export\" href=\"%s\">%s</a>",$action,$text);
}
?>
