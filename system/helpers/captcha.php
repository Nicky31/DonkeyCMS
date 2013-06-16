<?php
/*
 * Fonctions de génération d'un captcha
 * @author http://www.petit-kiwi.com/php-creation-captcha-anti-spam
 */

define('PATH_FONTS', ASSETS_PATH . '/shared/fonts');
define('NB_LINES', 10); // Nombre de lignes barrant le captcha

function getCode($length)
{
    $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'; 
    $code = '';
    
    for ($i=0; $i<$length; $i++) 
        $code .= $chars{ mt_rand( 0, strlen($chars) - 1 ) };
    
    return $code; 
}

function random($tab)
{
    return $tab[array_rand($tab)];
}

function getCaptcha($sessionName = 'captcha')
{
    $code = getCode(5);
    $_SESSION[$sessionName] = md5($code);
    
    $char1 = substr($code,0,1);
    $char2 = substr($code,1,1);
    $char3 = substr($code,2,1);
    $char4 = substr($code,3,1);
    $char5 = substr($code,4,1);
    
    $fonts = glob(PATH_FONTS .'/*.ttf');
    $image = imagecreatefrompng(img_url('captcha_1.png'));
    
    $colors = array(imagecolorallocate($image, 131, 154, 255),
                    imagecolorallocate($image,  89, 186, 255),
                    imagecolorallocate($image, 155, 190, 214),
                    imagecolorallocate($image, 255, 128, 234),
                    imagecolorallocate($image, 255, 123, 123));
    
    $emboss = array(array(2, 0, 0), array(0, -1, 0), array(0, 0, -1));
    imageconvolution($image, $emboss, 1, 127);
    
    imagettftext($image, 28, -10, 0, 37, random($colors), random($fonts), $char1);
    imagettftext($image, 28, 20, 37, 37, random($colors), random($fonts), $char2);
    imagettftext($image, 28, -35, 55, 37, random($colors),random($fonts), $char3);
    imagettftext($image, 28, 25, 100, 37, random($colors),random($fonts), $char4);
    imagettftext($image, 28, -15, 120, 37, random($colors),random($fonts), $char5);

    for($i = 0; $i < NB_LINES; $i++)
    {
        $x1 = mt_rand(0, imagesx($image));
        $y1 = mt_rand(0, imagesy($image));
        $x2 = mt_rand(0, imagesx($image));
        $y2 = mt_rand(0, imagesy($image));
        
        imageline($image, $x1, $y1, $x2, $y2, random($colors));
    }

    ob_start();
    imagepng($image);
    $imageData = ob_get_contents();
    ob_end_clean();
    // on libère toute la mémoire associée à l'image
    imagedestroy($image);
    
    return '<img alt="Captcha" title="Veuillez recopier le captcha ci-dessous" src="data:image/png;base64,' . base64_encode($imageData) . '" />';
}