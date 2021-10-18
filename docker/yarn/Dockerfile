FROM node:6-alpine

# Installing fixuid (https://github.com/boxboat/fixuid)
RUN USER=node && \
    GROUP=node && \
    apk add --no-cache --virtual .build-deps curl && \
    curl -SsL https://github.com/boxboat/fixuid/releases/download/v0.5/fixuid-0.5-linux-amd64.tar.gz | tar -C /usr/local/bin -xzf - && \
    apk del .build-deps && \
    chown root:root /usr/local/bin/fixuid && \
    chmod 4755 /usr/local/bin/fixuid && \
    mkdir -p /etc/fixuid && \
    printf "user: $USER\ngroup: $GROUP\n" > /etc/fixuid/config.yml

# Install python
RUN apk add --no-cache \
    python \
    make \
    g++

WORKDIR /app
ENTRYPOINT ["fixuid", "-q"]
CMD ["yarn", "dev"]