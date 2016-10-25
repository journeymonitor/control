#!/usr/bin/env bash

set -e # bail out if anything fails

apt-get update

apt-get -u -yy dist-upgrade

apt-get install -yy git puppet

cd /root/docker-assets/infra/puppet

FACTER_hostname=control FACTER_domain=container FACTER_puppet_root=./ \
  puppet apply --verbose --modulepath=./modules --hiera_config=./hiera.yaml manifests/site.pp

apt-get autoremove -yy --purge

apt-get clean
