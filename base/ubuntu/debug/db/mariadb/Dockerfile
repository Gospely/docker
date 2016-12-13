FROM mariadb:10
MAINTAINER sharkseven <shark@dodora.com>
ADD etc /etc
ENV DEBIAN_FRONTEND noninteractive
RUN apt-get update && \
	apt-get upgrade -y && \
	apt-get install -y apt-transport-https ca-certificates curl vim openssh-server build-essential wget git inotify-tools nano pwgen supervisor && \
	apt-get clean && \
	echo -n > /var/lib/apt/extended_states && \
	rm -rf /tmp/* /var/lib/apt/lists/* /var/tmp/*


# ssh密码登录
RUN sed -i 's/PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config
# 设置初始密码
RUN echo 'root:123456' | chpasswd

# 创建工作目录
RUN mkdir /root/workspace

# 创建配置 数据目录
RUN mkdir /config /data

# 添加core用户
RUN useradd -u 500 core


ADD config /config

RUN wget -O /config/dumb-init https://github.com/Yelp/dumb-init/releases/download/v1.1.3/dumb-init_1.1.3_amd64
RUN chmod +x /config/dumb-init /config/main
COPY start.sh /usr/local/bin/
RUN chmod 777 usr/local/bin/start.sh
RUN ln -s usr/local/bin/start.sh

EXPOSE 22
ENTRYPOINT /config/main
