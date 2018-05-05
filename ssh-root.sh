#!/usr/bin/expect  
set password xxxxxxpassworld
spawn ssh root@120.xx.xxx.95
expect "*password:*"
send "$password\r"
interact
expect eof
