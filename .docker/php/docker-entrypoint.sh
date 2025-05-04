#!/bin/sh
set -e

uid=$(stat -c %u /var/www/html)
gid=$(stat -c %g /var/www/html)

sed -i -r "s/localUser:x:[0-9]+:[0-9]+:/localUser:x:$uid:$gid:/g" /etc/passwd
sed -i -r "s/localUser:x:[0-9]+:/localUser:x:$gid:/g" /etc/group
user=$(grep ":x:$uid:" /etc/passwd | cut -d: -f1)

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
    set -- apache2-foreground "$@"
fi

if [ "$1" = 'apache2-foreground' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then

    if [ "$APP_ENV" != 'prod' ]; then
        if [ -d .git ]; then
          git config --global --add safe.directory /var/www/html
        fi

        mkdir -p var/cache var/logs var/sessions;
    fi

    setfacl -R -m u:www-data:rwX -m u:localUser:rwX var
    setfacl -dR -m u:www-data:rwX -m u:localUser:rwX var
fi

if [ "$1" = 'bash' ] || [ "$1" = 'composer' ]; then
    exec gosu ${user} docker-php-entrypoint "$@"
else
    exec docker-php-entrypoint "$@"
fi
