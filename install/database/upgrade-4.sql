--    POS-tech is a point of sales software
--    Copyright (C) 2012 SARL SCOP Scil
--    http://trac.scil.coop/pos-tech
--
--    This file is part of POS-Tech
--
--    POS-tech is free software: you can redistribute it and/or modify
--    it under the terms of the GNU General Public License as published by
--    the Free Software Foundation, either version 3 of the License, or
--    (at your option) any later version.
--
--    POS-tech is distributed in the hope that it will be useful,
--    but WITHOUT ANY WARRANTY; without even the implied warranty of
--    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
--    GNU General Public License for more details.
--
--    You should have received a copy of the GNU General Public License
--    along with POS-tech. If not, see <http://www.gnu.org/licenses/>.

-- Database upgrade script for MYSQL

-- db v4 - v5

-- final script
CREATE TABLE POS (
	ID INT(11) NOT NULL AUTO_INCREMENT,
	NAME VARCHAR(255) NOT NULL,
	PRIMARY KEY (ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE POSPARENTS (
    POS_ID INT(11) NOT NULL,
    PARENT_ID INT(11) NOT NULL,
    PRIMARY KEY (POS_ID, PARENT_ID),
    CONSTRAINT POS_FK FOREIGN KEY (POS_ID) REFERENCES POS(ID) ON DELETE CASCADE,
    CONSTRAINT PARENT_FK FOREIGN KEY (PARENT_ID) REFERENCES POS(ID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE CASHREGISTERS (
    ID INT(11) NOT NULL AUTO_INCREMENT,
    NAME VARCHAR(255) NOT NULL,
    LOCATION_ID VARCHAR(255) NOT NULL,
    POS_ID INT(11) NOT NULL,
    PRIMARY KEY (ID),
    CONSTRAINT CASHREGISTER_FK_LOCATION FOREIGN KEY (LOCATION_ID) REFERENCES LOCATIONS(ID),
    CONSTRAINT CASHREGISTER_FK_POS FOREIGN KEY (POS_ID) REFERENCES POS(ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE PRODUCTS_EXCLUDE (
    PRODUCT_ID VARCHAR(255),
    POS_ID INT(11),
    PRIMARY KEY (PRODUCT_ID, POS_ID),
    CONSTRAINT PRODUCTS_EXCLUDE_FK_PRODUCT FOREIGN KEY (PRODUCT_ID) REFERENCES PRODUCTS(ID) ON DELETE CASCADE,
    CONSTRAINT PRODUCTS_EXCLUDE_FK_POS FOREIGN KEY (POS_ID) REFERENCES POS(ID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE PRODUCTS_CAT ADD COLUMN POS_ID INT(11) NOT NULL DEFAULT 1 AFTER PRODUCT;
ALTER TABLE PRODUCTS_CAT ADD CONSTRAINT PRODUCT_CAT_FK_POS FOREIGN KEY (POS_ID) REFERENCES POS(ID) ON DELETE CASCADE;
ALTER TABLE PRODUCTS_CAT ALTER COLUMN POS_ID DROP DEFAULT;

UPDATE SET VERSION = 5 WHERE ID = "pasteque";
