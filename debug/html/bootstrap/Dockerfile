FROM registry.cn-hangzhou.aliyuncs.com/dodora/debug-nginx

RUN git clone https://github.com/Gospely/bootstrap-starter.git /root/temp
RUN bash -c "cd /root/temp && rm -rf .git"

ENTRYPOINT service ssh start && /config/main && /bin/bash
