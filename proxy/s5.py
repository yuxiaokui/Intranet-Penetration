import socket, sys, select, SocketServer, struct, time  
  
class ThreadingTCPServer(SocketServer.ThreadingMixIn, SocketServer.TCPServer): pass  
class Socks5Server(SocketServer.StreamRequestHandler):  
    def handle_tcp(self, sock, remote):  
        fdset = [sock, remote]  
        while True:  
            r, w, e = select.select(fdset, [], [])  
            if sock in r:  
                if remote.send(sock.recv(4096)) <= 0: break  
            if remote in r:  
                if sock.send(remote.recv(4096)) <= 0: break  
    def handle(self):  
        try:  
            print 'socks connection from ', self.client_address  
            sock = self.connection  
            # 1. Version  
            sock.recv(262)  
            sock.send(b"\x05\x00");  
            # 2. Request  
            data = self.rfile.read(4)  
            mode = ord(data[1])  
            addrtype = ord(data[3])  
            if addrtype == 1:       # IPv4  
                addr = socket.inet_ntoa(self.rfile.read(4))  
            elif addrtype == 3:     # Domain name  
                addr = self.rfile.read(ord(sock.recv(1)[0]))  
            port = struct.unpack('>H', self.rfile.read(2))  
            reply = b"\x05\x00\x00\x01"  
            try:  
                if mode == 1:  # 1. Tcp connect  
                    remote = socket.socket(socket.AF_INET, socket.SOCK_STREAM)  
                    remote.connect((addr, port[0]))  
                    print 'Tcp connect to', addr, port[0]  
                else:  
                    reply = b"\x05\x07\x00\x01" # Command not supported  
                local = remote.getsockname()  
                reply += socket.inet_aton(local[0]) + struct.pack(">H", local[1])  
            except socket.error:  
                # Connection refused  
                reply = '\x05\x05\x00\x01\x00\x00\x00\x00\x00\x00'  
            sock.send(reply)  
            # 3. Transfering  
            if reply[1] == '\x00':  # Success  
                if mode == 1:    # 1. Tcp connect  
                    self.handle_tcp(sock, remote)  
        except socket.error:  
            print 'socket error'  
def main():  
    server = ThreadingTCPServer(('', 1080), Socks5Server)  
    server.serve_forever()  
if __name__ == '__main__':  
    main()  