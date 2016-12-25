FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nginx-php:5
MAINTAINER sharkseven 'shark@dodora.cn'


#disuz version

ENV DZ_URL http://download.comsenz.com/DiscuzX/3.2/Discuz_X3.2_SC_UTF8.zip
ENV DZ_WWW_ROOT /root/workspace

RUN apt-get update
RUN apt-get install zip -y
ADD ${DZ_URL} /tmp/discuz.zip
RUN unzip /tmp/discuz.zip \
    && mv upload/* ${DZ_WWW_ROOT} \
    && cd ${DZ_WWW_ROOT} \
    && chmod a+w -R config data uc_server/data uc_client/data \
    && rm -rf /var/lib/apt/lists/*


EXPOSE 80

ENTRYPOINT service ssh start && /config/main && /bin/bash
