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

            $part1 = setupPartImage($width, $height, $black1);
            $part2 = setupPartImage($width, $height, $black2, $white2);

            $w1 = 0;
            $w2 = 0;
            $b2 = 0;
            $b1 = 0;
            
            for ($y = 0; $y < $height; $y++) {
                for ($x = 0; $x < $width; $x++) {
                    $pixel = imagecolorat($image, $x, $y);
                    if ($pixel === WHITE) {
                        if (rand(0,1)) {
                            $w1++;
                            imagesetpixel($part1, $x*2, $y, $black1);
                            imagesetpixel($part2, $x*2, $y, $black2);
                        } else {
                            $w2++;
                            imagesetpixel($part1, $x*2+1, $y, $black1);
                            imagesetpixel($part2, $x*2+1, $y, $black2);
                        }
                    } else if ($pixel === BLACK) {
                        if (rand(0,1)) {
                            $b1++;
                            imagesetpixel($part1, $x*2, $y, $black1);
                            imagesetpixel($part2, $x*2+1, $y, $black2);
                        } else {
                            $b2++;
                            imagesetpixel($part1, $x*2+1, $y, $black1);
                            imagesetpixel($part2, $x*2, $y, $black2);
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
            echo "Częstotliwość patternów: <br/>    W1: $w1, W2: $w2, B1: $b1, B2: $b2<br/>";

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
                        imagecolorat($part1, $x*2, $y),
                        imagecolorat($part1, $x*2+1, $y),
                    ];
                    if (array_sum($arr)/2 == max($arr)) {
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