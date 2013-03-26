#!/bin/bash 


if [ $# -lt 3 ]; then
    echo "Usage: remoteupload.sh filename tpath ip"
    exit 1
fi

##本地文件目录
FILEPATH=$1

## 远程更新目录，如：/home/game/gameserver/ /home/xj_bk/server/ 等
REMOTEPATH=$2   

##目标ip
IP=$3

checkret()
{
  if [ $? -eq 0 ]; then
    echo "$@" ok
  else
    echo "$@" fail
    exit 1 
  fi  
}

if [ $IP ]; then
  export HOME='/root/'
  scp -r ./shell/gmshell $IP:/tmp/.
  checkret scp -r ./shell/gmshell $IP:/tmp/.

  scp $FILEPATH $IP:/tmp/gmshell/${FILEPATH##*/}
  checkret scp $FILEPATH $IP:/tmp/gmshell/${FILEPATH##*/}

  ssh $IP "/tmp/gmshell/copyfile.sh /tmp/gmshell/${FILEPATH##*/} $REMOTEPATH"
  checkret  ssh $IP "/tmp/gmshell/copyfile.sh /tmp/gmshell/${FILEPATH##*/} $REMOTEPATH"
else
  ./shell/gmshell/copyfile.sh $FILEPATH $REMOTEPATH
fi

exit 0
