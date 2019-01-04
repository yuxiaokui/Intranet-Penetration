<?php
$dbhost = '10.192.83.53';
$dbport = '3306';
$dbuser = 'phpsocks5';
$dbpass = '123123';
$dbname = 'phpsocks5';

$secretkey = "gnuwisy78346g86s786d87f6782hjdkhkjchzxkjhkdjhdfhi2uq3yrsyidyfuishyidhyichyizxihyiuhyfiu89347979834ghe987t898d7uf897s89j";

$dbprefix = 'phpsocks5_';
$invstep = 100000;
$invmax = 3000000;

$version = "01";

function phpsocks5_encrypt($datastr)
{
	global $secretkey;
	$encrypted = '';
	for($i = 0; $i < strlen($datastr); $i++)
		$encrypted .= chr(ord($datastr[$i]) ^ ord($secretkey[$i % strlen($secretkey)]));
	return $encrypted;
}

function phpsocks5_decrypt($datastr)
{
	return phpsocks5_encrypt($datastr);
}

function phpsocks5_http_500($errmsg)
{
	header('HTTP/1.1 500');
	echo phpsocks5_encrypt($errmsg);
	mysql_close();
	exit;
}

function phpsocks5_usleep($usec)
{
	global $dbhost;
	global $dbport;
	global $dbuser;
	global $dbpass;
	global $dbname;
	mysql_close();
	usleep($usec);
	if(!mysql_pconnect("$dbhost:$dbport", $dbuser, $dbpass))
		phpsocks5_http_500('mysql_pconnect error');
	if(!mysql_select_db($dbname))
		phpsocks5_http_500('mysql_select_db error');
}

set_time_limit(30);

if(!mysql_pconnect("$dbhost:$dbport", $dbuser, $dbpass))
	phpsocks5_http_500('mysql_pconnect error');
if(!mysql_select_db($dbname))
	phpsocks5_http_500('mysql_select_db error');

$postdata = phpsocks5_decrypt(file_get_contents("php://input"));
if(!$postdata)
{
	if(!mysql_query("CREATE TABLE ${dbprefix}conning (  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,  sid VARCHAR(200) NOT NULL,  host VARCHAR(512) NOT NULL,  port INTEGER NOT NULL,  PRIMARY KEY (id))"))
	{
		echo 'Create table 1 error.';
		mysql_close();
		exit;
	}
	if(!mysql_query("CREATE TABLE ${dbprefix}sending (  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,  sid VARCHAR(200) NOT NULL,  cnt VARCHAR(8192) NOT NULL,  PRIMARY KEY (id))"))
	{
		echo 'Create table 2 error.';
		mysql_close();
		exit;
	}
	if(!mysql_query("CREATE TABLE ${dbprefix}recving (  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,  sid VARCHAR(200) NOT NULL,  cnt VARCHAR(8192) NOT NULL,  PRIMARY KEY (id))"))
	{
		echo 'Create table 3 error.';
		mysql_close();
		exit;
	}
	mysql_close();
	echo 'Create tables successfully.';
	exit;
}
if($postdata[0] != $version[0] || $postdata[1] != $version[1])
	phpsocks5_http_500('version not match');
$phpsid = mysql_escape_string($_COOKIE['PHPSESSID']);

if($postdata[2] == "1")
{
	if(!session_start())
		phpsocks5_http_500('session_start error');
	$host = mysql_escape_string(strtok(substr($postdata, 3), ':'));
	$port = mysql_escape_string(strtok(':'));
	$phpsid = mysql_escape_string(session_id());
	mysql_query("DELETE FROM ${dbprefix}conning WHERE sid = '" . $phpsid . "'");
	mysql_query("DELETE FROM ${dbprefix}sending WHERE sid = '" . $phpsid . "'");
	mysql_query("DELETE FROM ${dbprefix}recving WHERE sid = '" . $phpsid . "'");
	if(!mysql_query("INSERT INTO ${dbprefix}conning (sid, host, port) VALUES ('" . session_id() . "', '$host', '$port')"))
		phpsocks5_http_500('mysql_query INSERT error');
}
elseif($postdata[2] == "2")
{
	$inv = 0;
	$rslt = mysql_query("SELECT id, host, port FROM ${dbprefix}conning WHERE sid = '" . $phpsid . "'");
	if(!$rslt)
		phpsocks5_http_500('mysql_query SELECT error');
	$row = mysql_fetch_row($rslt);
	if(!$row)
		phpsocks5_http_500('mysql_fetch_row error');
	$rmtskt = fsockopen($row[1], $row[2]);
	if(!$rmtskt)
	{
		mysql_query("DELETE FROM ${dbprefix}conning WHERE id = $row[0]");
		phpsocks5_http_500('fsockopen error');
	}
	if(!stream_set_blocking($rmtskt, 0))
		phpsocks5_http_500('stream_set_blocking error');
	while(true)
	{
		$noop = true;
		if(feof($rmtskt))
			phpsocks5_http_500('feof');
		$cnt = fread($rmtskt, 4096);
		if($cnt)
		{
			if(!mysql_query("INSERT INTO ${dbprefix}recving (sid, cnt) VALUES ('" . $phpsid . "', '" . base64_encode($cnt) . "')"))
				phpsocks5_http_500('mysql_query INSERT error');
			$noop = false;
		}
		phpsocks5_usleep($inv);
		$rslt = mysql_query("SELECT id, cnt FROM ${dbprefix}sending WHERE sid = '" . $phpsid . "' ORDER BY id ASC LIMIT 1");
		$row = mysql_fetch_row($rslt);
		if($row)
		{
			$noop = false;
			mysql_query("DELETE FROM ${dbprefix}sending WHERE id = $row[0]");
			if(!$row[1])
				phpsocks5_http_500('break');
			if(!fwrite($rmtskt, base64_decode($row[1])))
				phpsocks5_http_500('fwrite error');
		}
		if($noop)
		{
			$inv += $invstep;
			if($inv > $invmax)
				$inv = $invmax;
		}
		else
		{
			set_time_limit(30);
			$inv = 0;
		}
		phpsocks5_usleep($inv);
	}
}
elseif($postdata[2] == "3")
{
	if(!mysql_query("INSERT INTO ${dbprefix}sending (sid, cnt) VALUES ('" . $phpsid . "', '" . base64_encode(substr($postdata, 3)) . "')"))
		phpsocks5_http_500('mysql_query INSERT INTO error');
}
elseif($postdata[2] == "4")
{
	$inv = 0;
	while(true)
	{
		$rslt = mysql_query("SELECT id, cnt FROM ${dbprefix}recving WHERE sid = '" . $phpsid . "' ORDER BY id ASC LIMIT 1");
		if(!$rslt)
			phpsocks5_http_500('mysql_query SELECT error');
		$row = mysql_fetch_row($rslt);
		if($row)
		{
			mysql_query("DELETE FROM ${dbprefix}recving WHERE id = $row[0]");
			if($row[1])
				echo phpsocks5_encrypt(base64_decode($row[1]));
			else
				phpsocks5_http_500('break');
			break;
		}
		$inv += $invstep;
		if($inv > $invmax)
			$inv = $invmax;
		phpsocks5_usleep($inv);
	}
}
elseif($postdata[2] == "5")
{
	mysql_query("INSERT INTO ${dbprefix}sending (sid, cnt) VALUES ('" . $phpsid . "', '')");
	mysql_query("INSERT INTO ${dbprefix}recving (sid, cnt) VALUES ('" . $phpsid . "', '')");
}
mysql_close();
?>