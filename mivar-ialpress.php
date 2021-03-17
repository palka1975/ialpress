<?php

/**
* Plugin Name: IalPress
* Plugin URI: https://mivar.in/
* Description: Plugin di MiVar per la gestione dell'interfaccia IALMAN su WordPress
* Version: 1.0
* Author: Mivar, Inc.
* Author URI: http://mivar.in/
*/

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
include "classes/class-ialpress-richinfo.php";
include "classes/class-ialman-ops.php";
include "classes/class-ialpress-domande-list-table.php";
include "backend-pages.php";
include "tables.php";

// installation routines
register_activation_hook( __FILE__, 'mivarip_install' );