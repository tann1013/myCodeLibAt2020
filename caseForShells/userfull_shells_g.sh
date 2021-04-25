#!/bin/bash

function usage()
{
    echo "请进入要搜索的目录再使用."
    echo "usage:$0 'keyword'"
}
if [ $# -lt 1 ];then
    usage
    exit
fi
[ "$2" == "" ] && SEARCH_PATH='*'
[ "$3" != "" ] && SEARCH_PATH=$3
grep --exclude=*ttf --exclude=*gif --exclude=*jpg --exclude=*gdf --exclude-dir=MathJax --exclude-dir=PHPExcel --exclude-dir=phpQuery --exclude-dir=PHPQRCode --exclude-dir=node_modules --exclude-dir=data --exclude-dir=Zend --exclude-dir=min --exclude-dir=css --exclude-dir=test --color=always -nre "$1" $2 $SEARCH_PATH | grep -v \.svn | grep -v '\(lib\|\.min\)\.js' | grep -v custom\.css | grep -v main_style\.css | grep -v template_build | grep -v .*-min\.js
