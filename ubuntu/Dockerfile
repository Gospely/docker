FROM ubuntu:xenial
MAINTAINER Max Schaefer <max@excloo.com>
ADD etc /etc
ENV DEBIAN_FRONTEND noninteractive
RUN apt-get update && \
	apt-get upgrade -y && \
	apt-get install -y apt-transport-https ca-certificates curl vim openssh-server wget git inotify-tools nano pwgen supervisor && \
	apt-get clean && \
	echo -n > /var/lib/apt/extended_states && \
	rm -rf /tmp/* /var/lib/apt/lists/* /var/tmp/*

# ssh密码登录
RUN sed -i 's/PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config
# 设置初始密码
RUN echo 'root:123456' | chpasswd

# 创建工作目录
RUN mkdir /root/workspace

# 创建脚本目录
RUN mkdir /root/.gospely

# 创建git脚本目录
RUN mkdir /root/.gospely/.git_shell

# git clone git脚本
RUN git clone https://github.com/Gospely/git.git /root/.gospely/.git_shell

# clone ssh Terminal脚本
RUN git clone https://github.com/Gospely/terminal-socket.git /root/.gospely/.socket


RUN wget -qO- https://raw.githubusercontent.com/creationix/nvm/v0.32.0/install.sh | bash \
    && export NVM_DIR="$HOME/.nvm" \
    && [ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh" \
    && nvm install v4.6.0
RUN cd /root/.gospely/.socket && nvm use v4.6.0 && npm install

# 创建配置 数据目录
RUN mkdir /config /data

# 添加core用户
RUN useradd -u 500 core


ADD config /config

RUN wget -O /config/dumb-init https://github.com/Yelp/dumb-init/releases/download/v1.1.3/dumb-init_1.1.3_amd64
RUN chmod +x /config/dumb-init /config/main

CMD /config/main


EXPOSE 22

ENTRYPOINT service ssh start && cd ~/.gospely/.socket && wget -qO- https://raw.githubusercontent.com/creationix/nvm/v0.32.0/install.sh | bash \
		&& export NVM_DIR="$HOME/.nvm" \
		&& [ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh" \
		&& nvm install v4.6.0 && nvm use v6 &&  npm install && npm start && /bin/bash
