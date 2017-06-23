PHProxy Source Code README
_____________________________________________________________________

Source Code Version 0.5b2 - January 20th 2007
Latest Version: http://www.sourceforge.net/projects/poxy/

Copyright 2002-2007 Abdullah Arif


Contact
_____________________________________________________________________

Email: phproxy.support@gmail.com
Website: http://whitefyre.com/


Support and Bug Reports
_____________________________________________________________________

http://whitefyre.com/forums/
phproxy.support@gmail.com


Table of Contents
_____________________________________________________________________

1. License
2. What is PHProxy?
3. How it Works
4. Requirements
5. Installation
6. Configurable Script Variables
7. Available Options
8. Disclaimer
9. Bugs and Limitations
10. ChangeLog, FAQ, TODO, LICENSE, Bugs, Limitations
11. Credits


1. License
_____________________________________________________________________

This source code is released under the GPL.
A copy of the license in provided in this package in the file
 named LICENSE.txt


2. What is PHProxy?
_____________________________________________________________________


PHProxy is a web HTTP proxy 
designed to bypass proxy restrictions through
a web interface very similar to the popular CGIProxy 
(http://www.jmarshall.com/tools/cgiproxy/). For example, in my 
university, the IT department blocks a lot of harmless websites 
simply because of their popularity. So I use this porgram to access 
those websites. The only thing that PHProxy needs is a web server 
with PHP installed (see Requirements below).
Be aware though, that the sever has to be able to access those 
resources to deliver them to you.



3. How it Works
_____________________________________________________________________

You simply supply a URL to the form and click Browse. The script then 
accesses that URL, and if it has any HTML contents, it modifies 
any URLs so that they point back to the script. Of course, there is more
to it than this, but if you would like to know more in
detail, view the source code. 
Comments have yet to be added.


4. Requirements
_____________________________________________________________________

- PHP version >= 4.2.0
- safe_mode turned off or at least having the fsockopen() function not disabled
- PHP version >= 4.3.0 and OpenSSL for support for secure connections (https)
- Zlib for output compression
- file_uploads turned On for HTTP file uploads.


5. Installation
_____________________________________________________________________

Simply upload these files to a directory of your liking (prefrebly in its own directory):

- index.php
- index.inc.php
- style.css

You can rename index.php without any problems, but not index.inc.php.

A good idea is to change these PHP settings in your php.ini file
or for instance Apache's httpd.conf or per directory .htaccess files:

- register_globals = Off (safer for your script)
- magic_quotes_gpc = Off (avoids unnecessary, slow stripslashing in the script)
- always_populate_raw_post_data = Off (no need for this extraneous data)
- zlib.output_compression = On (to enable output compression, better than doing it inside the script)

Your script will still function normally without these settings though.

All you need to do now is to access index.php and start browsing!


6. Configurable Script Variables
_____________________________________________________________________

These variables are available at the beginning of the index.php file:

- $_config:
___________

url_var_name:              name of the variable the contains the url 
                           to be passed to the script. default: 'q'
flags_var_name:            name of the variables the contains the flags
                           to be passed to the script. default: 'hl'
get_form_name:             name of the GET forms in case they were 
                           passed through the proxy.
                           default: '____pgfa'
basic_auth_var_name:       name of the variable when prompted for Basic
                           authentication. default:  '____pbavn'
max_file_size:             maximum file size in BYTES that can be 
                           downloaded through the proxy.
                           Use -1 for unlimited. default: -1
allow_hotlinking:          whether to allow hotlinking or not.
                           default is not unless in $_hotlink_domains.
                           default:0
upon_hotlink:              what to do if a website hotlinks through your
                           proxy. Possible values:
                           - 1: show the URL form (homepage)
                           - 2: issue a HTTP 404 Not Found error
                           - any web address which the user will be 
                              redirected to (e.g. goatse pic)
                           default: 1
compress_output:           whether to use gzip compression or not.
                           This may or may not work depending on whether
                           your PHP installation has Zlib loaded, and
                           whether the user's browser supports gzip
                           content encoding. Turn this on if you're
                           worried about bandwidth. This might be a 
                           bit taxing on your server if you have any kind of
                           substantial traffic. It is also better to enable
                           output compression through php.ini than here.
                           default: 0            


- $_flags:
__________

This array contains the default values for the browsing options which
 are explained in section 7.


- $_frozen_flags:
_________________

When a flag is frozen, it is no longer shown in the URL forms, and the
 user won't be able to change its value. A frozen flag will always
 assume its value given in $_flags. This is useful for forcing
 a specific URL encoding, or forcing the mini URL form to always be
 there for instance.
0 is for not frozen. 1 is for frozen. default: all are unfrozen.


- $_labels:
___________

The labels on flags.


- $_hosts:
__________

Each entry in this array is a seperate piece of regular expression 
code that is matched against the host part of the currently browsed URL.
If it evaluates to true, the user will not be allowed to access
that URL.
The first default entry contains the regular expression for private 
networks which are not supposed to be shown on the Internet.


- $_hotlink_domains:
____________________

This array holds entries of domain names which are allowed to hotlink
through your proxy when allow_hotlinking is 0.

To allow "example.com" and "example2.com" to hotlink:

$_hotlink_domains = array('example.com', 'example2.com');

You don't need to include the "www" part as it is automatically 
accounted for. Your website's domain name is also automatically included 
in this array.


- $_insert:
___________

This does nothing yet.


7. Available Options
_____________________________________________________________________

These options are available to you through the web interface. 
You can also edit the default values in the $_flags in index.php
Values can either be 1 (true) or 0 (false). 

+-------------------------------------------------------------------+
| Option         | Explanation                                      |
+-------------------------------------------------------------------+
| Include Form   | Includes a mini URL-form on every HTML page for  |
|                | easier browsing.                                 |
| Remove Scripts | Remove all sorts of client-side scripting        |
|                | (i.e. JavaScript). Removal is not perfect. Some  |
|                | scripts might slip by here and there.            |
| Accept Cookies | Accept HTTP cookies                              |
| Show Images    | Show images. You might want to turn this off if  |
|                | you want to save your server's bandwith.         |
| Show Referer   | Show referring website in HTTP headers. This     |
|                | will show the base URL for the website you're    |
|                | currently viewing. Because many website disable  |
|                | HotLinking, this can be quite useful.            |
| Rotate13       | Use rotate13 encoding on the URL. *              | 
| Base64         | Use base64 encoding on the URL. *                |
| Strip Meta     | Strip meta HTML tags                             |
| Strip Title    | Strip Website title                              |
| Session Cookies| Store cookies for this current session only      |
+-------------------------------------------------------------------+

* only one type of encoding will be used even if both are selected


8. Disclaimer
_____________________________________________________________________

Since this script basically bypasses restrictions that were imposed
on you, using it might be illegal in your country, school, office, 
or whatever. Even your host might not allow you to run it. Use it at
your own risk. I will not be responsible for any damages done or any
 harm that might result from using this script.



9. Bugs and Limitations
_____________________________________________________________________

PHP is retarded by nature, and as such, some problems arise that 
would have not if this script were otherwise coded in another programming
language. The first example of this is dots in incoming variable names 
from POST and GET. In a normal programming language, this wouldn't be
a problem as these variables could be accessed normally as they are 
supplied, with dots included. In PHP, however, dots in GET, POST, and
COOKIE variable names are magically transformed into underscores 
because of the stupid shit that is register_globals. Things like Yahoo! 
Mail which has dots in variable names will not work. There's no easy way
around this, but luckily, I have provided the solutions right here:

  1. I've already taken care of cookies by manually transforming
     the underscores manually into dots when needed.
  2. For GET variables, this shouldn't be a huge problem since the URLs
     are URL-encoded into the url_var_name. The only time this should be
     an issue is when a GET form uses dots in input names, and this could
     be recitified by using $_SERVER['QUERY_STRING'], and parsing that
     variable. But this, luckily, doesn't happen too often.
  3. As for POST data, one solution is to use $HTTP_RAW_POST_DATA. But then,
     this variable might not be available in certain PHP configurations,
     and it would need further parsing, and it still doesn't account 
     for uploaded FILES. This is extremely impractical and ugly.

The best thing you could do if you have enough control over your Web server
 and can compile custom builds of PHP is to delete a single line in a PHP source
code file called "php_variables.c" located in the "main" directory.
 The function in question is called "php_register_variable_ex". I've only checked
 this with PHP v4.4.4 and the exact line to delete is 117th line which basically
 consists of this:

			case '.':

Now just compile and install PHP and everything should be fine. Just make
sure that you have register_globals off or something might get messed up.
I've done this on my demo install on http://grab.cc/ and it's working
flawlessly.


Another problem facing many Web proxies is support for JavaScript.
Currently, therse is no such thing in PHProxy 0.5 but hopefully basic
support will be introduced for version 0.6. The best thing you could do
right now is to have the JavaScript disabled on your browsing options
as most sites degrade gracefully, such as Gmail.

A third limitation for Web proxies is content accessed from within proxied
Flash and Java applications and such. Since the proxy script doesn't have access
to the source code of these applications, the links which they may decide
 to stream or access will not be proxified. There's no easy solution for this
 right now.

PHProxy also doesn't support FTP. This may or may not be introduced 
in future releases, but there are no current plans for FTP support.


10. ChangeLog, TODO, LICENSE
_____________________________________________________________________

Refer to the accompanying files.



11. Credits
_____________________________________________________________________

James Marshall (http://www.jmarshall.com/) for his excellent CGIProxy
script which was a high inspiration and guide for me. The HTML
modification section is based off his script.

Also massive thanks to everyone who emailed me or posted on forums bugs,
 suggestions, and feedback. I really appreciate it.

