<?PHP
	session_start();
	session_cache_expire(30);
?>
<!-- page generated by the BowdoinRMH software package -->
<html>
	<head>
		<title>
			Log
		</title>
		<link rel="stylesheet" href="styles.css" type="text/css" />
	</head>
	<body>
		<div id="container">
			<?PHP include('header.php');?>
			<div id="content">
				<?php
				include('database/dbLog.php');
				if(array_key_exists("del_selected",$_POST)){
					$del=$_POST['delete'];
					delete_log_entries($del);
				}
				else if(array_key_exists("del_all",$_POST)){
					$log=get_full_log();
					for($i=0;$i<count($log);++$i){
						$to_delete[]=$log[$i][0];
					}
					delete_log_entries($to_delete);
				}
				$log=get_full_log();
				echo ('<form method="POST"><br><table align="center">' .
						'<tr><td colspan="2"><strong>Log of All Recent Changes</strong></td>' .
						'<td  align="right"><input type="submit" value="Delete All Records" name="del_all"><br>' .
						'
				    </td></tr>' .
						'<tr><td><strong>Time</strong></td><td><strong>Message</strong></td>
					<td><input type="submit" value="Delete Selected Records" name="del_selected"></td></tr>');
				echo ('<tr></tr>');
				for($i=0;$i<count($log);++$i) {
					echo ('<tr><td>'.$log[$i][1].'</td><td>'.$log[$i][2].'</td><td align="center"><input type="checkbox" name="delete[]" value="'.$log[$i][0].'">
					</td></tr>');
				}
				echo ('</table>');
				?>
				
			</div><?PHP include('footer.inc');?>
		</div>
	</body>
</html>