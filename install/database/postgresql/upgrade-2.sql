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

-- Database upgrade script for POSTGRESQL

-- db v2 - v3

-- final script

ALTER TABLE PRODUCTS ADD COLUMN DISCOUNTENABLED BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE PRODUCTS ADD COLUMN DISCOUNTRATE DOUBLE PRECISION NOT NULL DEFAULT 0.0;
ALTER TABLE TICKETS ADD COLUMN CUSTCOUNT INTEGER DEFAULT NULL;

UPDATE APPLICATIONS SET VERSION = 3 WHERE ID = 'postech';
