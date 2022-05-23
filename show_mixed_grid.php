<?php // print mixed colour matrix, don't use this page individually!
# 1. set up bg and alpha
?>
<div class="grid-wrap" style="background: <?php echo $cx->colour2hex($cx->get_bg()) ?>"><!-- start of grid -->

<?php // start table with alpha
if ($cx->get_alpha() != 1) { ?>
<table class="center-table" style="opacity: <?php printf ("%.1f", $cx->get_alpha()); ?>">
<?php
} else { ?>
<table class="center-table">
<?php
}
?>

<?php // print 1st row, marking input colours
echo "<tr>";
echo "<td></td>"; # one extra cell at beginning of row
for ($i=0; $i<$cx->get_cols(); $i++) {
	if ($i%($cx->get_step()-1)==0) {
		echo '<td>+</td>';
	} else {
		echo '<td></td>';
	}
}
echo "</tr>\n";
?>

<?php // loop & print rows
for ($i=0; $i<$cx->get_rows(); $i++) {
?>
<tr>
<?php // loop & print cells
# add one extra cell at left
if ($i%($cx->get_step()-1) ==0) {
	echo '<td>+</td>';
} else {
	echo '<td></td>';
}
# loop the cells
for ($j=0; $j<$cx->get_cols(); $j++) {
?>
<?php
$col=$cx->get_colour($i,$j);
// $showcode=0 if !$showcode;
	// my $invert=calc_display_colour($col);
	// my $showcol=sprintf ' style="color:#ffffff;"';
$colcode=$cx->print_colour($col, $cx->get_alpha());
$colcode1=$cx->print_colour($col, 1); # hex ignore alpha
$colcode2=$cx->print_colour($col, $cx->get_alpha(),1); # rgb/a
$txtcol=$cx->calc_text_colour($col); # show white text if color is dark
?>
<td class="hover-toggle" style="background: <?php echo $colcode; ?>">
	<span class="normal-hidden" <?php echo $txtcol?'style="color:'.$txtcol.'"':''; ?>><?php echo $colcode1; ?><br />
		<?php echo $colcode2; ?></span>
	</td>
<?php
/*
	if ($showcode and $col->[3]) {
		$txt=sprintf '<span class="refcol">%s</span>', $txt;
	}
	my $sline=sprintf '<td %sstyle="background: %s"><div%s>%s</div></td>',
		($showcode==-1?'class="rotate"':''),
		print_colour_code($col),
		(calc_display_colour($col)?$showcol:''),
		$txt;
	my $cline=sprintf '<td class="hover-toggle" style="background: %s"><span class="normal-hidden"%s>%s<br />%s</span></td>',
		print_colour_code($col),
		($invert?' style="color:#fff"':''),
		print_colour_code($col),
		print_colour_code($col,1);
	if ($showcode==1) {
		printf $fh "%s%s", $sline, $cline;
	}
	elsif ($showcode==-1) {
		printf $fh "%s", $sline;
	} else {
		printf $fh "%s", $cline;
	}
	*/
?>
<?php
} // end of loop cells
?>
</tr>
<?php
} // end of loop rows
?>


</table>
</div><!-- end of grid -->
<hr />