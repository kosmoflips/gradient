<?php
class GradientMixer {

private $MIN_STEP=2;
public $MATRIX = array(
	'x' => array (),
	'y' => array (),
	'bg' => [255,255,255],
	'alpha' => 1,
	'step' => 5, # step : for two colors c1 and c2, step =10 ==> there are 10 colours total from c1 to c2. so 8x intermediate colours between them. MINIMAL is therefore 2 meaning no intermediate colours
	'matrix' => array()
);

# setup
public function setup ($x=Null,$y=Null,$step=0,$alpha=0,$bg=Null) {
	# only verify if value isn't empty to avoid overwriting default values
	if ($x) {
		$this->MATRIX['x'] = $this->verify_clist($x);
	}
	if ($y) {
		$this->MATRIX['y'] = $this->verify_clist($y);
	}
	if ($bg) {
		$this->MATRIX['bg'] = $this->unify_colour($bg);
	}
	if ($step) { # step 2~30
		$this->MATRIX['step'] = $this->verify_step($step);
	}
	if ($alpha) {
		if (is_numeric($alpha) and $alpha<=1 and $alpha>0) {
			$this->MATRIX['alpha'] = $alpha;
		}
	}
	return 1;
}
public function mix ($x=Null, $y=Null, $step=Null) {
	$this->setup($x,$y,$step);
	$mx=array();
	# extreme cases, no x or no y
	if (count($this->MATRIX['x'])>0 and count($this->MATRIX['y'])>0) { # full mix x & y
		# 1. mix 1st row
		$row1 = $this->mixRow( $this->MATRIX['x'], $this->MATRIX['step'] );
		$mx=$this->add_matrix_row($row1, $mx, 0);
		# 2. mix 1st col by x[1] and y
		$col1=$this->mixRow( array_merge(array($this->MATRIX['x'][0]), $this->MATRIX['y']), $this->MATRIX['step'] );
		$curr_row = $row1;
		for ($j=1; $j<count($col1); $j++) { # start from index 1 b/c 0 is shared with 1st row
			$row_j = $this->createRow( $curr_row, $col1[$j]);
			$mx = $this->add_matrix_row($row_j, $mx, $j);
			$curr_row = $row_j;
		}
	}
	elseif (count($this->MATRIX['x'])==0 and count($this->MATRIX['y'])>0) { # has Y, no X, do 1 col only
	# I mix by row, so mixing by col only occurs here. no need to make it into a sub
		$col = $this->mixRow( $this->MATRIX['y'], $this->MATRIX['step']);
		for ($j=0; $j<count($col); $j++) {
			$mx[$j][0]=$col[$j];
		}
	}
	elseif (count($this->MATRIX['x'])>0 and count($this->MATRIX['y'])==0) { # has X, no Y. mix 1 row only
		$row2 = $this->mixRow($this->MATRIX['x'], $this->MATRIX['step']);
		$mx=$this->add_matrix_row($row2, $mx, 0);
	}
	$this->MATRIX['matrix'] = $mx;
	return 1;
}

# tools
public function calc_text_colour ($bgcor, $showblack=0) { # calc bg color. black text on light bg, or white text on dark bg. $bg is an array RGB
	$low=0;
	for ($i=0; $i<3; $i++) {
		if ($bgcor[$i]<125) {
			$low++;
		}
	}
	if ($low>=2) {
		return '#ffffff'; #use white txt
	}
	else {
		if ($showblack) { # if true, return nothing as default txt colour is black
			return '#000000'; #use dark txt
		}
		else {
			return '';
		}
	}
}
public function invert_colour ($col) { # col is an array RGB
	$c2=array();
	for ($i=0; $i<3; $i++) {
		$c2[$i]=abs(255-$col[$i]);
	}
	return $c2;
}
public function colour2hex ($col) { # col is array RGB
	$col2=strtoupper(sprintf ("#%02x%02x%02x", $col[0],$col[1],$col[2]));
	return $col2;
}
public function colour2rgb ($col) { # col is array RGB
	$col2=sprintf ("rgb(%d,%d,%d)", $col[0],$col[1],$col[2]);
	return $col2;
}
public function colour2rgba ($col,$alpha=1) { # col is array RGB
	$col2=sprintf ("rgba(%d,%d,%d,%.2f)", $col[0],$col[1],$col[2],$alpha);
	return $col2;
}
public function print_colour($col, $alpha=1, $as_rgb=0) { # auto decide rgba
	if ($alpha==1) {
		if ($as_rgb) {
			return $this->colour2rgb($col);
		} else {
			return $this->colour2hex($col);
		}
	} else { # always rgba
		return $this->colour2rgba($col,$alpha);
	}
}

# get data for html
public function get_bg () {
	return $this->MATRIX['bg'];
}
public function get_alpha () {
	return $this->MATRIX['alpha'];
}
public function get_step() {
	return $this->MATRIX['step'];
}
public function get_rows() {
	return count($this->MATRIX['matrix']);
}
public function get_cols() {
	return count($this->MATRIX['matrix'][0]);
}
public function get_colour($row,$col) {
	return $this->MATRIX['matrix'][$row][$col];
}

# verify ENV data
private function verify_clist ($cstr=Null) { # unify a list of colours
	$xx=array();
	if (gettype($cstr)!='array') {
		$xs=preg_split('/\s+/', $cstr);
	} else {
		$xs=$cstr;
	}
	foreach ($xs as $i=>$x0) {
		if (!$x0) {
			continue;
		}
		$xx[$i] = $this->unify_colour($x0);
	}
	return $xx;
}
private function unify_colour ($colour) { # input color code string (rgb,hex), convert input color code to RGB as 3-elem array
	$cv=[];
	$is_hex=0;

	if (gettype($colour) == 'array' and count($colour)>=3) {
		$cv=$colour;
	}
	elseif (preg_match('/(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})/', $colour, $matches)) { # RGB
		$cv=[$matches[1], $matches[2],$matches[3]];
	}
	elseif (preg_match('/([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i', $colour, $matches)) { #6-code HEX, IGNORE any extra string if any
		$cv=[$matches[1], $matches[2],$matches[3]];
		$is_hex=1;
	}
	elseif (preg_match('/[0-9a-f]{3}/i',$colour,$match)) { # 3-code HEX
		$is_hex=1;
		$c0=str_split($colour);
		for ($i=0; $i<3;$i++) {
			$cv[$i] = $c0[$i].$c0[$i];
		}
	}
	else {
		$cv=[255,255,255];
	}
	$cv=$this->chk_rgb($cv,$is_hex);
	return $cv;
}
private function chk_rgb ($c1, $is_hex=0) { # input [r,g,b], verify values
	$c2=[];
	for ($i=0;$i<3;$i++) {
		# hex to dec
		if ($is_hex) {
			$c2[$i] = hexdec($c1[$i]);
		} else {
			$c2[$i] = $c1[$i];
		}

		# now check range
		if ($c2[$i] > 255) {
			$c2[$i] = 255;
		}
		elseif ($c2[$i] < 0) {
			$c2[$i] = 0;
		}
		else {
			$c2[$i] = round($c2[$i]);
		}
	}
	return $c2;
}
private function verify_step ($step) {
	if ($step<=30 and $step>= $this->MIN_STEP) { # >30 is arbitary. theoretically can go as big as 254 steps but that's meaningless to human eyes and one should instead go view the colour palette in a painting software.
		return $step;
	} else {
		return $this->MATRIX['step'];
	}
}

# for 2x colours
private function calc_interval ($c1,$c2,$step=5) { # input 2 colours and step, return interval need to mix them
	if ($step<$this->MIN_STEP) {
		return array(); # step==2 , meaning no mix at all // but when mixing a row into a new column, will still do the mix, so not return NULL when step==2
	}
	$inv=array();
	for ($i=0;$i<3;$i++) {
		$inv[$i] = ( $c2[$i] - $c1[$i] ) / ($step-1);
	}
	return $inv;
}
private function mix2colours ($c1,$c2,$step=5) { # input 2 colours and step, return list of mixed colours, does NOT include inputs
# c1 and c2 should be both unified already! same for $step
	if ($step<=$this->MIN_STEP) {
		return array(); # step==2 , meaning no mix at all. is this only true when both colours exist.
	}

	$inv=$this->calc_interval($c1,$c2,$step);

	$mixed=array( $c1 );
	for ($j=1; $j<=($step-2); $j++) { # loop for a total of ($step-2) mixed colours, start from j=1 because $mixed[0] has c1 value for the first mix
		for ($i=0; $i<3; $i++) { #loop through each rgb
			$mixed[$j][$i] = $mixed[$j-1][$i] + $inv[$i];
		}
	}
	# remove 1st elem which is c1
	array_shift($mixed);
	# now round rgb for each of the list, so they don't go out of range
	foreach ($mixed as $j=>$c) {
		$mixed[$j] = $this->chk_rgb($mixed[$j]);
	}
	return $mixed;
}
private function create_colour ($c1, $inv) { # input one colour and one interval, return new color of the two added up
	$c2=array();
	for ($i=0; $i<3; $i++) {
		$c2[$i]=$c1[$i] + $inv[$i];
	}
	return $this->chk_rgb($c2);
}

# for a list of colours
private function mixRow ($row, $step=5) { # input a list of colours and step, mix every two adjacent colours and return the extended mixed list
# input values are all unified
	if ($step<=$this->MIN_STEP) { # no mix
		return $row;
	}
	if (count($row)<2) { # $row has only 1 element. no need to mix
		return $row;
	}

// echo "<pre>";
// print_r($row);
// echo "</pre>";
// exit;

	$current=$row[0];
	$mixrow=array();
	array_push($mixrow,$row[0]);
	for ($i=1; $i<count($row); $i++) {
		$mix2=$this->mix2colours($current,$row[$i],$step);
		$mixrow=array_merge($mixrow,$mix2);
		array_push($mixrow,$row[$i]);
		$current=$row[$i];
	}
	return $mixrow;
}
private function createRow($row, $refcol) { # input a list of colours, a new colour ($refcol), calculate distance between $row[1] and $refcol, then generate new row using the same distance
	if ($row[0] == $refcol) { # new column starting colour same as last row
		return $row; # no need to mix
	}
	// if ($step<$this->MIN_STEP) {
		// $step=$this->MIN_STEP; # still need to "create" the row with given colour ref
	// }

	$inv = $this->calc_interval($row[0], $refcol, 2);

	$row2=array( $refcol );
	for ($j=1; $j<count($row); $j++) {
		$c2= $this->create_colour($row[$j], $inv);
		array_push($row2, $c2);
	}
	return ($row2);
}
private function add_matrix_row ($row, $matrix, $rowid) { # row is AFTER mix. also give $rowid so newly mixed row content will be saved into $matrix's row by $rowid
	$matrix[$rowid]=array();
	for ($j=0; $j<count($row); $j++) {
		$matrix[$rowid][$j]=$row[$j];
	}
	return $matrix;
}

} # close class

?>

