FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nginx
MAINTAINER sharkseven 'shark@dodora.cn'

ARG BUILD_DATE
ARG VCS_REF
ARG VERSION

LABEL org.label-schema.build-date=${BUILD_DATE} \
      org.label-schema.name="python2.7" \
      org.label-schema.description="A Python 2.7 image based on Debian Sid." \
      org.label-schema.url="https://github.com/LuisAlejandro/dockershelf" \
      org.label-schema.vcs-ref=${VCS_REF} \
      org.label-schema.vcs-url="https://github.com/LuisAlejandro/dockershelf" \
      org.label-schema.vendor="Luis Alejandro Martínez Faneyth" \
      org.label-schema.version=${VERSION} \
      org.label-schema.schema-version="1.0.0-rc.1"

ENV PY_VER_NUM="2.7" PY_DEBIAN_SUITE="stretch" PYTHONIOENCODING="UTF-8"

COPY *.sh /usr/share/dockershelf/python/
RUN bash /usr/share/dockershelf/python/build-images.sh

ENTRYPOINT service ssh start && /config/main && /bin/bash
