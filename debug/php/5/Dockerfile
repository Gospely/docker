FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nginx

MAINTAINER sharkseven 'shark@dodora.cn'

ADD etc /etc

RUN echo "deb http://ppa.launchpad.net/nginx/$nginx/ubuntu $dist main" > /etc/apt/sources.list.d/nginx-$nginx-$dist.list
RUN apt-key adv --keyserver keyserver.ubuntu.com --recv-keys C300EE8C
RUN apt-get update
RUN apt-get install -y php5-fpm
RUN apt-get autoremove -y
RUN apt-get clean

ADD php5-fpm/php.ini /etc/php5/fpm/
ADD supervisord.conf /etc/supervisor/conf.d/php-fpm.conf

EXPOSE 22
EXPOSE 80


ENTRYPOINT service ssh start && /config/main && /bin/bash
