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

$message = NULL;
$error = NULL;

$pdo = \Pasteque\PDOBuilder::getPDO();

$sessId = $_GET['id'];
$session = \Pasteque\CashesService::get($sessId);
$zticket = \Pasteque\CashesService::getZTicket($sessId);

$crSrv = new \Pasteque\CashRegistersService();
$cashRegister = $crSrv->get($session->cashRegisterId);

if ($session->isClosed()) {
	$title = \i18n("Closed session", PLUGIN_NAME);
} else {
	$title = \i18n("Active session", PLUGIN_NAME);
}

echo \Pasteque\row(\Pasteque\mainTitle($title));

$content[0][] = \i18n("Session");
$content[0][] = "";
$content[1][] = \i18n("CashRegister.label");
$content[1][] = $cashRegister->label;
$content[2][] = \i18n("Session.openDate");
$content[2][] = \i18nDateTime($session->openDate);
$content[3][] = \i18n("Session.closeDate");
if ($session->isClosed()) {
	$content[3][] = \i18nDateTime($session->closeDate);
}
else {
	$content[3][] = "";
}
$content[4][] = \i18n("Tickets", PLUGIN_NAME);
$content[4][] = $zticket->ticketCount;
$content[5][] = \i18n("Consolidated sales", PLUGIN_NAME);
$content[5][] = \i18nCurr($zticket->cs);
echo \Pasteque\row(\Pasteque\standardTable($content));

unset($content);

$currSrv = new \Pasteque\CurrenciesService();
$content[0][] = \i18n("Payments", PLUGIN_NAME);
$content[0][] = \i18n("Amount", PLUGIN_NAME);
$i = 0;
foreach ($zticket->payments as $payment) {
    $currency = $currSrv->get($payment->currencyId);
    if ($currency->isMain) {
        $amount = \i18nCurr($payment->amount);
    } else {
        $amount = $currency->format($payment->currencyAmount) . " ("
                . \i18nCurr($payment->amount) . ")";
    }
	$content[$i][] = \i18n($payment->type);
	$content[$i][] = $amount;
	$i++;
}
if($i == 0) {
	$content[1][] = \i18n("No payment", PLUGIN_NAME);
	$content[1][] = "";
}
echo \Pasteque\row(\Pasteque\standardTable($content));

unset($content);

$content[0][] = \i18n("Taxes", PLUGIN_NAME);
$content[0][] = \i18n("Base", PLUGIN_NAME);
$content[0][] = \i18n("Amount", PLUGIN_NAME);
$i = 1;
foreach ($zticket->taxes as $tax) { 
	$content[$i][] = \Pasteque\TaxesService::getTax($tax["id"])->label;
	$content[$i][] = \i18nCurr($tax['base']);
	$content[$i][] = \i18nCurr($tax['amount']);
	$i++;
}
if($i == 1) {
	$content[1][] = \i18n("No payment", PLUGIN_NAME);
	$content[1][] = "";
	$content[1][] = "";
}
echo \Pasteque\row(\Pasteque\standardTable($content));

unset($content);

$content[0][] = \i18n("Sales by category", PLUGIN_NAME);
$content[0][] = \i18n("Amount", PLUGIN_NAME);
$i = 1;
foreach ($zticket->catSales as $cat) {
	$content[$i][] = \i18n(\Pasteque\CategoriesService::get($cat["id"]->label, PLUGIN_NAME));
	$content[$i][] = $cat["amount"];
	$i++;
}
if($i == 1) {
	$content[1][] = \i18n("No payment", PLUGIN_NAME);
	$content[1][] = "";
}
echo \Pasteque\row(\Pasteque\standardTable($content));
?>
