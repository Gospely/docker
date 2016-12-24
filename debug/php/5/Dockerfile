FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nginx

MAINTAINER sharkseven 'shark@dodora.cn'

RUN curl https://www.dotdeb.org/dotdeb.gpg | apt-key add - && \
	echo "deb http://packages.dotdeb.org/ jessie all" > /etc/apt/sources.list.d/dotdeb.list && \
	apt-get update && \
	apt-get install -y php5-cli php5-curl php5-fpm php5-mbstring php5-mcrypt php5-mysql php5-pgsql php5-sqlite3 && \
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
