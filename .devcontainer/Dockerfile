# See here for image contents: https://github.com/microsoft/vscode-dev-containers/tree/v0.203.0/containers/php/.devcontainer/base.Dockerfile

# [Choice] PHP version (use -bullseye variants on local arm64/Apple Silicon).
ARG VARIANT="8.1-apache-bullseye"
FROM mcr.microsoft.com/vscode/devcontainers/php:0-${VARIANT}

RUN apt-get update && \
    apt-get install -y zlib1g-dev libicu-dev g++ && \
    docker-php-ext-configure intl && \
    docker-php-ext-install intl

# [Optional] Uncomment this line to install additional OS packages.
# RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
#   && apt-get -y install --no-install-recommends icu-uc

# [Optional] Uncomment this line to install global node packages.
# RUN su vscode -c "source /usr/local/share/nvm/nvm.sh && npm install -g <your-package-here>" 2>&1
