#!/bin/sh
#
temp="/root/temp"
#这里的-d 参数判断$myPath是否存在
if [! -d "$temp"]; then
　　mv /root/temp/* /root/workspace
fi
