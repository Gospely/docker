FROM gospel-ubuntu

MAINTAINER sharkseven 'shark@dodora.cn'


EXPOSE 22


ENTRYPOINT service ssh start && cd /root/workspace  && node index.js && rm -rf .git && /bin/bash
