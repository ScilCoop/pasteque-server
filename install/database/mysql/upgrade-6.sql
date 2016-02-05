--    Pasteque is a point of sales software
--    Copyright (C) 2016 SARL SCOP Scil
--    http://github.com/ScilCoop
--
--    This file is part of Pasteque
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

-- db v6 - v7

-- final script

-- Orders

ALTER TABLE `ORDERLINES` CHANGE `ORDER` `ORDER_ID` VARCHAR(255);
ALTER TABLE `ORDERLINES` ADD CONSTRAINT ORDER_LINES_FK_1 FOREIGN KEY (ORDER_ID) REFERENCES ORDERS(ID);
