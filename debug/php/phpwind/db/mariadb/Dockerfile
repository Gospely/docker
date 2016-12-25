FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nginx-php:5
MAINTAINER sharkseven 'shark@dodora.cn'


ADD upload /root/temp
RUN apt-get update
RUN apt-get -q -y install mysql-server \
                          mysql-client \
                          telnet && \
                          apt-get -q -y clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN sed -i -e "s/^bind-address/#bind-address/" /etc/mysql/my.cnf && \
	sed -i -e "s/^datadir.*=.*/datadir = \/data/" /etc/mysql/my.cnf && \
	sed -i -e "s/^user.*=.*/user = core/" /etc/mysql/my.cnf && \
	sed -i -e "s/\/var\/log\/mysql/\/data\/log/" /etc/mysql/my.cnf && \
	chown -R core:adm /var/log/mysql.err && \
	chown -R core:adm /var/log/mysql.log && \
	chown -R core:adm /var/log/mysql && \
	chown -R core:root /run/mysqld
ADD config /config
ADD supervisord.conf /etc/supervisor/conf.d/mariadb.conf
EXPOSE 3306

EXPOSE 80

ENTRYPOINT service ssh start && /config/main && /bin/bash
