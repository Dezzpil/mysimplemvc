<!DOCTYPE html>
<html>

	<head>
	
		<title>MySimpleCore</title>
		<meta charset="utf-8"/>
        
		<!-- подключаем js/css для шаблона -->
        <script type="text/javascript" src="<?=TEMPLATE_PATH;?>base.js"></script>
		
	</head>
	<body>
	
		<?php
			// Вывод ответа контроллера
			echo $content;
		?>

	</body>
    
</html>