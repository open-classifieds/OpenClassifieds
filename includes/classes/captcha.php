<?php

/**
 * ultra light captcha class
 *
 * @package     Open Classifieds
 * @subpackage  Core
 * @category    Helper
 * @author      Chema Garrido <chema@garridodiaz.com>
 * @license     GPL v3
 */

class captcha{

    /**
     * generates the image for the captcha
     * @param string $name, used in the session
     * @param int $width
     * @param int $height
     * @param string $baseList
     */
    public static function image($name='',$width=120,$height=40,$baseList = '0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        session_start();

        $length = mt_rand(3,5);//code length
        $lines  =  mt_rand(1,5);//to make the image dirty
        $image  = @imagecreate($width, $height) or die('Cannot initialize GD!');
        $code   = ''; //code generated saved at session

        //base image with dirty lines
        for( $i=0; $i<$lines; $i++ ) {
           imageline($image, 
                 mt_rand(0,$width), mt_rand(0,$height), 
                 mt_rand(0,$width), mt_rand(0,$height), 
                 imagecolorallocate($image, mt_rand(150,255), mt_rand(150,255), mt_rand(150,255)));
        }

        //writting the chars
        for( $i=0, $x=0; $i<$length; $i++ ) {
           $actChar = substr($baseList, rand(0, strlen($baseList)-1), 1);
           $x += 10 + mt_rand(0,10);
           imagechar($image, mt_rand(4,5), $x, mt_rand(5,20), $actChar, 
              imagecolorallocate($image, mt_rand(0,155), mt_rand(0,155), mt_rand(0,155)));
           $code .= strtolower($actChar);
        }
           
        // prevent client side  caching
        header("Expires: Wed, 1 Jan 1997 00:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", FALSE);
        header("Pragma: no-cache");
        header('Content-Type: image/jpeg');
        imagejpeg($image);
        imagedestroy($image);

        $_SESSION['captcha'.$name] = $code;
    }

    
    /**
     * 
     * @param string $name for the session
     */
    public static function url($name='')
    {
        return SITE_URL.'/content/captcha.php?salt='.$name;        
    }
    
    /**
     * check if its valid or not
     * @param string $name for the session
     * @return boolean 
     */
    public static function check($name='')
    {
        if (CAPTCHA)
        {
            if  ($_SESSION['captcha'.$name]==strtolower(OC::$_POST['captcha']))
            {
                $_SESSION['captcha'.$name] = '';
                return TRUE;
            }   
            else return FALSE;
        }
        else return TRUE;
    }
    

}