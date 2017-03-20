FROM ubuntu:xenial
MAINTAINER sharkseven <shark@dodora.com>
ADD etc /etc
ENV DEBIAN_FRONTEND noninteractive
RUN apt-get update && \
	apt-get upgrade -y && \
	apt-get install -y apt-transport-https ca-certificates curl net-tools sudo vim openssh-server build-essential wget git inotify-tools nano pwgen supervisor && \
	apt-get clean && \
	echo -n > /var/lib/apt/extended_states && \
	rm -rf /tmp/* /var/lib/apt/lists/* /var/tmp/*


# ssh密码登录
RUN sed -i 's/PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config
# 设置初始密码
RUN echo 'root:123456' | chpasswd
ENV USERID=user
# 创建工作目录
RUN mkdir /root/workspace

# 创建脚本目录
RUN mkdir /root/.gospely

# 创建git脚本目录
RUN mkdir /root/.gospely/.git_shell

# 修改时区
RUN cp /usr/share/zoneinfo/Asia/Shanghai /etc/localtime

# git clone git脚本
RUN git clone https://github.com/Gospely/git.git /root/.gospely/.git_shell

# clone ssh Terminal脚本
RUN git clone https://github.com/Gospely/terminal-socket.git /root/.gospely/.socket

RUN wget -qO- https://raw.githubusercontent.com/creationix/nvm/v0.32.0/install.sh | bash \
    && export NVM_DIR="$HOME/.nvm" \
    && [ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh" \
    && nvm install v4.6.0


RUN bash -c '. /root/.nvm/nvm.sh && cd /root/.gospely/.socket && nvm use v4.6.0 && npm install -g cnpm --registry=https://registry.npm.taobao.org && cnpm install'

# 创建配置 数据目录
RUN mkdir /config /data

# 添加core用户
RUN useradd -u 500 core


ADD config /config

RUN wget -O /config/dumb-init https://github.com/Yelp/dumb-init/releases/download/v1.1.3/dumb-init_1.1.3_amd64
RUN chmod +x /config/dumb-init /config/main


EXPOSE 22
CMD /config/main
