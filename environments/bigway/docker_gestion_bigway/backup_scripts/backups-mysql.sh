#!/bin/bash

in_array() {
    local haystack=${1}[@]
    local needle=${2}
    for i in ${!haystack}; do
        if [[ ${i} == ${needle} ]]; then
            return 1
        fi
    done
    return 0
}

# Local backup

USER='root'
PASSWORD='secret'
HOST='rex-data'
declare -a EXCLUDE=("information_schema" "mysql" "performance_schema" "test" "backups" "rex-backups")
BACKUPDIR="/home/backup/"

# Find all databases
#DATABASES=`mysql -u$USER -p$PASSWORD -h$HOST -e "SHOW DATABASES" | tr -d "| " | grep -v Database`
DATABASES=`mysql -u$USER -p$PASSWORD -h$HOST -e "SHOW DATABASES" | tr -d "| " | grep -v Database`
TODAY=$(date +%F)

for db in $DATABASES
do
    if in_array EXCLUDE $db; then
        DATE=`date '+%d%m%Y_%H%M%S'`
        INFO="Dump: $db - "$DATE
        INIT=`date '+%d-%m-%Y %H:%M:%S'`

        mkdir -p $BACKUPDIR$db

        LOG=/var/log/$db"_log.txt"

        if [ -f "$LOG" ]; then
            INITLINE= wc -l $LOG
        else
            INITLINE= 0
        fi
        DUMP=$BACKUPDIR$db/$db"_"$DATE.sql
        /var/www/html/yii backup-mysql/init-backup $db
        #mysqldump -u$USER -p$PASSWORD -h $HOST --force --opt --routines --add-drop-database --add-drop-table -c --create-options -e --max_allowed_packet=20M --skip-lock-tables $db |gzip -9 -c > $BACKUPD$
        mysqldump -u$USER -p$PASSWORD -h $HOST --force --opt --routines --add-drop-database --add-drop-table -c --create-options -e --max_allowed_packet=20M --skip-lock-tables $db  >  $DUMP 2> $LOG

        /var/www/html/yii backup-mysql/finish-backup $db $DUMP


        echo $INFO
    fi
done

# Delete old local backups

#find /home/backup/arya_afip/* -mtime +7 -exec rm {} \;
#find /home/backup/arya_agenda/* -mtime +7 -exec rm {} \;
#find /home/backup/arya_ecopago/* -mtime +7 -exec rm {} \;
#find /home/backup/arya_log/* -mtime +7 -exec rm {} \;
#find /home/backup/arya_notifications/* -mtime +7 -exec rm {} \;
#find /home/backup/arya_ticket/* -mtime +7 -exec rm {} \;
#find /home/backup/config/* -mtime +7 -exec rm {} \;
#find /home/backup/media/* -mtime +7 -exec rm {} \;