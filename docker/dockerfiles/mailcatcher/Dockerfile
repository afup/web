FROM alpine:3.9

RUN apk add --no-cache ca-certificates openssl

RUN apk add --no-cache \
        ruby \
        ruby-bigdecimal \
        ruby-etc \
        ruby-json \
        libstdc++ \
        sqlite-libs

ARG MAILCATCHER_VERSION=0.7.1

RUN apk add --no-cache --virtual .build-deps \
        ruby-dev \
        make g++ \
        sqlite-dev \
    && gem install -v 0.1.2 net-protocol --no-ri --no-rdoc \
    && gem install -v 0.3.0 net-smtp --no-ri --no-rdoc \
    && gem install -v 0.2.2 net-imap --no-ri --no-rdoc \
    && gem install -v $MAILCATCHER_VERSION mailcatcher --no-ri --no-rdoc \
    && apk del .build-deps

EXPOSE 1025 1080

CMD ["mailcatcher", "--foreground", "--ip=0.0.0.0", "--smtp-port=1025", "--http-port=1080"]
