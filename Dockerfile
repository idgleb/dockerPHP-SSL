FROM php:8-apache

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar el archivo index.php a la carpeta HTML
COPY index.php /var/www/html

# Establecer el modo no interactivo para evitar prompts
ENV DEBIAN_FRONTEND=noninteractive

# Actualizar los repositorios
RUN apt-get update

# Instalar el módulo SSL y OpenSSL
RUN apt-get install -y --no-install-recommends apache2 openssl

# Limpiar caché de apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Habilitar el módulo SSL de Apache
RUN a2enmod ssl

# Configurar el ServerName para evitar la advertencia
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Crear un certificado SSL autofirmado (para pruebas)
RUN mkdir -p /etc/ssl/private && \
    openssl req -new -newkey rsa:2048 -days 365 -nodes -x509 \
    -keyout /etc/ssl/private/apache-selfsigned.key \
    -out /etc/ssl/certs/apache-selfsigned.crt \
    -subj "/C=US/ST=State/L=City/O=Organization/OU=OrgUnit/CN=localhost" \
    -addext "basicConstraints=CA:FALSE" \
    -addext "subjectAltName=DNS:localhost"

# Modificar la configuración SSL de Apache para usar el certificado generado
RUN sed -i "s|SSLCertificateFile.*|SSLCertificateFile /etc/ssl/certs/apache-selfsigned.crt|g" /etc/apache2/sites-available/default-ssl.conf && \
    sed -i "s|SSLCertificateKeyFile.*|SSLCertificateKeyFile /etc/ssl/private/apache-selfsigned.key|g" /etc/apache2/sites-available/default-ssl.conf

# Habilitar el sitio SSL predeterminado
RUN a2ensite default-ssl

# Exponer los puertos 80 y 443
EXPOSE 80 443

# Iniciar Apache en primer plano
CMD ["apache2-foreground"]
