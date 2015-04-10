<?php
//    Pastèque Web back office, Users module
//
//    Copyright (C) 2013 Scil (http://scil.coop)
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

namespace BaseSales;

$sql = "SELECT "
        . "PRODUCTS.REFERENCE, "
        . "PRODUCTS.NAME, "
        . "PRODUCTS.CATEGORY, "
        . "CATEGORIES.NAME AS CATNAME, "
        . "SUM(TICKETLINES.UNITS) AS UNITS, "
        . "SUM(TICKETLINES.UNITS * TICKETLINES.PRICE) AS TOTAL, "
        . "TICKETLINES.UNITS * TICKETLINES.PRICE * SUM(1 + TAXES.RATE) AS TAXEDTOTAL, "
        . "SUM(TICKETLINES.UNITS * (TICKETLINES.PRICE - PRODUCTS.PRICEBUY)) AS MARGIN "
        . "FROM RECEIPTS, TICKETS, TICKETLINES, PRODUCTS, "
        . "CLOSEDCASH, CATEGORIES, TAXES "
        . "WHERE "
        . "CLOSEDCASH.DATESTART > :start AND CLOSEDCASH.DATEEND < :stop "
        . "AND PRODUCTS.CATEGORY = CATEGORIES.ID "
        . "AND RECEIPTS.MONEY = CLOSEDCASH.MONEY "
        . "AND RECEIPTS.ID = TICKETS.ID AND TICKETS.ID = TICKETLINES.TICKET "
        . "AND TICKETLINES.PRODUCT = PRODUCTS.ID "
        . "AND TAXES.CATEGORY = PRODUCTS.TAXCAT "
        . "GROUP BY PRODUCTS.REFERENCE, PRODUCTS.NAME, PRODUCTS.CATEGORY "
        . "ORDER BY CATEGORIES.NAME, PRODUCTS.NAME ";

$fields = array("REFERENCE", "NAME", "UNITS", "TOTAL", "TAXEDTOTAL", "MARGIN");

$headers = array(\i18n("Product.reference"),
        \i18n("Product.label"),
        \i18n("Quantity"), \i18n("Total w/o VAT", PLUGIN_NAME),
        \i18n("Total", PLUGIN_NAME),
        \i18n("Margin", PLUGIN_NAME));

$report = new \Pasteque\Report(PLUGIN_NAME, "sales_by_category_report",
        \i18n("Sales by category", PLUGIN_NAME),
        $sql, $headers, $fields);

$report->addInput("start", \i18n("Session.openDate"), \Pasteque\DB::DATE);
$report->setDefaultInput("start", time() - (time() % 86400) - 7 * 86400);
$report->addInput("stop", \i18n("Session.closeDate"), \Pasteque\DB::DATE);
$report->setDefaultinput("stop", time() - (time() % 86400) + 86400);

$report->setGrouping("CATNAME");
$report->addSubTotal("TOTAL",\Pasteque\Report::TOTAL_SUM);
$report->addSubTotal("TAXEDTOTAL",\Pasteque\Report::TOTAL_SUM);
$report->addSubTotal("MARGIN",\Pasteque\Report::TOTAL_SUM);
$report->addFilter("DATESTART", "\Pasteque\stdtimefstr");
$report->addFilter("DATESTART", "\i18nDatetime");
$report->addFilter("DATEEND", "\Pasteque\stdtimefstr");
$report->addFilter("DATEEND", "\i18nDatetime");
$report->setVisualFilter("UNITS", "\i18nFlt", \Pasteque\Report::DISP_USER);
$report->setVisualFilter("UNITS", "\i18nFlt", \Pasteque\Report::DISP_CSV);
$report->setVisualFilter("TOTAL", "\i18nCurr", \Pasteque\Report::DISP_USER);
$report->setVisualFilter("TOTAL", "\i18nFlt", \Pasteque\Report::DISP_CSV);
$report->setVisualFilter("TAXEDTOTAL", "\i18nCurr", \Pasteque\Report::DISP_USER);
$report->setVisualFilter("TAXEDTOTAL", "\i18nFlt", \Pasteque\Report::DISP_CSV);
$report->setVisualFilter("MARGIN", "\i18nCurr", \Pasteque\Report::DISP_USER);
$report->setVisualFilter("MARGIN", "\i18nFlt", \Pasteque\Report::DISP_CSV);

\Pasteque\register_report($report);
