#!/bin/sh
set -eu

mysql -uroot -p"${MYSQL_ROOT_PASSWORD}" <<SQL
CREATE DATABASE IF NOT EXISTS p_gar_test;
GRANT ALL PRIVILEGES ON p_gar_test.* TO '${MYSQL_USER}'@'%';
FLUSH PRIVILEGES;
SQL
