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

function vanillaDiv($content, $id=null) {
	if($id === null) {
		return sprintf("<div>%s</div>\n",$content);
	}
	else {
		return sprintf("<div id=\"%s\">%s</div>\n",$id,$content);
	}
}

function counterDiv($content, $id=null) {
	if($id === null) {
		return sprintf("<div class=\"alert alert-info\">%s</div>\n",$content);
	}
	else {
		return sprintf("<div id=\"%s\" class=\"alert alert-info\">%s</div>\n",$id,$content);
	}
}

function infoDiv($content, $id=null) {
	if($id === null) {
		return sprintf("<div class=\"alert alert-info\">%s</div>\n",$content);
	}
	else {
		return sprintf("<div id=\"%s\" class=\"alert alert-info\">%s</div>\n",$id,$content);
	}
}

function successDiv($content, $id=null) {
	if($id === null) {
		return sprintf("<div class=\"alert alert-success\">%s</div>\n",$content);
	}
	else {
		return sprintf("<div id=\"%s\" class=\"alert alert-success\">%s</div>\n",$id,$content);
	}
}

function warningDiv($content, $id=null) {
	if($id === null) {
		return sprintf("<div class=\"alert alert-warning\">%s</div>\n",$content);
	}
	else {
		return sprintf("<div id=\"%s\" class=\"alert alert-warning\">%s</div>\n",$id,$content);
	}
}

function errorDiv($content, $id=null) {
	if($id === null) {
		return sprintf("<div class=\"alert alert-danger\">%s</div>\n",$content);
	}
	else {
		return sprintf("<div id=\"%s\" class=\"alert alert-danger\">%s</div>\n",$id,$content);
	}
}
?>
