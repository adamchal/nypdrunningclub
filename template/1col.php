<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?=$nav->getSiteName(); ?></title>
	<link rel="stylesheet" media="all" href="<?=$site['baseURL'] ?>lib/screen.css">
	<script src="<?=$site['baseURL']?>imageswap.js" type="text/javascript"></script>
	<link rel="shortcut icon" href="favicon.ico" >
</head>
<body <?=$nav->getBodyPreloader() ?>>
	<div id="site">
		<?=$nav->drawBox();?>
		<div id="pageContent">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr valign="top">
					<td width="204"><img src="img/spacer.gif" alt="" height="1" width="204"></td>
					<td width="772" id="oneCol"><?=$content ?></td>
					<td width="11"><img src="img/spacer.gif" alt="" height="1" width="11"></td>
				</tr>
			</table>
		</div>
		<?=$nav->drawFooter();?>
	</div>
	<?=require "lib/analytics.php"; ?>
</body>
</html>