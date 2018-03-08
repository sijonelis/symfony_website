FROM nginx:1.13


#Copy configuration files
COPY ./conf.d/*.conf /etc/nginx/conf.d/
COPY ./config/* /etc/nginx/

VOLUME ["/var/www/html"]
WORKDIR "/var/www/html"

EXPOSE 80
