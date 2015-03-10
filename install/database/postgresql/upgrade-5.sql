CREATE TABLE PROVIDERS (
    ID VARCHAR NOT NULL,
    NAME VARCHAR NOT NULL,
    IMAGE BYTEA,
    ADDRESS VARCHAR,
    ADDRESS2 VARCHAR,
    POSTAL VARCHAR,
    CITY VARCHAR,
    REGION VARCHAR,
    COUNTRY VARCHAR,
    FIRSTNAME VARCHAR,
    LASTNAME VARCHAR,
    EMAIL VARCHAR,
    PHONE VARCHAR,
    PHONE2 VARCHAR,
    WEBSITE VARCHAR,
    FAX VARCHAR,
    NOTES VARCHAR,
    DISPORDER INTEGER DEFAULT NULL,
    VISIBLE BOOLEAN NOT NULL DEFAULT TRUE,
    PRIMARY KEY (ID)
);

ALTER TABLE PRODUCTS ADD COLUMN PROVIDER VARCHAR;
ALTER TABLE PRODUCTS ADD CONSTRAINT PRODUCTS_PROVIDER_FK FOREIGN KEY (PROVIDER) REFERENCES PROVIDERS(ID);