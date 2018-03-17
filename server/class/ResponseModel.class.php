<?php

//返回结果包装：错误/正确
function ResponseModel($arrData = array(), $status = -1, $message = '状态未定义')
{
    $arrResponse           = array();
    $arrResponse['status'] = $status;
    $arrResponse['info']   = $message;
    if (!empty($arrData)) {
        $arrResponse['data'] = $arrData;
    }

    return $arrResponse;
}

function dowith_sql($str)
{
    $str = str_replace("and", "", $str);
    $str = str_replace("execute", "", $str);
    $str = str_replace("update", "", $str);
    $str = str_replace("count", "", $str);
    $str = str_replace("chr", "", $str);
    $str = str_replace("mid", "", $str);
    $str = str_replace("master", "", $str);
    $str = str_replace("truncate", "", $str);
    $str = str_replace("char", "", $str);
    $str = str_replace("declare", "", $str);
    $str = str_replace("select", "", $str);
    $str = str_replace("create", "", $str);
    $str = str_replace("delete", "", $str);
    $str = str_replace("insert", "", $str);
    $str = str_replace("'", "", $str);
    $str = str_replace('"', "", $str);
    $str = str_replace("", "", $str);
    $str = str_replace(" or ", "", $str);
    $str = str_replace(" = ", "", $str);
    $str = str_replace(" % 20", "", $str);
    //echo $str;
    return $str;
}

function saveImage($file, $filename)
{
    $data = [];
    /*不再支持的格式
    $file["type"] == "image/gif") ||
     */

    if ((($file["type"] == "image/jpeg") || ($file["type"] == "image/pjpeg") || ($file["type"] == "image/x-png") || ($file["type"] == "image/png")) && ($file["size"] < 10000000)) {
        if ($file["error"] > 0) {
            $data['status']  = false;
            $data['message'] = "文件上传错误 " . $file["error"];
            return $data;
        } else {
            $fileType = getExtension($file["tmp_name"]);
            if (file_exists("../upload/lanpartyclub/images/album/" . $filename . '.' . $fileType)) {
                $data['status']  = false;
                $data['message'] = $file["name"] . ' 文件已经存在';
                return $data;
            } else {
                //move_uploaded_file($file["tmp_name"],"../upload/" . $file["name"]);
                $file_path     = "../upload/lanpartyclub/images/album/";
                $file_name_all = $filename . '.' . $fileType;
                move_uploaded_file($file["tmp_name"], $file_path . $file_name_all);
                //存储缩略图
                //$im        = imagecreatefromjpeg($file_path . $file_name_all); //参数是图片的存方路径
                //$maxwidth  = "600"; //设置图片的最大宽度
                //$maxheight = "400"; //设置图片的最大高度
                //$name      = $file_path . "thumb/" . $filename; //图片的名称，随便取吧
                //$filetype  = ".jpg"; //图片类型
                //resizeImage($im, $maxwidth, $maxheight, $name, $filetype);
                ///.存储缩略图
                $data['status']  = true;
                $data['message'] = "成功上传: " . $file["name"] . " 新文件名: " . $filename . '.' . $fileType . " 大小: " . ($file["size"] / 1024) . "Kb 临时文件名: " . $file["tmp_name"];
                return $data;
            }
        }
    } else {
        $data['status']  = false;
        $data['message'] = '不合法的文件 ' . $file["name"];
        return $data;
    }
}

function guid()
{
    if (function_exists('com_create_guid')) {
        return com_create_guid();
    } else {
        mt_srand((double) microtime() * 10000); //optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45); // "-"
        $uuid   = chr(123) // "{"
         . substr($charid, 0, 8) . $hyphen
        . substr($charid, 8, 4) . $hyphen
        . substr($charid, 12, 4) . $hyphen
        . substr($charid, 16, 4) . $hyphen
        . substr($charid, 20, 12)
        . chr(125); // "}"
        return $uuid;
    }
}

function getExtension($file)
{
    $tempfile = @fopen($file, "rb");
    $bin      = fread($tempfile, 2); //只读2字节
    fclose($tempfile);
    $strInfo  = @unpack("C2chars", $bin);
    $typeCode = intval($strInfo['chars1'] . $strInfo['chars2']);
    $fileType = '';
    switch ($typeCode) {
        // 6677:bmp 255216:jpg 7173:gif 13780:png 7790:exe 8297:rar 8075:zip tar:109121 7z:55122 gz 31139
        case '255216':
            $fileType = 'jpg';
            break;
        case '7173':
            $fileType = 'gif';
            break;
        case '13780':
            $fileType = 'png';
            break;
        default:
            $fileType = 'unknown';
    }
    return $fileType;
}
