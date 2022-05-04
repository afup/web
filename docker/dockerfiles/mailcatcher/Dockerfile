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
    && gem install -v $MAILCATCHER_VERSION mailcatcher --no-ri --no-rdoc \
    && apk del .build-deps

EXPOSE 1025 1080

CMD ["mailcatcher", "--foreground", "--ip=0.0.0.0", "--smtp-port=1025", "--http-port=1080"]
