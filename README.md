# gradient
2D color mixer
- give list of x-asix colors and y-axis colors and interval then mix them to generate a 2D color table.

-----

remember to change form action!

-----

2022-May-23: converted all codes to php, and now works only in a browser
- [ gmixer_wrapper.php ] interactive page to input colours and view mixed grid
- [ gradient_mixer.php ] core functions
- [ show_mixed_grid.php ] prints mixed grid as html
- [ style_grid.css ] styles
- [ spectrum/ ] colour picker

-----

- ~~can run the "core.pl" without setting up a web server, output to a static webpage, OR~~
- work interactively on server with a color picker (require spectrum.js https://github.com/bgrins/spectrum)

~~plans (none will happen soon):
- convert the interactive page to php -- NO plan to convert the core (yet)
- limit intervals based on color value distance~~
