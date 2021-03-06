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
        $sql = "SELECT * FROM TAXES WHERE CATEGORY = :cat";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":cat", $db_taxcat['ID']);
        $stmt->execute();
        while ($db_tax = $stmt->fetch()) {
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

    static function getByName($name) {
        $pdo = PDOBuilder::getPDO();
        $stmt = $pdo->prepare("SELECT * FROM TAXCATEGORIES WHERE NAME = :name");
        $stmt->bindParam(":name", $name, \PDO::PARAM_STR);
        if ($stmt->execute()) {
            if ($row = $stmt->fetch()) {
                $tax = TaxesService::buildDBTaxCat($row, $pdo);
                return $tax;
            }
        }
        return null;
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

    /** Update an existing category. Taxes must be set, if they have no id
     * they are added to the category, otherwise updated (no way to delete).
     */
    static function updateCat($cat) {
        if ($cat->getId() == null || count($cat->taxes) == 0) {
            return false;
        }
        $pdo = PDOBuilder::getPDO();
        $newTransaction = !$pdo->inTransaction();
        if ($newTransaction) {
            $pdo->beginTransaction();
        }
        $stmt = $pdo->prepare('UPDATE TAXCATEGORIES SET NAME = :name '
                              . 'WHERE ID = :id');
        if (!$stmt->execute(array(':name' => $cat->label, ':id' => $cat->id))) {
            if ($newTransaction) {
                $pdo->rollback();
            }
            return false;
        }
        foreach ($cat->taxes as $tax) {
            if ($tax->id !== null) {
                if (TaxesService::updateTax($tax) === false) {
                    if ($newTransaction) {
                        $pdo->rollback();
                    }
                    return false;
                }
            } else {
                $taxId = TaxesService::createTax($tax) === false;
                if ($taxId === false) {
                    if ($newTransaction) {
                        $pdo->rollback();
                    }
                    return false;
                }
                $tax->id = $taxId;
            }
        }
        if ($newTransaction) {
            $pdo->commit();
        }
        return $id;

    }

    /** Create a new tax category. It must have taxes defined. */
    static function createCat($cat) {
        if (count($cat->taxes) == 0) {
            return false;
        }
        $pdo = PDOBuilder::getPDO();
        $newTransaction = !$pdo->inTransaction();
        if ($newTransaction) {
            $pdo->beginTransaction();
        }
        $id = md5(time() . rand());
        $stmt = $pdo->prepare('INSERT INTO TAXCATEGORIES (ID, NAME) VALUES '
                . '(:id, :name)');
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $cat->label);
        if (!$stmt->execute()) {
            return false;
        } else {
            foreach ($cat->taxes as $tax) {
                $tax->taxCatId = $id;
                $taxId = TaxesService::createTax($tax);
                if ($taxId === false) {
                    if ($newTransaction) {
                        $pdo->rollback();
                    }
                    return false;
                }
                $tax->id = $taxId;
            }
            if ($newTransaction) {
                $pdo->commit();
            }
            return $id;
        }
    }

    static function deleteCat($id) {
        $pdo = PDOBuilder::getPDO();
        $newTransaction = !$pdo->inTransaction();
        if ($newTransaction) {
            $pdo->beginTransaction();
        }
        $stmtTax = $pdo->prepare("DELETE FROM TAXES WHERE CATEGORY = :id");
        $stmtTax->bindParam(':id', $id);
        if ($stmtTax->execute() === FALSE) {
            if ($newTransaction) {
                $pdo->rollback();
            }
            return FALSE;
        }
        $stmt = $pdo->prepare('DELETE FROM TAXCATEGORIES WHERE ID = :id');
        $stmt->bindParam(':id', $id);
        if ($stmt->execute() === FALSE) {
            if ($newTransaction) {
                $pdo->rollback();
            }
            return FALSE;
        }
        if ($newTransaction) {
            $pdo->commit();
        }
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

    private static function updateTax($tax) {
        if ($tax->id == null) {
            return false;
        }
        $pdo = PDOBuilder::getPDO();
        $stmt = $pdo->prepare('UPDATE TAXES SET NAME = :name, VALIDFROM = :valid, '
                              . 'CATEGORY = :cat, RATE = :rate '
                              . 'WHERE ID = :id');
        $date = strftime("%Y-%m-%d %H:%M:%S", $tax->startDate);
        return $stmt->execute(array(':name' => $tax->label,
                                    ':valid' => $date,
                                    ':cat' => $tax->taxCatId,
                                    ':rate' => $tax->rate,
                                    ':id' => $tax->id));
    }

    private static function createTax($tax) {
        $pdo = PDOBuilder::getPDO();
        $id = md5(time() . rand());
        $stmt = $pdo->prepare('INSERT INTO TAXES (ID, NAME, VALIDFROM, '
                              . 'CATEGORY, RATE) VALUES '
                              . '(:id, :name, :valid, :cat, :rate)');
        $date = strftime("%Y-%m-%d %H:%M:%S", $tax->startDate);
        $stmt->bindParam(':name', $tax->label);
        $stmt->bindParam(':valid', $date);
        $stmt->bindParam(':cat', $tax->taxCatId);
        $stmt->bindParam(':rate', $tax->rate);
        $stmt->bindParam(':id', $id);
        if (!$stmt->execute()) {
            return FALSE;
        } else {
            return $id;
        }
    }

    private static function deleteTax($id) {
        $pdo = PDOBuilder::getPDO();
        $stmt = $pdo->prepare("DELETE FROM TAXES WHERE ID = :id");
        return $stmt->execute(array(':id' => $id));
    }
}