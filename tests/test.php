<?php
/**
 * Created by PhpStorm.
 * User: banma
 * Date: 2019/12/31
 * Time: 13:38
 */
// 引入autoload.php文件，用于自动加载vendor中的库
require '../vendor/autoload.php';

// use HelloWorld类
use pack\HelloWorld;

// 实例化HelloWorld类
$obj = new HelloWorld();

// 使用对象调用sayHello()方法
$obj->sayHello();

echo PHP_EOL . "--------" . PHP_EOL;

//使用类名调用sayHello()方法(静态调用)
HelloWorld::sayHelloStatic();