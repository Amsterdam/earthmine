FROM nginx
MAINTAINER datapunt.ois@amsterdam.nl

EXPOSE 80


RUN apt-get update \
	&& apt-get clean \
    && apt-get install -y nginx php5-fpm php5-mcrypt php5-intl php5-curl vim \
	&& rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
	&& usermod -a -G www-data nginx \
    && mkdir /app


COPY /src/* /app/
COPY /docker-entrypoint.sh /app/docker-entrypoint.sh
COPY /conf/default.conf /etc/nginx/conf.d/default.conf

CMD /app/docker-entrypoint.sh
