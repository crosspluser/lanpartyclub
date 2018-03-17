<?php
require 'flight/Flight.php';
require 'class/ResponseModel.class.php';
require 'class/ImageThumb.class.php';
require 'class/ItemClass.class.php';
require 'class/Item.class.php';
require 'class/Album.class.php';

//1.欢迎
Flight::route('/', function () {
    echo 'hello lanpartyclub API!';
});

//2.获取单个类别
Flight::route('/class/get', function () {
    $id                = Flight::request()->query['id'];
    $object_item_class = new ItemClass();
    Flight::jsonp($object_item_class->getClass($id), 'callback');
});

//3.获取全部类别
Flight::route('/class/get/all', function () {
    $object_item_class = new ItemClass();
    Flight::jsonp($object_item_class->getClassAll(), 'callback');
});

//4.获取子类别
Flight::route('/class/get/child', function () {
    $id                = Flight::request()->query['id'];
    $object_item_class = new ItemClass();
    Flight::jsonp($object_item_class->getClassChild($id), 'callback');
});

//5.新增单个类别
Flight::route('/class/post', function () {
    $name              = Flight::request()->query['name'];
    $pid               = Flight::request()->query['pid'];
    $object_item_class = new ItemClass();
    Flight::jsonp($object_item_class->postClass($name, $pid), 'callback');
});

//6.修改单个类别
Flight::route('/class/put', function () {
    $id                = Flight::request()->query['id'];
    $name              = Flight::request()->query['name'];
    $pid               = Flight::request()->query['pid'];
    $object_item_class = new ItemClass();
    Flight::jsonp($object_item_class->putClass($id, $name, $pid), 'callback');
});

//7.删除单个类别
Flight::route('/class/delete', function () {
    $id                = Flight::request()->query['id'];
    $object_item_class = new ItemClass();
    Flight::jsonp($object_item_class->deleteClass($id), 'callback');
});

//8.获取单个品项 （基本信息+全部照片+全部详情）
Flight::route('/item/get', function () {
    $id          = Flight::request()->query['id'];
    $object_item = new Item();
    Flight::jsonp($object_item->get_item($id), 'callback');
});

//9.获取全部品项
Flight::route('/item/get/all', function () {
    $object_item = new Item();
    Flight::jsonp($object_item->get_item_all(), 'callback');
});

//10.按类别获取多个品项
Flight::route('/item/get/class', function () {
    $id          = Flight::request()->query['id'];
    $object_item = new Item();
    Flight::jsonp($object_item->get_item_class($id), 'callback');
});

//11.按类别随机获取单个品项
Flight::route('/item/get/class/random', function () {
    $id          = Flight::request()->query['id'];
    $object_item = new Item();
    Flight::jsonp($object_item->get_item_class_random($id), 'callback');
});

//12.新增单个品项
Flight::route('/item/post', function () {
    $title       = Flight::request()->query['title'];
    $brief       = Flight::request()->query['brief'];
    $class       = Flight::request()->query['class'];
    $object_item = new Item();
    Flight::jsonp($object_item->post_item($title, $brief, $class), 'callback');
});

//13.修改单个品项
Flight::route('/item/put', function () {
    $id          = Flight::request()->query['id'];
    $title       = Flight::request()->query['title'];
    $brief       = Flight::request()->query['brief'];
    $class       = Flight::request()->query['class'];
    $object_item = new Item();
    Flight::json($object_item->put_item($id, $title, $brief, $class));
});

//14.删除单个品项
Flight::route('/item/delete', function () {
    $id          = Flight::request()->query['id'];
    $object_item = new Item();
    Flight::jsonp($object_item->delete_item($id), 'callback');
});

//15.新增单个品项详情
Flight::route('/item/detail/post', function () {
    $id          = Flight::request()->query['id'];
    $title       = Flight::request()->query['title'];
    $content     = Flight::request()->query['content'];
    $object_item = new Item();
    Flight::jsonp($object_item->post_item_detail($id, $title, $content), 'callback');
});

//16.修改单个品项详情
Flight::route('/item/detail/put', function () {
    $id          = Flight::request()->query['id'];
    $title       = Flight::request()->query['title'];
    $content     = Flight::request()->query['content'];
    $object_item = new Item();
    Flight::json($object_item->put_item_detail($id, $title, $content));
});

//17.删除单个品项详情
Flight::route('/item/detail/delete', function () {
    $id          = Flight::request()->query['id'];
    $object_item = new Item();
    Flight::jsonp($object_item->delete_item_detail($id), 'callback');
});

//18.新增单张品项照片
Flight::route('/item/photo/post', function () {
    $id = Flight::request()->data['id'];
    print_r($_FILES["file"]["size"]);
    $file        = $_FILES["file"];
    $object_item = new Item();
    Flight::json($object_item->post_item_photo($id, $file));
});

//19.删除单张品项照片
Flight::route('/item/photo/delete', function () {
    $id          = Flight::request()->query['id'];
    $object_item = new Item();
    Flight::jsonp($object_item->delete_item_photo($id), 'callback');
});

//20.获取全部相册
Flight::route('/album/get/all', function () {
    $a = new Album();
    Flight::jsonp($a->getAlbumAll(), 'callback');
});

//21.获取相册·随机五个
Flight::route('/album/get/random', function () {
    $a = new Album();
    Flight::jsonp($a->getAlbumRandom(), 'callback');
});

//22.获取相册
Flight::route('/album/get', function () {
    $id = Flight::request()->query['id'];
    $a  = new Album();
    Flight::jsonp($a->getAlbum($id), 'callback');
});

//23.新增单个相册
Flight::route('/album/post', function () {
    $title = Flight::request()->query['title'];
    $a     = new Album();
    Flight::jsonp($a->postAlbum($title), 'callback');
});

//24.修改单个相册
Flight::route('/album/put', function () {
    $id     = Flight::request()->query['id'];
    $title  = Flight::request()->query['title'];
    $object = new Album();
    Flight::jsonp($object->putAlbum($id, $title), 'callback');
});

//25.删除单个相册
Flight::route('/album/delete', function () {
    $id     = Flight::request()->query['id'];
    $object = new Album();
    Flight::jsonp($object->deleteAlbum($id), 'callback');
});

//26.上传单张相册照片
Flight::route('/album/photo/post', function () {
    $id   = Flight::request()->data['id'];
    $file = $_FILES["file"];
    //print_r($_FILES["file"]["size"]);
    $object = new Album();
    Flight::json($object->postAlbumPhoto($id, $file));
});

//27.删除单张相册照片
Flight::route('/album/photo/delete', function () {
    $id     = Flight::request()->query['id'];
    $object = new Album();
    Flight::jsonp($object->deleteAlbumPhoto($id), 'callback');
});

Flight::start();

//回收站
//header('content-type:application/json;charset=utf8');
