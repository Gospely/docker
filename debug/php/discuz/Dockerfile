FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nginx-php
MAINTAINER sharkseven 'shark@dodora.cn'
ADD etc /etc
ADD upload /root/temp

RUN chmod 777 /root/temp/templates
RUN chmod /root/temp/attachments
RUN chmod /root/temp/forumdata
RUN chmod /root/temp/forumdata/cache
RUN chmod /root/temp/forumdata/templates
RUN chmod /root/temp/forumdata/threadcaches
RUN chmod /root/temp/forumdata/logs
EXPOSE 22
EXPOSE 80

ENTRYPOINT service ssh start && /config/main && /bin/bash
