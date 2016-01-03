#!/bin/bash
sqlCommand="sqlite3 raspap/.content/database.sqlite3"
tables=`$sqlCommand "SELECT name FROM sqlite_master WHERE type='table' AND name not like 'sqlite%';"`

for table in $tables
do
    echo "Truncating table $table"
    eval "$sqlCommand \"DELETE FROM $table\""
done

# cache
echo "========================="
echo "Clearing the cache"
sqlite3 raspap/.content/cache/applicationCache.sqlite3 "DELETE FROM pf2_simple_cache"
echo "Truncating table pf2_simple_cache"
