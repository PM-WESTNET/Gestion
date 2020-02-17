#!/bin/sh

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
EXCLUDE= [ "information_schema" "mysql" "performance_schema" "test" ]
BACKUPDIR=$(/home/backup/)

# Find all databases
#DATABASES=`mysql -u$USER -p$PASSWORD -h$HOST -e "SHOW DATABASES" | tr -d "| " | grep -v Database`
DATABASES=`mysql -u$USER -p$PASSWORD -h$HOST -e "SHOW DATABASES" | tr -d "| " | grep -v Database`
TODAY=$(date +%F)

for db in $DATABASES
do
    if in_array EXCLUDE $db; then
        INFO="Dump: $db - "`date '+%d/%m/%Y %H:%M:%S'`
        INIT = `date '+%d-%m-%Y %H:%M:%S'`
            if [ ! -d "$BACKUPDIR$db/" ]; then
                mkdir $BACKUPDIR$db/
            fi
        LOG = /var/log/$db"_log.log"
        INITLINE= wc -l $LOG
        echo $db
        $(/var/www/html/yii backup/init-backup INIT)
        #mysqldump -u$USER -p$PASSWORD -h $HOST --force --opt --routines --add-drop-database --add-drop-table -c --create-options -e --max_allowed_packet=20M --skip-lock-tables $db |gzip -9 -c > $BACKUPD$
        mysqldump -u$USER -p$PASSWORD -h $HOST -force --opt --routines --add-drop-database --add-drop-table -c --create-options -e --max_allowed_packet=20M --skip-lock-tables $db |gzip -9 -c > $($BACKUPDIR$db/$db"_"`$db"_"`date +%Y%m%d_%H%M`.sql.gz)

        LASTLINE = wc -l $LOG

        if [$? -eq 0] then

         $(/var/www/html/yii backup/init-finish-success-backup $INIT)
        else
         $(/var/www/html/yii backup/init-finish-error-backup $INIT $LOG $INITLINE $LASTLINE)
        fi

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

