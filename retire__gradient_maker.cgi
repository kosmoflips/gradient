#!/usr/bin/perlml

use strict;
use warnings;

use Storable qw/:DEFAULT dclone/;

use strict;
use warnings;
use Data::Dumper;

#cgi version , using the same set of subs in my "handytools"
#only differ by outputting cgi forms

use CGI qw(-utf8); #http://perldoc.perl.org/CGI.html
use CGI::Carp qw/fatalsToBrowser warningsToBrowser/;

require $ENV{DOCUMENT_ROOT}.'/cgi-bin/gradient/gradient_maker_core.pl';
my $k=new CGI;
my $p=$k->Vars();

my $zero; my $xx=[]; my $yy=[]; my $opt;
my $active;
if (keys %$p) {
	$opt->{lv}=verify_lv($p->{lv});
	$zero=unify_colour($p->{zero});
	$opt->{bg}=unify_colour($p->{bg});
	$opt->{alfa}=verify_alfa($p->{alfa});
	$xx=verify_clist($p->{tbx});
	$yy=verify_clist($p->{tby});
	$active=1;
}

my $m2=mix_matrix($zero, $xx, $yy, $opt->{lv});
my $oversize=calc_oversize(scalar @{$xx}, scalar @{$yy}, $opt->{lv});

# -------- html start ----------
my $http;
$http->{'-status'}='200 OK';
$http->{'-charset'}='utf-8';
$http->{'-type'}='text/html';
print $k->header($http);

my $fh=*STDOUT;
print_html_head_cgi_ver($fh);
if ($active and ($xx or $yy) and !$oversize) {
	print "<hr />\n";
	print_html_table_open($opt,$fh);
	print_matrix_table($m2);
	print "</table>\n";
	print "</div>\n";
}
elsif ($oversize) {
	printf "<div style=\"color: red\">data overload! reduce input colours or the mixing level and try again...</div>\n";
}
print_html_end_cgi_ver();
# -------- html end ----------


# -------- subs ----------


sub print_html_head_cgi_ver {
my $fh=shift;
print '<!DOCTYPE html>
<html lang="en">
<head>
<title>Gradient Maker 2D | Pocchong.de</title>
';
print_html_head_style($fh);
print '<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.1.1.min.js"></script>
<script src="/deco/js/spectrum/spectrum.js"></script>
<script src="/deco/js/spectrum/setting.js"></script>
<link rel="stylesheet" type="text/css" href="/deco/js/spectrum/spectrum.css" />

</head>

<body>
<h1>Gradient Maker 2D</h1>

<hr />

<ul style="list-style-type:none;">
<li>inspired by w3school\'s <a href="https://www.w3schools.com/colors/colors_mixer.asp" target="_blank">colour mixer</a>, mixing 2 sets of colours into a 2D table.</li>
<li>acceptable formats: HEX, HEX 3-letter, RGB, separate by whitespaces</li>
</ul>

<form action="/gradient" method="post">
<div style="text-align:left;width:800px;border: 1px solid #aaa;margin:10px auto">
<ul>
';
my $userdef=0;
if ($p->{tbx} or $p->{tby}) {
	$userdef=1;
}
printf '	<li>starting colour <input type="text" maxlength="15" size="6" value="%s" name="zero"></li>%s', ($zero?print_colour_code($zero):'#FFFFFF'),"\n";
printf '	<li>x division: <input type="text" size="80" value="%s" name="tbx" /></li>%s', ($p->{tbx}||($userdef?'':'C98EFF 467BDF')),"\n";
printf '	<li>y division: <input type="text" size="80" value="%s" name="tby" /></li>%s', ($p->{tby}||($userdef?'':'E6FF3A faa')),"\n";
printf '	<li>mix step: <input type="number" min="0" max="255" size="2" value="%s" name="lv"> theoretically 0~255, depending on your RAM</li>%s', (($opt->{lv} and $opt->{lv}>2)?($opt->{lv}-1):5),"\n";
printf '	<li>alpha (0~1): <input type="number" step="0.01" min="0" max="1" size="3" value="%s" name="alfa"> | background: <input type="text" maxlength="15" size="6" value="%s" name="bg"></li>%s', ($opt->{alfa}||1), ($opt->{bg}?print_colour_code($opt->{bg}):'#FFFFFF'),"\n";
printf '	<li>Find a colour <input id="full" /> <span id="basic-log" style="color: red"></span></li>
</ul>
</div>
<input type="reset" name="RESET" value="RESET" />
<input type="submit" name="submit" value="Mix" onclick="this.form.target=\'_self\'" />
</form>
';
1;
}
sub print_html_end_cgi_ver {
print <<TAIL;
<hr />
<div>
</div><!-- #outlayer -->
<div>
	<a href="/">www.pocchong.de</a><br />
	2006-<script>document.write(new Date().getFullYear())</script> kiyoko\@FairyAria
</div>
</body>
</html>
TAIL
}

