#!/bin/sh
set -e

if [[ -z "IGNORE_HEALTHCHECK_FCGI_PING" ]]; then
    if env -i REQUEST_METHOD=GET SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping cgi-fcgi -bind -connect 127.0.0.1:9000; then
        exit 0
    else
        echo "PHP-FPM is not pingable"

        exit 1
    fi
fi

exit 0
