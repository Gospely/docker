FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nginx
MAINTAINER sharkseven 'shark@dodora.cn'

RUN apt-get update
RUN apt-get install -y zlib-devel bzip2-devel openssl-devel ncurses-devel

RUN bash -c "cd /tmp && wget  https://www.python.org/ftp/python/3.5.0/Python-3.5.0.tar.xz && tar jxvf  Python-3.5.0.tar.xz && cd Python-3.5.0 && ./configure --prefix=/usr/local/python3 && make && make install"

RUN bash /usr/share/dockershelf/python/build-images.sh
RUN ln -s /usr/local/python3/bin/python3.5 /usr/local/bin/python3

RUN ln -s /usr/local/python3/bin/pip3.5 /usr/local/bin/pip

RUN pip install --upgrade pip

ENTRYPOINT service ssh start && /config/main && /bin/bash
