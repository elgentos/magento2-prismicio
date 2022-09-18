FROM python:alpine

ARG UID=1000
ARG GID=1000
ARG USER=app

RUN apk add --no-cache git openssh-client
RUN pip install mkdocs

#RUN addgroup -g 1000 ${USER} 
RUN adduser -u ${UID} -g ${GID} -D ${USER}

USER ${USER}

ENTRYPOINT ["mkdocs"]

