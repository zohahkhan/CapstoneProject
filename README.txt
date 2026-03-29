This is place holder for README file REVISED
phpadmin: https://db.ongkg.com/
Github webpages: https://github.ongkg.com/github/loginpages/index.php
test: https://test.ongkg.com/test/index.php
prod: https://capstone.ongkg.com/capstone/index.php

CREATE USER 'shannon'@'%' IDENTIFIED BY 'Shannon@2026!';
CREATE USER 'zoha'@'%' IDENTIFIED BY 'Zoha@2026!';
CREATE USER 'jj'@'%' IDENTIFIED BY 'Jj@2026!';
CREATE USER 'drmenon'@'%' IDENTIFIED BY 'Drmenon@2026!';

GRANT SELECT, INSERT, UPDATE, DELETE ON lajna_db.* TO 'shannon'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON lajna_db.* TO 'zoha'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE ON lajna_db.* TO 'jj'@'%';
GRANT SELECT ON lajna_db.* TO 'drmenon'@'%';

FLUSH PRIVILEGES;
