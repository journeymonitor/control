#!/bin/bash -e

platform="linux"
if [[ "$OSTYPE" == "darwin"* ]]; then
    platform="mac"
fi


CURDIR="$(pwd)"
CIDDIR="/tmp"
DB_CID="$CIDDIR/.db.cid" # global lock

if [[ -f "CNAME" ]]; then
    hostname=$(head -n 1 CNAME | sed  's/ /_/g')
else 
    hostname="unknown"
fi

# not really production ready
DB_PASSWORD="root"

# used for phpnginx
XDEBUG_ENABLE=""

EchoCommand()
{
    echo -e "\e[32m$1\e[0m"
}

StartDb()
{
    EchoCommand "Starting DB container"

    # check if it is not already running
    if [[ ! -z $(docker ps -a | grep selenior_db) ]]; then
        # check if it is really running
        status=$(docker inspect -f "{{ .State.Running }}" $(docker ps -a | grep selenior_db | head -n 1 | awk '{print $1}') | tr -d '/' )

        if [[ "$status" == "true" ]]; then
            echo -e "\e[34mselenior_db\e[0m is already running, skipping"
            return 0
        fi
    fi

    # remove named container
    RemoveNamedContainer "selenior_db"

    sudo mkdir -p /opt/selenior-db
    # start named container
    docker run -d -p 3306:3306 \
        -e MYSQL_ROOT_PASSWORD=$DB_PASSWORD \
        -v "/opt/selenior-db":/var/lib/mysql \
        --cidfile=$DB_CID \
        --name selenior_db \
        selenior/db
}

StopDb()
{
    if [ -e "$DB_CID" ]
    then
        EchoCommand "Stopping DB container"
        docker stop $(cat $DB_CID)
        rm -f $DB_CID
    fi
}

CheckCnameFile()
{
    if [ -e ".hostname" ]
    then
        echo -e "Warning! \e[34m.hostname\e[0m file detected. Renaming it to \e[32mCNAME\e[0m\nPlease include it in your next commit!\n\n"
        mv .hostname CNAME
        git add CNAME
        git rm .hostname
    fi
}

StartPhpNginx()
{   
    CheckCnameFile

    EchoCommand "Starting php-fpm nginx container $hostname" 

    LOCAL_DYNAMODB=""

    if [[ ! -z $(docker ps -a | grep "$hostname") ]]; then
        # check if it is really running
        status=$(docker inspect -f "{{ .State.Running }}" $(docker ps -a | grep "$hostname" | head -n 1 | awk '{print $1}') | tr -d '/' )

        if [[ "$status" == "true" ]]; then
            echo -e "\e[34m$hostname\e[0m is already running, skipping"
            return 0
        else
            RemoveNamedContainer $hostname 
        fi
    fi

    if [[ ! -z $(docker ps -a | grep dynamodb) ]]; then
        status=$(docker inspect -f "{{ .State.Running }}" $(docker ps -a | grep "dynamodb" | head -n 1 | awk '{print $1}') | tr -d '/' )
        if [[ "$status" == "true" ]]; then
            LOCAL_DYNAMODB=" --link dynamodb:dynamodb -v /tmp/dynamodb:/var/dynamodb "
        fi
    fi

    docker run -d -P \
        --link selenior_db:db \
        -v $(pwd):/var/www \
        $XDEBUG_ENABLE \
        $LOCAL_DYNAMODB \
        --name="$hostname" \
        --cidfile="$CIDDIR/$hostname.cid" \
        selenior/frontend

    EchoCommand "updating /etc/hosts"
    SELENIOR_WEBAPP_CONTAINER_ID=$(docker ps | grep $hostname | cut -d" " -f 1)
    SELENIOR_WEBAPP_CONTAINER_IP=$(docker inspect $SELENIOR_WEBAPP_CONTAINER_ID | grep IPAddress | cut -d"\"" -f4)
    cat /etc/hosts | grep -v "$hostname" > /tmp/hosts
    echo "$SELENIOR_WEBAPP_CONTAINER_IP      $hostname" >> /tmp/hosts
    sudo cp /tmp/hosts /etc/hosts && rm /tmp/hosts
}

StopPhpNginx()
{
    CheckCnameFile

    cidfile="$CIDDIR/$hostname.cid"

    if [ -e "$cidfile" ]
    then
        EchoCommand "Stopping php-fpm nginx container"
        docker stop $(cat "$cidfile")
        rm -f "$cidfile"
    else
        EchoCommand "php-fpm nginx is not running?"
    fi
}

ShowStatus()
{
    CheckCnameFile

    if [ -e "$DB_CID" ]
    then
        echo -e "DB container active: \e[32m$(docker inspect -f '{{ .State.Running }}' $(cat $DB_CID))\e[0m"
    else
        echo -e "DB Container active: \e[32mfalse\e[0m"
    fi

    PHPNGINX_CID="$CIDDIR/$hostname.cid"
    
    if [ -e "$PHPNGINX_CID" ]
    then
        echo -e "PHP nginx container active: \e[32m$(docker inspect -f '{{ .State.Running }}' $(cat $PHPNGINX_CID))\e[0m"
    else
        echo -e "PHP nginx container active: \e[32mfalse\e[0m"
    fi
}

RemoveNamedContainer()
{
    if [[ -n $(docker ps -a | grep "$1") ]]; then
        docker rm -f $2 $1
    fi
}

InitializeMysql()
{
    EchoCommand "Creating database $1 for user $2 indentified by $3"

    sql="CREATE DATABASE IF NOT EXISTS $1; "
    sql+="CREATE USER '$2'@'%' IDENTIFIED BY '$3'; "
    sql+="GRANT ALL ON $1.* TO '$2'@'%'; "
     
    docker run -it \
        -e TMP_SQL="$sql" \
        --link selenior_db:mysql \
        --rm selenior/db \
        sh -c 'exec mysql -h"$MYSQL_PORT_3306_TCP_ADDR" -P"$MYSQL_PORT_3306_TCP_PORT" -uroot -p"$MYSQL_ENV_MYSQL_ROOT_PASSWORD" -Bse "$TMP_SQL"'
}

MysqlConsole()
{
    docker run -it \
        --link selenior_db:mysql \
        --rm selenior/db \
        sh -c 'exec mysql -h"$MYSQL_PORT_3306_TCP_ADDR" -P"$MYSQL_PORT_3306_TCP_PORT" -uroot -p"$MYSQL_ENV_MYSQL_ROOT_PASSWORD"'
}

CleanupDocker()
{
    docker ps -a | grep "Exit" | awk '{print $1}' | while read -r id ; do
        docker rm  $id
    done

    docker images | grep "^<none>" | awk 'BEGIN { FS = "[ \t]+" } { print $3 }'  | while read -r id ; do
        docker rmi $id
    done
}

ShowHelp()
{
    echo -e "\nSyntax: \e[96m$0 [-xdebug] command\e[0m\n" \
            "\nAvailable switches: \n" \
            "\t\e[95m-xdebug\e[0m\t\t Will start php5-fpm with xdebug support \n" \
            "\nAvailable commands: \n" \
            "\t\e[93mhelp\e[0m\t\t Show this help\n" \
            "\t\e[93mstart\e[0m\t\t Start database and php-nginx containers\n" \
            "\t\e[93mstart-db\e[0m\t Start database container\n" \
            "\t\e[93mstart-nginx\e[0m\t Start php-nginx container\n" \
            "\t\e[93mstop\e[0m\t\t Stop DB and nginx containers (in current project)\n" \
            "\t\e[93mstop-db\e[0m\t\t Stop DB container\n" \
            "\t\e[93mstop-nginx\e[0m\t Stop nginx container (in curent project)\n" \
            "\t\e[93mrestart\e[0m\t\t Restart containers\n" \
            "\t\e[93mstatus\e[0m\t\t Check container status\n" \
            "\t\e[93mnginx-reverse\e[0m\t Start nginx-reverse-proxy\n" \
            "\t\e[93minit-db\e[0m\t\t Ensure database exists and given user is granted access\n\t\t\t arguments: \e[92mDB_NAME USER PASS\e[0m\n" \
            "\t\e[93mmysql-console\e[0m\t Login into mysql console\n" \
            "\t\e[93mcleanup\e[0m\t\t Remove all docker 'Exited' containers (not needed)\n" \
            "\n" 
    exit 0;
}

if [[ $# -eq 0 ]]
then
    ShowHelp
fi

if [[ "$1" = "-xdebug" ]]
then
    XDEBUG_ENABLE="-e ENABLE_XDEBUG=true"
    CMD=$2
else
    CMD=$1
fi

case $CMD in
    "start")
    StartDb && StartPhpNginx
    ;;

    "start-db")
    StartDb
    ;;

    "start-nginx")
    StartPhpNginx
    ;;

    "stop")
    StopPhpNginx
    StopDb
    ;;

    "stop-nginx")
    StopPhpNginx
    ;;

    "stop-db")
    StopDb
    ;;

    "restart")
    StopPhpNginx
    StopDb
    StartDb
    StartPhpNginx
    ;;

    "status")
    ShowStatus
    ;;

    "init-db")
    InitializeMysql $2 $3 $4
    ;;

    "mysql-console")
    MysqlConsole
    ;;

    "cleanup")
    CleanupDocker
    ;;

    "help" | *)
    ShowHelp
    ;;

esac

