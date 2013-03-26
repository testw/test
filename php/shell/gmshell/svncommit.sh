#!/bin/sh


##$1 根目录
##$2 注释
##$3 svn安装路径


if [ $# -lt 3 ]; then
    echo "Usage: svncommit./sh workpath comment svnpath"
    exit 1
fi

## 如果是utf8则需要转换
#M=`echo "$2"|iconv -f UTF-8 -t GBK`
WP=$1
M=$2
SVN=$3

cd $WP
export LANG=en_US
export LC_CTYPE=en_US

$SVN st|grep "?"|awk '{print $2}'|xargs $SVN add 

$SVN ci -m "$M" --encoding gbk --config-dir ../../.svn/cfg 

exit 0
