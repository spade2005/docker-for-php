FROM spade/php8.1:v5

COPY php.ini /usr/local/etc/php/php.ini
COPY supervisor /etc/supervisor

COPY blogstar /app/blogstar
COPY env.php /app/env.php

EXPOSE 9001

CMD ["supervisord", "-n","-c", "/etc/supervisor/supervisord.conf"]


--

FROM spade/php8.1-fpm:v1

COPY php.ini /usr/local/etc/php/php.ini
COPY fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY nginx.conf /etc/nginx/conf.d/app.conf
RUN rm /etc/nginx/sites-enabled/default


COPY supervisor /etc/supervisor

COPY blogstar /app/blogstar
COPY env.php /app/env.php

EXPOSE 80 443

CMD ["supervisord", "-n","-c", "/etc/supervisor/supervisord.conf"]

