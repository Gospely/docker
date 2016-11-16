FROM node:4.4.4

MAINTAINER ivy 'xieyang@dodora.cn'

RUN apt-get update
RUN apt-get install git -y
RUN apt-get install vim -y
RUN apt-get install openssh-server -y

RUN sed -i 's/PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config

RUN mkdir /root/workspace

# init node project
RUN git clone https://github.com/Gospely/hello_node.git /root/workspace && cd /root/workspace && rm -rf .git

EXPOSE 22

ENTRYPOINT  service ssh start && cd ~/.gospely/.socket && npm start && /bin/bash
