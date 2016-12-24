FROM php:5.6-fpm
MAINTAINER sharkseven 'shark@dodora.cn'

ADD etc /etc
ENV DEBIAN_FRONTEND noninteractive
RUN apt-get update && \
	apt-get upgrade -y && \
	apt-get install -y unzip php5-mysql apt-transport-https ca-certificates curl sudo vim openssh-server build-essential wget git inotify-tools nano pwgen supervisor && \
	apt-get clean && \
	echo -n > /var/lib/apt/extended_states && \
	rm -rf /tmp/* /var/lib/apt/lists/* /var/tmp/*

#disuz version

ENV DZ_URL http://download.comsenz.com/DiscuzX/3.2/Discuz_X3.2_SC_UTF8.zip
ENV DZ_WWW_ROOT /var/www/html

ADD ${DZ_URL} /tmp/discuz.zip
RUN unzip /tmp/discuz.zip \
    && mv upload/* ${DZ_WWW_ROOT} \
    && cd ${DZ_WWW_ROOT} \
    && chmod a+w -R config data uc_server/data uc_client/data \
    && rm -rf /var/lib/apt/lists/*

# clone ssh Terminal脚本
RUN git clone https://github.com/Gospely/terminal-socket.git /root/.gospely/.socket

RUN wget -qO- https://raw.githubusercontent.com/creationix/nvm/v0.32.0/install.sh | bash \
    && export NVM_DIR="$HOME/.nvm" \
    && [ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh" \
    && nvm install v4.6.0


RUN bash -c '. /root/.nvm/nvm.sh && cd /root/.gospely/.socket && nvm use v4.6.0 && npm install -g cnpm --registry=https://registry.npm.taobao.org && cnpm install'

# 创建配置 数据目录
RUN mkdir /config /data

ADD config /config

# 添加core用户
RUN useradd -u 500 core

RUN wget -O /config/dumb-init https://github.com/Yelp/dumb-init/releases/download/v1.1.3/dumb-init_1.1.3_amd64
RUN chmod +x /config/dumb-init /config/main


ENV DBUSER=docker
ENV DBPASS=docker

RUN apt-key adv --keyserver keyserver.ubuntu.com --recv-keys CBCB082A1BB943DB && \
	echo "deb http://ftp.osuosl.org/pub/mariadb/repo/10.1/debian/ jessie main" > /etc/apt/sources.list.d/mariadb.list && \
	apt-get update && \
	apt-get install -y mariadb-server && \
	apt-get clean && \
	echo -n > /var/lib/apt/extended_states && \
	rm -rf /tmp/* /var/lib/apt/lists/* /var/tmp/*
RUN sed -i -e "s/^bind-address/#bind-address/" /etc/mysql/my.cnf && \
	sed -i -e "s/^datadir.*=.*/datadir = \/data/" /etc/mysql/my.cnf && \
	sed -i -e "s/^user.*=.*/user = core/" /etc/mysql/my.cnf && \
	sed -i -e "s/\/var\/log\/mysql/\/data\/log/" /etc/mysql/my.cnf && \
	chown -R core:adm /var/log/mysql.err && \
	chown -R core:adm /var/log/mysql.log && \
	chown -R core:adm /var/log/mysql && \
	chown -R core:root /run/mysqld


EXPOSE 3306
EXPOSE 22
EXPOSE 80

ENTRYPOINT service ssh start && /config/main && /bin/bash
