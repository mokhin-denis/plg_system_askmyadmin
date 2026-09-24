ARG JOOMLA_IMAGE=joomla:6.1.3-php8.3-apache
FROM ${JOOMLA_IMAGE}

# Keep the extension source inside the image so the official Joomla entrypoint
COPY . /tmp/askmyadmin
RUN tar --create --gzip --file /opt/askmyadmin.tar.gz --directory /tmp/askmyadmin .; rm -rf /tmp/askmyadmin
