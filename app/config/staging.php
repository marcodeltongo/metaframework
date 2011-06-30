<?php

/*
 * Inherits from application main.
 */
$appMainConfig = require APP_DIR . 'config/main.php';

/**
 * Application development configuration.
 */
return CMap::mergeArray(array(
), $appMainConfig);