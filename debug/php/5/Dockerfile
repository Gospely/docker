FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nginx

MAINTAINER sharkseven 'shark@dodora.cn'

RUN apt-get update && \
	apt-get install -y php5 php5-fpm php5-cli php5-dev php5-common php5-curl php5-mysql&& \
	apt-get clean && \
	echo -n > /var/lib/apt/extended_stat5es && \
	rm -rf /tmp/* /var/lib/apt/lists/* /var/tmp/*
RUN rm -rf /etc/php/5.0/fpm/pool.d && \
	mkdir -p /etc/php/5.0/fpm/pool.d

ADD etc /etc
ADD supervisord.conf /etc/supervisor/conf.d/php-fpm.conf

RUN git clone https://github.com/Gospely/hello_php.git /root/temp

EXPOSE 22
EXPOSE 80


ENTRYPOINT service ssh start && /config/main && /bin/bash
