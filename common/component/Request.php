<?php
/**
 * Created by PhpStorm.
 * User: amane
 * Date: 5/29/2016
 * Time: 4:15 PM
 */

namespace common\component;


class Request extends \yii\web\Request {
    public $web;
    public $adminUrl;

    public function getUrl(){

        return  str_replace($this->adminUrl, $this->web, parent::getUrl());

    }

    /*
        If you don't have this function, the admin site will 404 if you leave off
        the trailing slash.

        E.g.:

        Wouldn't work:
        site.com/admin

        Would work:
        site.com/admin/

        Using this function, both will work.
    */
    public function resolvePathInfo(){
        if($this->getUrl() === $this->adminUrl){
            return "";
        }else{
            return parent::resolvePathInfo();
        }
    }
}