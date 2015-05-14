<?php
if(getenv('DB_PORT_3306_TCP_ADDR')) {
    //indicates we are running in docker
    $container->setParameter('database_host', getenv('DB_PORT_3306_TCP_ADDR'));
}