#!/bin/bash
# ibon update logs
PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin
export PATH

# FTP username & password
#host1='ftp.yccse.com:21000'
#id1='addwe'
#pw1='22787789'

#host='sftp://mars.pic.net.tw'
#host='mars.pic.net.tw'
#id='vdc01005'
#pw='s91kgirz'

#host='sftp://61.57.227.199:990'
host='ftps://61.57.227.199'
id='vdc01010'
pw='bfhueo9d'

# Working directory
basedir='/home/htdocs/data/ftp'

# Get DATE Files
date1=$(date --date='1 days ago' +%Y%m%d)	# 1 Day ago
#date1=$(date +%Y%m%d)
if [ "$1" != "" ] ; then
	date1=$1
fi
#date1='20090527'
file1='7055889301.'$date1
file2='7055889301.'$date1'.FILEOK'
file3='7055889301'$date1'VD.ADACT'

cd $basedir'/../../iboncard'
/usr/local/php/bin/php -q ./ibon_ftp_client.auto.php

# Create OK File
cd $basedir
touch $file2
touch $file3
zip -q $file1'.zip' $file3
mv $file1'.zip' $file1

# Start FTP working
#mput $file1 $file2
#lftp $host1 > /var/log/ibon_ftp.log 2>&1 > /dev/null <<EOC
#user $id1 $pw1
#put $file1
#put $file2
#bye
#EOC

# ssh ftp
#sftp -oPort=15678 root@210.59.147.185 2>&1 <<EOC
/usr/local/lftp/bin/lftp $host 2>&1 <<EOC
user $id $pw
put $file1
put $file2
bye
EOC

