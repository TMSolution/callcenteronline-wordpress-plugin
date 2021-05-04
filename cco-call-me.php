<?php

/*
Plugin Name: Call Center Online
Plugin URI: https://callcenteronline.pl
Description: A simple-to-use plugin that works with the Call Center Online platform. Adds a button to collect contacts on your website.
Version: 1.0
Author: Jacek Łoziński
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

include_once("includes/const.php");
include_once("includes/api.php");
include_once("admin/cco-call-me-admin.php");
include_once("content/cco-call-me-content.php");


class cco_call_me
{

    private $options;

    public function __construct()
    {
        
        
        $admin = new cco_call_me_admin();
        $admin->show();
        $content = new cco_call_me_content();
        $content->show();

        
        
    }

}
$cco_settings_page = new cco_call_me();

?>
