<?php
//    POS-Tech API
//
//    Copyright (C) 2012 Scil (http://scil.coop)
//
//    This file is part of POS-Tech.
//
//    POS-Tech is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    POS-Tech is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with POS-Tech.  If not, see <http://www.gnu.org/licenses/>.

namespace Pasteque;

class Cash {

    public $id;
    public $host;
    public $sequence;
    /** Open date as timestamp */
    public $openDate;
    /** Close date as timestamp, may be null */
    public $closeDate;
    public $openCash;
    public $closeCash;
    /** Optionnal tickets count */
    public $tickets;
    /** Optionnal total */
    public $total;

    static function __build($id, $host, $sequence, $openDate, $closeDate,
            $openCash, $closeCash) {
        $cash = new Cash($host, $sequence, $openDate, $closeDate, $openCash,
                $closeCash);
        $cash->id = $id;
        return $cash;
    }

    function __construct($host, $sequence, $openDate, $closeDate, $openCash,
            $closeCash) {
        $this->host = $host;
        $this->sequence = $sequence;
        $this->openDate = $openDate;
        $this->closeDate = $closeDate;
        $this->openCash = $openCash;
        $this->closeCash = $closeCash;
    }

    function isClosed() {
        return $this->closeDate != null;
    }

    function isOpened() {
        return $this->openDate != null;
    }
}

?>
