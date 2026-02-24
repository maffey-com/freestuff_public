FROM ubuntu:24.04
LABEL authors="chrismaffey"

ENV TZ="Pacific/Auckland"
ENV DEBIAN_FRONTEND=noninteractive


#RUN timedatectl set-timezone Pacific/Auckland
#RUN docker-php-ext-install mysqli
RUN apt-get update

RUN apt install php8.3-fpm  -y
RUN apt install php8.3  -y
RUN apt install libapache2-mod-php8.3 php8.3-mysql  -y
RUN apt install php8.3-xml php8.3-xmlrpc php8.3-curl php8.3-gd php8.3-imagick  -y
RUN apt install php8.3-cli php8.3-imap php8.3-mbstring -y
RUN apt install php8.3-opcache php8.3-soap php8.3-zip php8.3-bcmath unzip -y

RUN apt install apache2 -y
RUN apt install w3m -y
RUN apt install bash -y

#iinstall go for the mhsendmail application
# RUN apt install golang -y
#RUN go install github.com/mailhog/mhsendmail@latest
#RUN mv /root/go/bin/mhsendmail /usr/bin/mhsendmail
COPY docker/mhsendmail /usr/bin/mhsendmail

RUN apt install php-xdebug -y

RUN apt clean -y

RUN a2dismod php8.3
RUN a2dismod mpm_prefork
RUN a2enmod mpm_event proxy_fcgi setenvif
RUN a2enconf php8.3-fpm
RUN a2enmod ssl headers
RUN a2enmod rewrite
RUN a2enmod expires

#RUN sed -i 's/short_open_tag = Off/short_open_tag = On/' /etc/php/8.3/fpm/php.ini
#RUN sed -i 's/short_open_tag = Off/short_open_tag = On/' /etc/php/8.3/cli/php.ini


# Change the default shell to bash
SHELL ["/bin/bash", "-c"]


# Set working directory
WORKDIR /var/www/html



# Copy the startup script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# Run the startup script
CMD ["/start.sh"]

