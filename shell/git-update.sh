#!/bin/bash
#
#更新GIT到最新版本
#
echo "开始执行"
CMD_PATH=`dirname $0`
cd $CMD_PATH
echo "进入目录：$PWD"
cd ../

WEB_USER='nginx'
WEB_USERGROUP='nginx'

#
#未clone请先做一下clone
#

echo "拉取代码..."
git reset --hard HEAD
git clean -f
git pull

#处理数据库配置被覆盖
rm -rf ./application/database.php
cp -rf ./shell/default-database.php ./application/database.php
echo "修改目录用户权限..."

chmod -R 755 ./shell
chmod -R 755 ./runtime

chown $WEB_USER:$WEB_USERGROUP ./runtime
chown $WEB_USER:$WEB_USERGROUP ./service
chmod 555 ./service/

echo "完成！！！"
