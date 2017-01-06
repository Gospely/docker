FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nginx

RUN git clone https://github.com/Gospely/html_hello.git /root/temp

ENTRYPOINT service ssh start && /config/main && /bin/bash
