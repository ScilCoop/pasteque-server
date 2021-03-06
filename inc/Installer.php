<?php
//    Pastèque Web back office
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

namespace Pasteque;

class Installer {
    const DB_NOT_INSTALLED = 0;
    const OK = 1;
    const NEED_DB_UPGRADE = 2;
    const NEED_DB_DOWNGRADE = 3;

    private static function loadFile($pdo, $fileContent) {
        $pdo->beginTransaction();
        $sqls = str_replace("\r\n", "\n", $fileContent);
        $sqls = explode(";\n", $sqls);
        foreach ($sqls as $sql) {
            $sql = trim($sql);
            if ($sql == "") {
                continue;
            }
            if ($pdo->query($sql) === false) {
                $pdo->rollback();
                return false;
            }
        }
        $pdo->commit();
        return true;
    }

    static function install($country) {
        $uid = get_user_id();
        $type = get_db_type($uid);
        $pdo = PDOBuilder::getPDO();
        $file = PT::$ABSPATH . "/install/database/" . $type . "/create.sql";
        if (!\file_exists($file)) {
            return false;
        }
        $fileContent = \file_get_contents($file);
        if (!Installer::loadFile($pdo, $fileContent)) {
            return false;
        }
        // Load country data
        if ($country !== null) {
            $cfile = PT::$ABSPATH . "/install/database/" . $type
                    . "/data_" . $country . ".sql";
            $fileContent = \file_get_contents($cfile);
            Installer::loadFile($pdo, $fileContent);
        }
        return true;
    }

    /** Upgrade database from given version to the latest. */
    static function upgrade($country, $version = null) {
        if ($version === null) {
            $version = Installer::getVersion();
            if ($version === null) {
                // Assume it's an old v4 with the old id
                $version = 4;
            }
        }
        while ($version != PT::DB_LEVEL) {
            $uid = get_user_id();
            $type = get_db_type($uid);
            $pdo = PDOBuilder::getPDO();
            // Load generic sql update for current version
            $file = PT::$ABSPATH . "/install/database/" . $type
                    . "/upgrade-" . $version . ".sql";
            $fileContent = \file_get_contents($file);
            if (!Installer::loadFile($pdo, $fileContent)) {
                return false;
            }
            // Check for localized update data for current version
            $file = PT::$ABSPATH . "/install/database/" . $type
                    . "upgrade-" . $version . "_" . $country . ".sql";
            if (\file_exists($file)) {
                $fileContent = \file_get_contents($file);
                if (!Installer::loadFile($pdo, $fileContent)) {
                    return false;
                }
            }
            $version++;
        }
    }

    static function getVersion($id = "pasteque") {
        $pdo = PDOBuilder::getPDO();
        $sql = "SELECT VERSION FROM APPLICATIONS WHERE ID = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $data = $stmt->fetch();
        if ($data !== false) {
            return (int) $data['VERSION'];
        } else {
            return null;
        }
    }

    static function checkVersion($dbVer = null) {
        if ($dbVer === null) {
            $dbVer = Installer::getVersion();
        }
        if ($dbVer === null) {
            // Search for an old lvl4 "postech"
            $dbVer = Installer::getVersion("postech");
        }
        if ($dbVer !== null) {
            if (intval($dbVer) < intval(PT::DB_LEVEL)) {
                return Installer::NEED_DB_UPGRADE;
            } else if (intval($dbVer) > intval(PT::DB_LEVEL)) {
                return Installer::NEED_DB_DOWNGRADE;
            } else {
                return Installer::OK;
            }
        } else {
            return Installer::DB_NOT_INSTALLED;
        }
    }
}
