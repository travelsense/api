#!/bin/bash
#
# Table dumper
#
# Usage: bin/dump.table <database> <table>
#

pg_dump \
    --host localhost \
    --username postgres \
    --format plain \
    --data-only \
    --column-inserts \
    --table $2 $1 \
    | grep "^INSERT" | sort
