<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html><head><title>Рок-кафе :: Днепропетровск :: Обратная связь</title>
<META HTTP-EQUIV="content-type" CONTENT="text/html; charset=windows-1251">
<META HTTP-EQUIV="content-language" CONTENT="ru">
<link rel="stylesheet" type="text/css" href="../css/css.css">
</head>

<body topmargin=4 leftmargin=0 bgcolor=#6a026a text=#ffffff>

<table width=468 align=center cellspacing=0 cellpadding=0 border=0>

<tr><td align=center valign=top>

<br>
<h2>Ваше сообщение :: Результат отправки</h2><hr size=1 color=white><br>

<?

$name_g = $_GET['name'];
$message_g = $_GET['message'];
$from_g = $_GET['from'];

/* error - заполн не всё */
if ((!$name_g) or (!$message_g) or (!$from_g))
	{
	echo "<font color=red><b>Вы заполнили не все поля сообщения</font><p>Пожалуйста, вернитесь и повторите попытку<p><hr size=1 color=white><p>[ <a href='javascript:self.close();'>закрыть окно</a> ]</b></td></tr></table></body></html>"; 
	return;
	}

/* OK */

$subj = "Message from WEB-site www.rock-cafe.dp.ua";
$time  = date("l, d M Y, H:i:s")."\n";
$email = "monks@mail.ru";
//$email = "nsp@workfromhome.com.ua";

/* формируем сообщение на имейл*/
$m = " Name: $name_g\n\n Message: $message_g\n\n E-mail: $from_g\n\n $time";

echo "<b><font color=lightgreen>Ваше сообщение отправлено</font></b>";

mail($email, $subj, $m, "From: $from_g");

/* end */
echo "<p><hr size=1 color=white><p>[ <a href='javascript:self.close();'>закрыть окно</a> ]</td></tr></table></body></html>"; 
return;
?>