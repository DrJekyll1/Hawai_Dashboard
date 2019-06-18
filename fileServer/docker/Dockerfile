FROM php:7.1-apache

RUN apt-get update -y
RUN apt-get upgrade -y

RUN apt-get install -y \
        adduser \
        ca-certificates \
        unzip \
        curl \
        git \
        wget \
        openssl

RUN docker-php-ext-install pdo_mysql

# Installiere benötigte PHP Erweiterungen
RUN apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
    && docker-php-ext-install -j$(nproc) iconv mcrypt \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

# Install v
RUN docker-php-ext-install bcmath pcntl

# strings
RUN docker-php-ext-install \
	gettext \
	mbstring

# Install Phalcon-PHP
WORKDIR /phalcon/cphalcon

# https://github.com/phalcon/cphalcon/releases
ENV PHALCON_VERSION "v3.2.1"
RUN curl -L -o "phalcon-${PHALCON_VERSION}.tar.gz" "https://api.github.com/repos/phalcon/cphalcon/tarball/${PHALCON_VERSION}" \
    && tar -xvf "phalcon-${PHALCON_VERSION}.tar.gz" --strip 1 \
    && rm "phalcon-${PHALCON_VERSION}.tar.gz" \
    && cd /phalcon/cphalcon/build \
    && ./install

# Install Phalcon Devtools
WORKDIR /usr/local/lib/phalcon-devtools

# https://github.com/phalcon/phalcon-devtools/releases
ENV PHALCON_DEVTOOLS_VERSION "v3.2.0"
RUN curl -L -o "phalcon-devtools-${PHALCON_DEVTOOLS_VERSION}.tar.gz" "https://api.github.com/repos/phalcon/phalcon-devtools/tarball/${PHALCON_DEVTOOLS_VERSION}" \
    && tar -xvf "phalcon-devtools-${PHALCON_DEVTOOLS_VERSION}.tar.gz" --strip 1 \
    && rm "phalcon-devtools-${PHALCON_DEVTOOLS_VERSION}.tar.gz" \
    && ln -s /usr/local/lib/phalcon-devtools/phalcon.php /usr/bin/phalcon \
    && chmod +x /usr/bin/phalcon

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN chmod +x /usr/local/bin/composer

# Install Dockerize
ENV DOCKERIZE_VERSION v0.5.0
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz



WORKDIR /usr/src/fileserver

COPY config/apache/apache2.conf /etc/apache2/apache2.conf
COPY config/apache/envvars /etc/apache2/envvars
COPY config/apache/site-default.conf /etc/apache2/sites-available/000-default.conf
COPY config/apache/project.conf /etc/apache2/project.conf
COPY config/php.ini /usr/local/etc/php/

COPY run.sh /run.sh
COPY start_safe_perms.sh /start_safe_perms.sh
RUN chmod 755 /*.sh

RUN a2enmod rewrite
RUN a2enmod headers
CMD ["/run.sh"]
#EXPOSE 80
#EXPOSE 5001

VOLUME ["/usr/src/fileserver"]

CMD ["/run.sh"]