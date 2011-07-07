<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php echo $this->pageTitle; ?></title>
    <meta name="language" content="<?php echo $this->language; ?>" />
    <meta name="keywords" content="<?php echo $this->metaKeywords; ?>" />
    <meta name="description" content="<?php echo $this->metaDescription; ?>" />

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?php echo BASE_URL ?>css/style.css">
	<link rel="stylesheet" href="<?php echo BASE_URL ?>css/ui/custom-aluminum/style.css">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:light,lightitalic,regular,regularitalic,600,600italic,bold,bolditalic,800,800italic&v1' rel='stylesheet' type='text/css'>
    <link rel="shortcut icon" href="<?php echo BASE_URL ?>favicon.ico">

	<!-- JavaScript at the bottom for fast page loading, only strictly needed here. -->
	<script src="<?php echo BASE_URL ?>js/libs/modernizr-2.0.4-all.min.js"></script>

	<!-- Place favicon.ico and apple-touch-icon.png in root: mathiasbynens.be/notes/touch-icons -->
</head>
<body class="<?php echo implode(',', $this->bodyClasses), ' app-', $this->id, '-', $this->action->id ?>">
<div id="container">
<header>
	<h1 class="title"><?php echo $this->pageTitle; ?></h1>
<?php
    /*
     * Navigation
     */
    if (false !== $this->menu) {
?>
    <nav id="navigation" rel="navigation">
<?php
    // Use array from controller if set or locally inlined items.
    $menuItems = (is_array($this->menu)) ? $this->menu : array(
        array('label' => '<img src="' . BASE_URL . 'images/home.png" />', 'url' => BASE_URL, 'itemOptions' => array('class' => 'icon')),
        array('label' => Yii::t('menu', 'User'), 'items' => array(
                array('label' => Yii::t('menu', 'Login'), 'url' => array('/user/login'), 'visible' => Yii::app()->user->isGuest),
                array('label' => Yii::t('menu', 'Logout'), 'url' => array('/user/logout'), 'visible' => !Yii::app()->user->isGuest),
        )),
    );
    $this->widget('zii.widgets.CMenu', array(
        'encodeLabel' => false,
        'items' => $menuItems,
    ));
?>
    </nav>
<?php
    } // -Navigation
?>
</header>
<div id="main" role="main">
<?php
    /*
     * Breadcrumbs
     */
    if (false !== $this->breadcrumbs) {
?>
    <nav id="breadcrumbs" rel="breadcrumbs">
<?php
    $this->widget('zii.widgets.CBreadcrumbs', array(
        // 'homeLink' => false,
        'homeLink' => CHtml::link(Yii::t('menu', 'Home'), BASE_URL),
        'separator' => ' &raquo; ',
        'links' => $this->breadcrumbs,
    ));
?>
    </nav>
<?php
    } // -Breadcrumbs
?>
<?php
    /*
     * View output
     */
    echo trim($content);
?>
</div><!-- end of #main -->
<footer>
	<span class="app-name"><?php echo Yii::app()->name; ?></span>
</footer>
</div><!--! end of #container -->

<?php $this->widget('common.widgets.FlashMessages', array()); ?><!-- flashes -->

<!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if necessary -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="<?php echo BASE_URL ?>js/libs/jquery-1.6.2.js">\x3C/script>')</script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/jquery-ui.min.js"></script>
<script>window.jQuery.effects || document.write('<script src="<?php echo BASE_URL ?>js/libs/jquery-ui-1.8.14.js">\x3C/script>')</script>
<!--[if (gte IE 6) & (lte IE 8) & (!IEMobile)]>
<script src="<?php echo BASE_URL ?>js/libs/selectivizr-1.0.2-min.js"></script>
<![endif]-->
<!--[if lt IE 7 ]>
<script src="<?php echo BASE_URL ?>js/libs/DD_belatedPNG-0.0.8a.min.js"></script>
<script>DD_belatedPNG.fix('a, li, img, div, .png_bg'); //goo.gl/mZiyb </script>
<![endif]-->
<script src="<?php echo BASE_URL ?>js/plugins.js"></script>
<script src="<?php echo BASE_URL ?>js/scripts.js"></script>
<!-- end scripts-->

<?php
    /*
     * Google Analytics
     */
    if (false !== $this->param('google-analytics', false)) {
?>
<script>
	var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview'],['_trackPageLoadTime']];
	(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.async=1;
	g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
	s.parentNode.insertBefore(g,s)}(document,'script'));
</script>
<?php
    } // -Analytics
?>
</body>
</html>