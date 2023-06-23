FROM spade/php8.1:v5

COPY php.ini /usr/local/etc/php/php.ini
COPY supervisor /etc/supervisor

COPY blogstar /app/blogstar
COPY env.php /app/env.php

EXPOSE 9001

CMD ["supervisord", "-n","-c", "/etc/supervisor/supervisord.conf"]