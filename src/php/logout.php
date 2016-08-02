<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 7/29/16
 * Time: 11:12 PM
 */
    session_start();
    session_unset();
    session_destroy();
    echo "SUCCESS";