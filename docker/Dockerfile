FROM ubuntu:14.04

USER root

WORKDIR /root

RUN mkdir -p /root/docker-assets && mkdir -p /opt/journeymonitor/control

COPY docker/assets /root/docker-assets

COPY ./ /opt/journeymonitor/control

RUN /bin/bash /root/docker-assets/install-container-image.sh

CMD /bin/bash /root/docker-assets/boot-application.sh
