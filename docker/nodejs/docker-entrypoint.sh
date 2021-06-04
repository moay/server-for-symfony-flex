#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- node "$@"
fi

if [ "$1" = 'node' ] || [ "$1" = 'npm' ]; then
	npm install

	>&2 echo "Waiting for PHP to be ready..."
	until nc -z "127.0.0.1" "9000"; do
		sleep 1
	done
fi

exec "$@"
