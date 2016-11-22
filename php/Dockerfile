FROM gospel-nginx

MAINTAINER sharkseven 'shark@dodora.cn'

RUN curl https://www.dotdeb.org/dotdeb.gpg | apt-key add - && \
	echo "deb http://packages.dotdeb.org/ jessie all" > /etc/apt/sources.list.d/dotdeb.list && \
	apt-get update && \
	apt-get install -y php-cli php-curl php-fpm php-mbstring php-mcrypt php-mysql php-pgsql php-sqlite3 && \
	apt-get clean && \
	echo -n > /var/lib/apt/extended_states && \
	rm -rf /tmp/* /var/lib/apt/lists/* /var/tmp/*
RUN rm -rf /etc/php/7.0/fpm/pool.d && \
	mkdir -p /etc/php/7.0/fpm/pool.d

ADD etc /etc
ADD supervisord.conf /etc/supervisor/conf.d/php-fpm.conf

RUN git clone https://github.com/Gospely/hello_php.git /root/workspace

EXPOSE 22
EXPOSE 80


ENTRYPOINT service ssh start && /config/main && /bin/bash
