cd java
@echo off
echo 请确保php和phpsocks5.properties中的配置正确，并且浏览器第一次访问服务器上的php文件显示Create tables successfully。
pause
echo 正在启动代理服务器，请确保本机有Java环境...如果没有错误提示，那么请设置程序代理服务器，类型为socks5，地址为127.0.0.1:10080。
echo on
java phpsocks5.PhpSocks5
@echo off
pause