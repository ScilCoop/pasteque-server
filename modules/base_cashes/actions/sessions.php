<?php
//    Pastèque Web back office, Users module
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

namespace BaseCashes;

$message = null;
$error = null;

$srv = new \Pasteque\CashesService();
$sessions = $srv->getAll();
$crSrv = new \Pasteque\CashRegistersService();

//Title
echo \Pasteque\row(\Pasteque\mainTitle(\i18n("Active sessions", PLUGIN_NAME)));
$content[0][] = \i18n("CashRegister.label");
$content[0][] = \i18n("Session.openDate");
$content[0][] = \i18n("Session.tickets");
$content[0][] = \i18n("Session.total");
$i = 1;
foreach ($sessions as $session) {
	$cashRegister = $crSrv->get($session->cashRegisterId);
	if (!$session->isClosed()) {
		$content[$i][0] = $cashRegister->label;
		$content[$i][1] = \i18nDatetime($session->openDate);
		$content[$i][2] = $session->tickets;
		$content[$i][3] = \i18nCurr($session->total);
		$content[$i][4] = "<a href=\"" . \Pasteque\get_module_url_action(PLUGIN_NAME, 'session_details', array('id' => $session->id)) . "\"><img src=\"" . \Pasteque\get_template_url() . "img/edit.png\" alt=\"" . \i18n('Edit') ."\" title=\"" . \i18n('Edit') ."\"></a>";
		$i++;
	}
}
echo \Pasteque\row(\Pasteque\standardTable($content));
