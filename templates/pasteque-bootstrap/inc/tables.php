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

function standardTable($content) {
	$table = "<table class=\"table table-bordered table-hover\">";
	$table .= "<thead>";
	$table .= "<tr>";
	for($j = 0; $j < sizeof($content[0]); $j++) {
		$table .= sprintf("<th>%s</th>",$content[0][$j]);
	}
	$table .= "</tr>";
	$table .= "</thead>";
	$table .= "<tbody>";
	for($i = 1; $i < sizeof($content); $i++) {
		$table .= "<tr>";
		for($j = 0; $j < sizeof($content[$i]); $j++) {
			$table .= sprintf("<td>%s</td>",$content[$i][$j]);
		}
		$table .= "</tr>";
	}
	$table .= "</tbody>";
	$table .= "</table>";
	return $table;
}

?>
