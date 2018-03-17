<?php
function resizeImage($im, $maxwidth, $maxheight, $name, $filetype)
{
    $pic_width  = imagesx($im);
    $pic_height = imagesy($im);

    if ($pic_width <= 0 || $pic_height <= 0) {return false;} //不合法的长宽

    if ($maxwidth <= 0 || $maxheight <= 0) {return false;} //不合法的限制长宽

    if ($pic_width <= $maxwidth && $pic_height <= $maxheight) {
        $name = $name . $filetype;
        imagejpeg($im, $name);
        return true; //长宽均已小于限制高度，不必操作
    } else {
        $widthratio  = $maxwidth / $pic_width;
        $heightratio = $maxheight / $pic_height;
        if ($widthratio < $heightratio) {
            $ratio = $widthratio;
        } else {
            $ratio = $heightratio;
        }
        $newwidth  = $pic_width * $ratio;
        $newheight = $pic_height * $ratio;

        if (function_exists("imagecopyresampled")) {
            $newim = imagecreatetruecolor($newwidth, $newheight); //PHP系统函数
            imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height); //PHP系统函数
        } else {
            $newim = imagecreate($newwidth, $newheight);
            imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
        }
        $name = $name . $filetype;
        imagejpeg($newim, $name);
        imagedestroy($newim);
    }
}
//使用方法：
/*
$im        = imagecreatefromjpeg("./test.jpg"); //参数是图片的存方路径
$maxwidth  = "3000"; //设置图片的最大宽度
$maxheight = "1000"; //设置图片的最大高度
$name      = "test_new"; //图片的名称，随便取吧
$filetype  = ".jpg"; //图片类型
resizeImage($im, $maxwidth, $maxheight, $name, $filetype); //调用上面的函数
 */
