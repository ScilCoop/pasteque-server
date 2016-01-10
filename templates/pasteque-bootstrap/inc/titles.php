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

function mainTitle($title, $subtitle=null) {
	$title = sprintf("<h1>%s", $title);
	if($subtitle !== null) {
		$title .= sprintf(" <small>%s</small>", $subtitle);
	}
	$title .= "</h1>";
	return $title;
}

function secondaryTitle($title, $subtitle=null) {
	$title = sprintf("<h2>%s", $title);
	if($subtitle !== null) {
		$title .= sprintf(" <small>%s</small>", $subtitle);
	}
	$title .= "</h2>";
	return $title;
}

function h1Title($title, $subtitle=null) {
	$title = sprintf("<h1>%s", $title);
	if($subtitle !== null) {
		$title .= sprintf(" <small>%s</small>", $subtitle);
	}
	$title .= "</h1>";
	return $title;
}

function h2Title($title, $subtitle=null) {
	$title = sprintf("<h2>%s", $title);
	if($subtitle !== null) {
		$title .= sprintf(" <small>%s</small>", $subtitle);
	}
	$title .= "</h2>";
	return $title;
}

function h3Title($title, $subtitle=null) {
	$title = sprintf("<h3>%s", $title);
	if($subtitle !== null) {
		$title .= sprintf(" <small>%s</small>", $subtitle);
	}
	$title .= "</h3>";
	return $title;
}

function h4Title($title, $subtitle=null) {
	$title = sprintf("<h4>%s", $title);
	if($subtitle !== null) {
		$title .= sprintf(" <small>%s</small>", $subtitle);
	}
	$title .= "</h4>";
	return $title;
}

function h5Title($title, $subtitle=null) {
	$title = sprintf("<h5>%s", $title);
	if($subtitle !== null) {
		$title .= sprintf(" <small>%s</small>", $subtitle);
	}
	$title .= "</h5>";
	return $title;
}

function h6Title($title, $subtitle=null) {
	$title = sprintf("<h6>%s", $title);
	if($subtitle !== null) {
		$title .= sprintf(" <small>%s</small>", $subtitle);
	}
	$title .= "</h6>";
	return $title;
}
?>
