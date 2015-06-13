FROM ubuntu:14.04
 
MAINTAINER JourneyMonitor "info@journeymonitor.com"

# Disable user prompts
ENV DEBIAN_FRONTEND noninteractive
 
ENV TIMEZONE Europe/Berlin
 
RUN locale-gen en_US en_US.UTF-8
ENV LANG en_US.UTF-8

RUN apt-get update && apt-get install -y --force-yes \
	software-properties-common \
	ntpdate \
	supervisor

# Install requirement for synchronization with time server
RUN cp /usr/share/zoneinfo/$TIMEZONE /etc/localtime

# Declare timezone
RUN echo "TZ='$TIMEZONE'; export TZ" >> ~/.profile

