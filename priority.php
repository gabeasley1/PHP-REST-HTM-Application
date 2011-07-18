<?php
/**
 * Displays an image to the screen based on some given values.  
 * Used for the priority bubble.
 * @package PhpHtmRestApplicationPriorityImage
 */

/**
 * Turns an array of HSV values into an array of RGB values
 * @param array $hsv The HSV values to load in
 * @return array The RGB values that are equivalent.
 */
function hsvToRgb(array $hsv) {
    list($H,$S,$V) = $hsv;
    //1
    $H *= 6;
    //2
    $I = floor($H);
    $F = $H - $I;
    //3
    $M = $V * (1 - $S);
    $N = $V * (1 - $S * $F);
    $K = $V * (1 - $S * (1 - $F));
    //4
    switch ($I) {
        case 0:
            list($R,$G,$B) = array($V,$K,$M);
            break;
        case 1:
            list($R,$G,$B) = array($N,$V,$M);
            break;
        case 2:
            list($R,$G,$B) = array($M,$V,$K);
            break;
        case 3:
            list($R,$G,$B) = array($M,$N,$V);
            break;
        case 4:
            list($R,$G,$B) = array($K,$M,$V);
            break;
        case 5:
        case 6: //for when $H=1 is given
            list($R,$G,$B) = array($V,$M,$N);
            break;
    }
    return array((int)(255*$R), (int)(255*$G), (int)(255*$B));
}

/**
 * Returns a priority as a value.
 * @param string $priority One of [HIGHEST, VERYHIGH, HIGH, MEDIUM, LOW, 
 *              VERYLOW, LOWEST].
 * @return int An integer for determining what radius to use for a hue.
 */
function priorityToValue($priority) {
    if ($priority == "HIGHEST") return 0;
    if ($priority == "VERYHIGH") return 1;
    if ($priority == "HIGH") return 2;
    if ($priority == "MEDIUM") return 3;
    if ($priority == "LOW") return 4;
    if ($priority == "VERYLOW") return 5;
    if ($priority == "LOWEST") return 6;
    return -1;
}

/**
 * Takes a priority and returns it as an RGB value for creating a bubble.
 * @param string The priority to convert.
 */
function priorityToRgb($priority) {
    $val = priorityToValue($priority);
    if ($val == -1) return array(175, 175, 175);
    return hsvToRgb(array($val/21.0, 1, 1));
}

header("Content-Type: image/png");

$priority = 'NONE';
if (isset($_GET['priority'])) {
    $priority = strtoupper($_GET['priority']);
}

$len = 20;
if (isset($_GET['len'])) {
    $len = (int)$_GET['len'];
}
$image = imagecreatetruecolor($len+1,$len+1);
$rgbVal = priorityToRgb($priority);
$color = imagecolorallocate($image, $rgbVal[0], $rgbVal[1], $rgbVal[2]);
$black = imagecolorallocate($image, 0, 0, 0);

imagecolortransparent($image, $black);
imagefilledellipse($image,$len/2,$len/2,$len,$len,$color);

imagepng($image);
imagedestroy($image);
?>
