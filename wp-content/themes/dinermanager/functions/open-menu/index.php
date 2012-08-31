<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>OpenMenu Reader</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	
<?php 
	
	//************************ OpenMenu location ***************************//
	$menu_location = 'http://openmenu.com/menu/sample';
	//*************************** Sample *********************************//
	include 'class-omf-reader.php';
	$omr = New cOmfReader();
	$openmenu = $omr->read_file($menu_location);
	unset($omr);
	//*********************************************************************//
?>

</head>

<body>
	
	<pre>
	<?php print_r($openmenu); ?>
	</pre>
	
</body>
</html>