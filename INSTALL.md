# 安装说明

## 注意事项

- 复制根目录 `weixiaoqing-work.conf` 到 Nginx 配置目录前，请先修改文件中的路径为当前项目的实际部署路径，否则可能因路径错误导致安装出错。
- 修改完成后，再执行 Nginx 重载或后续安装步骤。
- 若部署在宝塔、Docker 或子目录中，请确保 `root`、`fastcgi_param SCRIPT_FILENAME` 等路径与实际环境一致。
