##安装
1. 用户Composer安装对应包
`composer update`

2. 导入数据库
`shell/db.sql`

3. 配置Nginx
`将shell/nginx.conf文件修改成你自己的配置`

4. 配置目录权限
	`chown nginx:nginx runtime`
	`chown nginx:nginx service`
	`chmod -R 755 runtime`
	`chmod 555 service`

##目录结构
| 目录        | 说明 |
| ------------- |:-----|
| 1. application		| 应用目录| 
| 2. extend				| 扩展目录| 
| 3. runtime			| 运行临时目录| 
| 4. service			| WEB运行目录| 
| 5. shell				| Shell脚本、初始化数据库文件、Nginx配置| 
| 6. vendor				| Composer包目录| 

##Console应用运行
1. 同步文件至阿里云OSS `php think file_oss`

##其它
1. 后台账号密码 `账号admin 密码111111`


