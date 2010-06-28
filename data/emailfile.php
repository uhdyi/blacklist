<?php
ini_set("display_errors", 0);

function send_email($user, $list, $filename, $to) {
$name = basename($filename);	 
$file_type = "application/xml"; // File Type

$email_from = "dyi.Queen@hoana.com"; // Who the email is from
$a = localtime();
$a[5] += 1900;
$email_subject = "Updated ".$list." list ".$a[5]."-".$a[4]."-".$a[3]." ".$a[2].":".$a[1].":".$a[0]; // The Subject of the email
$email_txt = "This is ".$user."'s ".$list." list"; // Message that the email has in it
$email_to = $to;

$headers = "From: ".$email_from;

$file = fopen($filename,'rb');
$data = fread($file,filesize($filename));
fclose($file);

$semi_rand = md5(time());
$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

$headers .= "\nMIME-Version: 1.0\n" .
"Content-Type: multipart/mixed;\n" .
" boundary=\"{$mime_boundary}\"";

$email_message = "";
$email_message .= "This is a multi-part message in MIME format.\n\n" .
"--{$mime_boundary}\n" .
"Content-Type:text/html; charset=\"iso-8859-1\"\n" .
"Content-Transfer-Encoding: 7bit\n\n" .$email_txt. "\n\n";

$data = chunk_split(base64_encode($data));

$email_message .= "--{$mime_boundary}\n" .
"Content-Type: {$file_type};\n" .
" name=\"{$name}\"\n" .
//"Content-Disposition: attachment;\n" .
//" filename=\"{$filename}\"\n" .
"Content-Transfer-Encoding: base64\n\n" .
$data . "\n\n" .
"--{$mime_boundary}--\n";
echo $email_to; echo $email_subject; echo $email_message; echo $headers;
$ok = @mail($email_to, $email_subject, $email_message, $headers);
return $ok;
}

$reval = array();
if(!isset($_REQUEST['uname'])) {
   $retval['status'] = 'Fail';
   $retval['statusmsg'] = 'no user name is set';
   echo json_encode($retval);
   exit;
}

if(!isset($_REQUEST['lname'])) {
   $retval['status'] = 'Fail';
   $retval['statusmsg'] = 'no list name is set';
   echo json_encode($retval);
   exit;
}

if(!isset($_REQUEST['addr'])) {
   $retval['status'] = 'Fail';
   $retval['statusmsg'] = 'no email address is set';
   echo json_encode($retval);
   exit;
}
$user = $_REQUEST['uname'];
$list = $_REQUEST['lname'];
$to = $_REQUEST['addr'];

$filename = "../sets/".$user."/".$list.".xml"; 
$emailfolder = "../email/";
$emailfile = $emailfolder."/".$list.".xml"; 

if(!copy($filename, $emailfile)) {
	$retval['status'] = 'FAILED';
	$retval['statusmsg'] = 'cannot copy default file to email folder';
	echo json_encode($retval);
	exit;
}
chmod($emailfile, 0755);

$x = send_email($user, $list,$emailfile, $to);

//remove the temporary folder and file
unlink($emailfile);
//rmdir($emailfolder);

$retval['status'] = 'OK';
$retval['statusmsg'] = 'OK';
$retval['xmlfile'] = $filename;
$retval['sent'] = $x ? 'Mail sent':'Mail failed';
echo json_encode($retval);
?>