<?php
/**
* @package WOW WordPress 
* @subpackage Theme by Nguyen Pham
* https://baonguyenyam.github.io
* @since 2021
*/

// https://docsv3.redux.io/
// https://devs.redux.io/core-fields/

if ( ! class_exists( 'Redux' ) ) {
	return;
}

require_once 'options/options.php';
require_once 'options/function.php';
require_once 'options/post-page.php';
// INI TABS
require_once 'tabs/tab-css-js.php';
require_once 'tabs/tab-global.php';
require_once 'tabs/tab-layout.php';
require_once 'tabs/tab-header.php';
require_once 'tabs/tab-footer.php';
require_once 'tabs/tab-copyright.php';
require_once 'tabs/tab-blog.php';
require_once 'tabs/tab-page.php';
require_once 'tabs/tab-search.php';
require_once 'tabs/tab-typography.php';
require_once 'tabs/tab-social.php';
require_once 'tabs/tab-license.php';

// INI POST 
require_once 'posts/post-type.php';
require_once 'posts/post-sidebar.php';
require_once 'posts/post-relates.php';
require_once 'posts/post-series.php';
require_once 'posts/post-adv.php';

// require_once 'tabs/tab-demo.php'; // This one for demo 
// CALL ACTION 
require_once 'options/helps.php';
require_once 'options/docs.php';
require_once 'options/init.php';
