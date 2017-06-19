<?php

/**
 * Customer account controller
 */
require_once 'Mage/Customer/controllers/AccountController.php';

class Md_Customer_AccountController extends Mage_Customer_AccountController {

    public function ajaxAction() {
        echo 'ajax!!';die;
    }

    public function testAction() {
        echo 'test222';die;
    }

    public function loginAction() {
        echo 'index';die;
    }

}