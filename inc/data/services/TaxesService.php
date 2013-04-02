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

class TaxesService {

    private static function buildDBTaxCat($db_taxcat, $pdo) {
        $taxcat = TaxCat::__build($db_taxcat['ID'], $db_taxcat['NAME']);
        $sqltax = 'SELECT * FROM TAXES WHERE CATEGORY = "' . $db_taxcat['ID'] . '"';
        foreach ($pdo->query($sqltax) as $db_tax) {
            $tax = TaxesService::buildDBTax($db_tax);
            $taxcat->addTax($tax);
        }
        return $taxcat;
    }

    private static function buildDBTax($db_tax) {
        $tax = Tax::__build($db_tax['ID'], $db_tax['CATEGORY'],
                            $db_tax['NAME'], $db_tax['VALIDFROM'],
                            $db_tax['RATE']);
        return $tax;
    }

    static function getAll() {
        $taxcats = array();
        $pdo = PDOBuilder::getPDO();
        $sql = "SELECT * FROM TAXCATEGORIES";
        foreach ($pdo->query($sql) as $db_taxcat) {
            $taxcat = TaxesService::buildDBTaxCat($db_taxcat, $pdo);
            $taxcats[] = $taxcat;
        }
        return $taxcats;
    }

    static function get($id) {
        $pdo = PDOBuilder::getPDO();
        $stmt = $pdo->prepare("SELECT * FROM TAXCATEGORIES WHERE ID = :id");
        if ($stmt->execute(array(':id' => $id))) {
            if ($row = $stmt->fetch()) {
                return TaxesService::buildDBTaxCat($row, $pdo);
            }
        }
        return null;
    }

    static function updateCat($cat) {
        if ($cat->getId() == null) {
            return false;
        }
        $pdo = PDOBuilder::getPDO();
        $stmt = $pdo->prepare('UPDATE TAXCATEGORIES SET NAME = :name '
                              . 'WHERE ID = :id');
        return $stmt->execute(array(':name' => $cat->label, ':id' => $cat->id));
    }

    static function createCat($cat) {
        $pdo = PDOBuilder::getPDO();
        $id = md5(time() . rand());
        $stmt = $pdo->prepare('INSERT INTO TAXCATEGORIES (ID, NAME) VALUES '
                . '(:id, :name)');
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $cat->label);
        if (!$stmt->execute()) {
            return FALSE;
        } else {
            return $id;
        }
    }

    static function deleteCat($id) {
        $pdo = PDOBuilder::getPDO();
        $pdo->beginTransaction();
        $stmtTax = $pdo->prepare("DELETE FROM TAXES WHERE CATEGORY = :id");
        $stmtTax->bindParam(':id', $id);
        if ($stmtTax->execute() === FALSE) {
            $pdo->rollback();
            return FALSE;
        }
        $stmt = $pdo->prepare('DELETE FROM TAXCATEGORIES WHERE ID = :id');
        $stmt->bindParam(':id', $id);
        if ($stmt->execute() === FALSE) {
            $pdo->rollback();
            return FALSE;
        }
        $pdo->commit();
        return TRUE;
    }

    static function getTax($id) {
        $pdo = PDOBuilder::getPDO();
        $stmt = $pdo->prepare("SELECT * FROM TAXES WHERE ID = :id");
        if ($stmt->execute(array(':id' => $id))) {
            if ($row = $stmt->fetch()) {
                return TaxesService::buildDBTax($row, $pdo);
            }
        }
        return null;
    }

    static function updateTax($tax) {
        if ($tax->id == null) {
            return false;
        }
        $pdo = PDOBuilder::getPDO();
        $stmt = $pdo->prepare('UPDATE TAXES SET NAME = :name, VALIDFROM = :valid, '
                              . 'CATEGORY = :cat, RATE = :rate '
                              . 'WHERE ID = :id');
        $date = strftime("%Y-%m-%d %H:%M:%S", $tax->start_date);
        return $stmt->execute(array(':name' => $tax->label,
                                    ':valid' => $date,
                                    ':cat' => $tax->tax_cat_id,
                                    ':rate' => $tax->rate,
                                    ':id' => $tax->id));
    }

    static function createTax($tax) {
        $pdo = PDOBuilder::getPDO();
        $id = md5(time() . rand());
        $stmt = $pdo->prepare('INSERT INTO TAXES (ID, NAME, VALIDFROM, '
                              . 'CATEGORY, RATE) VALUES '
                              . '(:id, :name, :valid, :cat, :rate)');
        $date = strftime("%Y-%m-%d %H:%M:%S", $tax->start_date);
        $stmt->bindParam(':name', $id);
        $stmt->bindParam(':valid', $date);
        $stmt->bindParam(':cat', $tax->tax_cat_id);
        $stmt->bindParam(':rate', $tax->rate);
        $stmt->bindParam(':id', $id);
        if (!$stmt->execute()) {
        var_dump($stmt->errorInfo());
            return FALSE;
        } else {
            return $id;
        }
    }

    static function deleteTax($id) {
        $pdo = PDOBuilder::getPDO();
        $stmt = $pdo->prepare("DELETE FROM TAXES WHERE ID = :id");
        return $stmt->execute(array(':id' => $id));
    }
}

?>
