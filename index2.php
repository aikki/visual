<form method="post" enctype="multipart/form-data">
<input type="file" name="image" accept="image/bmp" /> <button>Wyślij</button>
</form>

<pre style="font-size: 12px;">
<?php
    require 'functions.php';
    if (!empty($_FILES['image'])) {
        $file = $_FILES['image'];

        $image = imagecreatefrombmp($file['tmp_name']);

        if ($image) {
            $bits = getimagesize($file['tmp_name'])['bits'];

            define('BLACK', 0);
            define('WHITE', pow($bits, 2) - 1);

            $width = imagesx($image);
            $height = imagesy($image);

            echo "Plik ma rozmiar $width x $height<br/>";
            echo '<img src='.getImageString($image).'><br/>';

            $part1 = setupPartImage4($width, $height, $black1);
            $part2 = setupPartImage4($width, $height, $black2, $white2);

            $w = [0,0,0,0];
            $b = [0,0,0,0];
            for ($y = 0; $y < $height; $y++) {
                for ($x = 0; $x < $width; $x++) {
                    $pixel = imagecolorat($image, $x, $y);
                    $r = rand(0,3);
                    if ($pixel === WHITE) {
                        $w[$r]++;
                        if ($r == 0) {
                            imagesetpixel($part1, $x*2, $y*2, $black1);
                            imagesetpixel($part1, $x*2, $y*2+1, $black1);
                            imagesetpixel($part2, $x*2, $y*2, $black2);
                            imagesetpixel($part2, $x*2, $y*2+1, $black2);
                        } elseif ($r == 1) {
                            imagesetpixel($part1, $x*2+1, $y*2, $black1);
                            imagesetpixel($part1, $x*2+1, $y*2+1, $black1);
                            imagesetpixel($part2, $x*2+1, $y*2, $black2);
                            imagesetpixel($part2, $x*2+1, $y*2+1, $black2);
                        } elseif ($r == 2) {
                            imagesetpixel($part1, $x*2+1, $y*2+1, $black1);
                            imagesetpixel($part1, $x*2, $y*2, $black1);
                            imagesetpixel($part2, $x*2+1, $y*2+1, $black2);
                            imagesetpixel($part2, $x*2, $y*2, $black2);
                        } elseif ($r == 3) {
                            imagesetpixel($part1, $x*2, $y*2, $black1);
                            imagesetpixel($part1, $x*2+1, $y*2+1, $black1);
                            imagesetpixel($part2, $x*2, $y*2, $black2);
                            imagesetpixel($part2, $x*2+1, $y*2+1, $black2);
                        }
                    } else if ($pixel === BLACK) {
                        $b[$r]++;
                        if ($r == 0) {
                            imagesetpixel($part1, $x*2, $y*2, $black1);
                            imagesetpixel($part1, $x*2, $y*2+1, $black1);
                            imagesetpixel($part2, $x*2+1, $y*2, $black2);
                            imagesetpixel($part2, $x*2+1, $y*2+1, $black2);
                        } elseif ($r == 1) {
                            imagesetpixel($part1, $x*2+1, $y*2, $black1);
                            imagesetpixel($part1, $x*2+1, $y*2+1, $black1);
                            imagesetpixel($part2, $x*2, $y*2, $black2);
                            imagesetpixel($part2, $x*2, $y*2+1, $black2);
                        } elseif ($r == 2) {
                            imagesetpixel($part1, $x*2+1, $y*2+1, $black1);
                            imagesetpixel($part1, $x*2, $y*2, $black1);
                            imagesetpixel($part2, $x*2, $y*2+1, $black2);
                            imagesetpixel($part2, $x*2+1, $y*2, $black2);
                        } elseif ($r == 3) {
                            imagesetpixel($part1, $x*2+1, $y*2, $black1);
                            imagesetpixel($part1, $x*2, $y*2+1, $black1);
                            imagesetpixel($part2, $x*2, $y*2, $black2);
                            imagesetpixel($part2, $x*2+1, $y*2+1, $black2);
                        }
                    } else {
                        echo 'Plik BMP nie jest czarno-biały';
                    }
                }
            }

            echo "Part 1:<br/>";
            echo '<img src='.getImageString($part1).'><br/>';
            echo "Part 2:<br/>";
            echo '<img src='.getImageString($part2).'><br/>';
            echo "Częstotliwość patternów: <br/>    W: ".print_r($w, true).", B: " . print_r($b, true) . "<br/>";

            imagecolortransparent($part2, $white2);
            imagecopymerge($part1, $part2, 0,0,0,0, $width * 2, $height * 2, 100);

            echo "Merged:<br/>";
            echo '<img src='.getImageString($part1).'><br/>';


            $clean = imagecreate($width, $height);
            $cwhite = imagecolorallocate($clean, 255,255,255);
            $cblack = imagecolorallocate($clean, 0,0,0);
            for ($y = 0; $y < $height; $y++) {
                for ($x = 0; $x < $width; $x++) {
                    $arr = [
                        imagecolorat($part1, $x*2, $y*2),
                        imagecolorat($part1, $x*2+1, $y*2),
                        imagecolorat($part1, $x*2, $y*2+1),
                        imagecolorat($part1, $x*2+1, $y*2+1),
                    ];
                    if (array_sum($arr)/4 == max($arr)) {
                        imagesetpixel($clean, $x, $y, $cblack);
                    }
                }
            }
            echo "Cleaned:<br/>";
            echo '<img src='.getImageString($clean).'><br/>';



        } else {
            echo 'Nieprawidłowy plik BMP.';
        }
    }
?>