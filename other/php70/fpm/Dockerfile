FROM debian:12.0

ENV TZ=Asia/Shanghai

RUN echo " " > /etc/apt/sources.list \
	&& echo "deb http://mirrors.tuna.tsinghua.edu.cn/debian/ bookworm main contrib non-free non-free-firmware" >>/etc/apt/sources.list \
	&& echo "deb http://mirrors.tuna.tsinghua.edu.cn/debian/ bookworm-updates main contrib non-free non-free-firmware" >>/etc/apt/sources.list \
	&& echo "deb http://mirrors.tuna.tsinghua.edu.cn/debian-security bookworm-security main contrib non-free non-free-firmware" >>/etc/apt/sources.list


RUN apt-get update \
    && apt-get -y install lsb-release ca-certificates curl \
    && curl -sSLo /usr/share/keyrings/deb.sury.org-php.gpg https://packages.sury.org/php/apt.gpg \
    && sh -c 'echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list' 
    

RUN apt-get update \
    && apt-get -y install php7.0-cli php7.0-bcmath php7.0-curl php7.0-dom php7.0-gd php7.0-mbstring php7.0-mysqli php7.0-sqlite3 php7.0-fpm php7.0-redis php7.0-amqp 

RUN apt-get -y install nginx supervisor \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

