FROM httpd:2.4-alpine

RUN sed -i \
        -e 's/^#\(Include .*httpd-vhosts.conf\)/\1/' \
        #-e 's/^#\(Include .*httpd-ssl.conf\)/\1/' \
        -e '/LoadModule rewrite_module/s/^#//g' \
        -e '/LoadModule ssl_module/s/^#//g' \
        conf/httpd.conf

COPY httpd-vhosts.conf /usr/local/apache2/conf/extra/httpd-vhosts.conf

COPY apache.crt /usr/local/apache2/conf/apache.crt
COPY apache.key /usr/local/apache2/conf/apache.key
