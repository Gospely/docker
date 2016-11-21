FROM gospel-nginx-php

MAINTAINER sharkseven 'shark@dodora.cn'

RUN git clone https://github.com/Gospely/hello_php.git /root/workspace

EXPOSE 22
EXPOSE 80


ENTRYPOINT service ssh start  && /bin/bash
