FROM php:7.4.30-fpm

# 备份镜像源
RUN cp /etc/apt/sources.list /etc/apt/sources.list.bak

# 更换镜像源
RUN sed -i 's/deb.debian.org/mirrors.aliyun.com/g' /etc/apt/sources.list
RUN sed -i 's/security.debian.org/mirrors.aliyun.com/g' /etc/apt/sources.list

# 更新系统
RUN apt-get clean && apt-get update && apt-get -y upgrade

# 安装进程管理
RUN apt-get install -y supervisor zlib1g-dev libzip-dev wget zip unzip mcrypt libpng-dev libonig-dev libmcrypt-dev

# 官方扩展
RUN docker-php-ext-install zip bcmath gd mysqli pdo_mysql sockets mbstring
# redis
RUN pecl install redis && echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini
# mcrypt
RUN pecl install mcrypt && echo "extension=mcrypt.so" > /usr/local/etc/php/conf.d/mcrypt.ini
#
RUN pecl install mongodb && echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini

# 复制SwooleCli
COPY ./swoole-cli-v4.8.10-linux-x64.tar.xz /root/

# 安装Swoole
RUN cd /root && \
    xz -d swoole-cli-v4.8.10-linux-x64.tar.xz && \
    tar xvf swoole-cli-v4.8.10-linux-x64.tar && \
    mv swoole-cli /usr/local/bin/swoole-cli && \
    rm -fR LICENSE swoole-cli-v4.8.10-linux-x64.tar

# 复制代理脚本文件
COPY ./proxy.php /root/

# 复制启动脚本
COPY ./start.sh /root/

# 设置PHP-FPM 脚本运行目录
ENV SWOOLE_PROXY_DOCUMENT_ROOT="/var/www/html"

# 代理对外暴露端口
ENV SWOOLE_PROXY_PORT=8080

EXPOSE 8080

# 复制配置文件
COPY ./supervisord.conf /etc/supervisor/supervisord.conf
COPY ./server.conf /etc/supervisor/conf.d/server.conf
RUN mkdir /etc/php-fpm
COPY ./php-fpm.conf /etc/php-fpm/php-fpm.conf

ENTRYPOINT ["/usr/bin/supervisord","-c","/etc/supervisor/supervisord.conf"]