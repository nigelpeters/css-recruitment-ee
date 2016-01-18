<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<title>Your Message Subject or Title</title>
</head>
<body>
	<p>Dear <?=$authorinfo->screen_name; ?>, </p>
	<p><?=$userinfo->screen_name; ?> has applied for the job position: <?=$userinfo->title; ?>  </p>
	<p>Please find their cover letter below</p>
	<p>-------------------------------------------------------------------</p>
	<?=$userinfo->application_message; ?>
</body>
</html>