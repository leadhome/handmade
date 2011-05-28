#!/bin/bash
echo 'You want dump your data now? (y/n):'
read answer
if [ "$answer" = "y" ]; then
	zf dump-data doctrine true
fi
zf drop-database doctrine
zf generate-models-from-yaml doctrine
zf create-database doctrine
zf create-tables doctrine
zf load-data doctrine
