服务器上部署teamserver:

docker run -d -e PASSWD=cspass -p 50050:50050 -p 3389:3389 xi4okv/cs:3.12   添加3.14版本的镜像

50050 CS客户端连接端口

再开放几个端口作为listener

cspass是客户端连接密码。
