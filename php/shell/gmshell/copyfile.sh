#!/bin/bash


checkret()
{
  if [ $? -eq 0 ]; then
    echo "$@" ok
  else
    echo "$@" fail
    exit 1 
  fi
}



if [ $# -lt 2 ]; then
    echo "Usage: copyfile filepath tpath"
    exit 1
fi


if [ ! -f $1 ]; then
   echo "no such file or dictionary($1)"
   exit 1
fi

if [ ! -d $2 ]; then
  mkdir -p $2
  checkret mkdir:$2
fi

cp $1 $2
checkret copyfile:$2${1##*/}

if [ "zip" == ${1##*.} ]; then
  cd $2
  FILENAME=`basename $1` 
  unzip -o $FILENAME
  checkret unzip $FILENAME

  rm -f $FILENAME
  checkret rm $FILENAME
fi 

exit 0
