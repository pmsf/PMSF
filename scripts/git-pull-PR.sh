#!/bin/sh

PMSF_DIR=`dirname "$(readlink -f "$0/..")"`
cd $PMSF_DIR

case $1 in
	''|*[!0-9]*) echo "You must specify a PR number to apply a patch" ;;
        *) curl -s https://patch-diff.githubusercontent.com/raw/pmsf/pmsf/pull/${1}.diff | git apply --exclude=*.png -v ${2} ;;
esac
