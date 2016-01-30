<?php
//    Pastèque Web back office, Users module
//
//    Copyright (C) 2013-2016 Scil (http://scil.coop)
//        Cédric Houbart, Philippe Pary
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

$sql = "SELECT TICKETS.CUSTCOUNT, " // nb cust / table
        . "COUNT(TICKETS.TICKETID) AS COUNT, " // nb tickets
        . "AVG(TAXLINES.BASE) AS AVGSUBPRICE, " // avg subprice
        . "AVG(TAXLINES.BASE + TAXLINES.AMOUNT) AS AVGPRICE " // agv vatprice
        . "FROM TICKETS, RECEIPTS, TAXLINES WHERE RECEIPTS.ID = TICKETS.ID "
        . "AND RECEIPTS.ID = TAXLINES.RECEIPT "
        . "AND RECEIPTS.DATENEW > :start "
        . "AND RECEIPTS.DATENEW < :stop "
        . "AND TICKETS.CUSTCOUNT IS NOT NULL "
        . "GROUP BY TICKETS.CUSTCOUNT "
        . "ORDER BY TICKETS.CUSTCOUNT";

$fields = array("CUSTCOUNT", "COUNT",
        "AVGSUBPRICE", "AVGPRICE");
$headers = array(\i18n("Custcount", PLUGIN_NAME),
        \i18n("Number of tickets", PLUGIN_NAME),
        \i18n("Average price w/o tax", PLUGIN_NAME),
        \i18n("Average price", PLUGIN_NAME)
        );

$report = new \Pasteque\Report(PLUGIN_NAME, "place_sales_report",
        \i18n("Place sales", PLUGIN_NAME),
        $sql, $headers, $fields);

$report->addInput("start", \i18n("Session.openDate"), \Pasteque\DB::DATE);
$report->setDefaultInput("start", time() - (time() % 86400) - 7 * 86400);
$report->addInput("stop", \i18n("Session.closeDate"), \Pasteque\DB::DATE);
$report->setDefaultinput("stop", time() - (time() % 86400) + 86400);

$report->setVisualFilter("AVGSUBPRICE", "\i18nCurr", \Pasteque\Report::DISP_USER);
$report->setVisualFilter("AVGSUBPRICE", "\i18nFlt", \Pasteque\Report::DISP_CSV);
$report->setVisualFilter("AVGPRICE", "\i18nCurr", \Pasteque\Report::DISP_USER);
$report->setVisualFilter("AVGPRICE", "\i18nFlt", \Pasteque\Report::DISP_CSV);

// sum of count / sum of tables
$report->addTotal("COUNT", \Pasteque\Report::TOTAL_SUM);
$report->addPonderate("CUSTCOUNT", "TABLES"); // COUNT = SUM(TABLE * CUSTCOUNT)
$report->addTotal("AVGSUBPRICE", \Pasteque\Report::TOTAL_AVG);
$report->addTotal("AVGPRICE", \Pasteque\Report::TOTAL_AVG);

\Pasteque\register_report($report);
