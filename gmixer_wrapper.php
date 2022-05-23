<?php
require('gradient_mixer.php');
?>
<!DOCTYPE html>
<html>
<head>
<title>Gradient Mixer 2D | Pocchong.de</title>
<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.1.1.min.js"></script>
<!-- tweaked js, original: https://github.com/bgrins/spectrum -->
<script src="spectrum/spectrum.js"></script>
<script src="spectrum/setting.js"></script>
<link rel="stylesheet" type="text/css" href="spectrum/spectrum.css" />
<link rel="stylesheet" type="text/css" href="style_grid.css" />
</head>
<body>
<h1>Gradient Mixer 2D</h1>

<hr />

<ul style="list-style-type:none;">
<li>inspired by w3school's <a href="https://www.w3schools.com/colors/colors_mixer.asp" target="_blank">colour mixer</a>, mixing 2 sets of colours into a 2D table.</li>
<li>acceptable formats: HEX, HEX 3-letter, RGB, separate by whitespaces</li>
</ul>
<?php
print_r($_SERVER);
?>
<form action="/gradient" method="post">
<div style="text-align:left;width:800px;border: 1px solid #aaa;margin:10px auto">
<ul>
	<li>x division - first colour will be at the top-left<br />
		<input type="text" size="60" value="<?php echo $_POST['x']??'' ?>" name="x" placeholder="C98EFF 467BDF" /></li>
	<li>y division - follows first colour in "x" unless "x" isn't given<br />
		<input type="text" size="60" value="<?php echo $_POST['y']??'' ?>" name="y" placeholder="E6FF3A faa" /></li>
	<li>mix step <input type="number" min="2" max="30" size="2" value="<?php echo $_POST['step']??5; ?>" name="step"> includes start, end and intermediate colours. min=2 for no mixing. max=30</li>
	<li>alpha (0~1) <input type="number" step="0.01" min="0" max="1" value="<?php echo $_POST['alpha']??1; ?>" name="alpha" style="width: 50px;"> | 
		background <input type="text" maxlength="15" size="2" value="<?php echo $_POST['bg']??'#FFFFFF'; ?>" name="bg" placeholder="#ffffff"> | 
		colour picker <input id="full" /> <span id="basic-log" style="color: red"></span></li>
</ul>
</div>
<input type="reset" name="RESET" value="RESET" />
<input type="submit" name="submit" value="Mix" onclick="this.form.target='_self'" />
</form>
<hr />
<div>

<?php
if (array_key_exists('submit',$_POST) and $_POST['submit'] == 'Mix') {
	$cx=new GradientMixer();
	$cx->setup($_POST['x'],$_POST['y'],$_POST['step'],$_POST['alpha'],$_POST['bg']);
	$cx->mix();
	include('show_mixed_grid.php');
}
# else do nothing
?>

</div>
<div>
<a href="/">www.pocchong.de</a> | <a href="https://github.com/kosmoflips/gradient2D">GradientMixer</a><br />
2006-<script>document.write(new Date().getFullYear())</script> kiyoko@FairyAria
</div>
</body>
</html>
