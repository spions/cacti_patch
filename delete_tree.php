#!/usr/bin/php -q
<?php

/* do NOT run this script through a web browser */
if (!isset($_SERVER["argv"][0]) || isset($_SERVER['REQUEST_METHOD'])  || isset($_SERVER['REMOTE_ADDR'])) {
        die("<br><strong>This script is only meant to run at the command line.</strong>");
}

$no_http_headers = true;

include(dirname(__FILE__)."/../include/global.php");
include_once($config["base_path"]."/lib/api_automation_tools.php");
include_once($config["base_path"].'/lib/tree.php');
include_once($config["base_path"].'/lib/api_tree.php');

/* process calling arguments */
$parms = $_SERVER["argv"];
array_shift($parms);

if (sizeof($parms)) {
        /* setup defaults */
        $treeId     = 0;   # When creating a node, it has to go in a tree


        foreach($parms as $parameter) {
                @list($arg, $value) = @explode("=", $parameter);

                switch ($arg) {
                case "--tree-id":
                        $treeId = $value;
                        break;

                default:
                        echo "ERROR: Invalid Argument: ($arg)\n\n";
                        display_help();
                        exit(1);
                }
        }

        if ($treeId>0) {
                echo "Delete Tree ".$treeId;
                delete_branch($treeId);
                exit(0);
        } else {
                echo "ERROR: Unknown type: ($type)\n";
                display_help();
                exit(1);
        }
} else {
        display_help();
        exit(0);
}

function display_help() {
        echo "Delete Tree Script 1.0, Copyright 2004-2012 - The Cacti Group\n\n";
        echo "A simple command line utility to add objects to a tree in Cacti\n\n";
        echo "usage: delete_tree.php  --tree-id=[ID]\n\n";
}

?>
