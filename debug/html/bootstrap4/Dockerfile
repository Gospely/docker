FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nginx

RUN git clone https://github.com/Gospely/bootstrap-4-starter.git /root/temp
RUN bash -c "cd /root/temp && rm -rf .git"

ENTRYPOINT service ssh start && /config/main && /bin/bash
