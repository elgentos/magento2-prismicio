FROM python:alpine

ARG UID=1000
ARG GID=1000
ARG USER=app

RUN apk add --no-cache git openssh-client
RUN pip install mkdocs mkdocs-material

RUN adduser -u ${UID} -g ${GID} -D ${USER}

USER ${USER}

ENTRYPOINT ["mkdocs"]

