FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-ubuntu

# specify maintainer
MAINTAINER sharkseven <shark@dodora.cn>

ADD init /init
RUN sudo apt-get install -y language-pack-en-base
RUN sudo LC_ALL=en_US.UTF-8 add-apt-repository ppa:ondrej/php
RUN sudo add-apt-repository ppa:ondrej/php

作者：Vecarange
链接：https://www.zhihu.com/question/45999546/answer/100165171
来源：知乎
著作权归作者所有，转载请联系作者获得授权。
RUN sudo add-apt-repository ppa:ondrej/php
RUN apt-get update -y && \
	apt-get install -y \
	nginx \
	curl \
	nano \
	git \
	php5-fpm \
	php5-cli \
	php5-intl \
	php5-mcrypt \
	php5-apcu \
	php5-gd \
	php5-curl

RUN chmod +x /init/init.sh
RUN /init/init.sh

EXPOSE 80

# run nginx and php5-fpm on startup
RUN echo "/etc/init.d/php5-fpm start" >> /etc/bash.bashrc
RUN echo "/etc/init.d/nginx start" >> /etc/bash.bashrc
