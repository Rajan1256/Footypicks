FROM nginx:latest

COPY config/app.conf /etc/nginx/conf.d/app.conf

RUN rm -rf /etc/nginx/conf.d/default
RUN rm -rf /etc/nginx/conf.d/default.conf

RUN usermod -u 1000 www-data