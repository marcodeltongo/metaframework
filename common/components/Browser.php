<?php

/**
 * Browser detection using php_browscap.ini file.
 *
 * @see http://browsers.garykeith.com/downloads.asp for updated file.
 *
 * GET INFO:
 *
 * get_info() - returns array of all info
 * get_name() - returns just the name
 * get_version() - returns version and minor version (3.2)
 *
 * CONDITIONAL STATEMENTS INCLUDED:
 *
 * $version is optional. Include a number to test a specific one, or leave blank to test for any version.
 *
 * is_firefox ($version)
 * is_safari ($version)
 * is_chrome ($version)
 * is_opera ($version)
 * is_ie ($version)
 *
 * is_iphone ($version)
 * is_ipad ($version)
 * is_ipod ($version)
 *
 * is_mobile ()
 *
 * supports_javascript()
 * supports_cookies()
 * supports_css()
 *
 *
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/mit-license Licensed under the MIT license.
 * @version 1.0
 */
class Browser extends CApplicationComponent
{
    /**
     * Parsed browscap.ini
     *
     * @var array
     */
    private $_info = null;

    /**
     * Initialize
     */
    public function init()
    {
        parent::init();
    }

//  GET BROWSER INFO

    public function get_info()
    {
        if ($this->_info === null) {
            $agent = $_SERVER['HTTP_USER_AGENT'];

            $browscap = VENDORS_DIR . 'Browscap/php_browscap.ini';
            if (!is_file(realpath($browscap))) {
                return array('error' => 'No browscap ini file found.');
            }
            $agent = $agent ? $agent : $_SERVER['HTTP_USER_AGENT'];
            $yu = array();
            $q_s = array("#\.#", "#\*#", "#\?#");
            $q_r = array("\.", ".*", ".?");

            if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
                $brows = parse_ini_file(realpath($browscap), true, INI_SCANNER_RAW);
            } else {
                $brows = parse_ini_file(realpath($browscap), true);
            }

            foreach ($brows as $k => $t) {
                if (fnmatch($k, $agent)) {
                    $yu['browser_name_pattern'] = $k;
                    $pat = preg_replace($q_s, $q_r, $k);
                    $yu['browser_name_regex'] = strtolower("^$pat$");
                    foreach ($brows as $g => $r) {
                        if ($t['Parent'] == $g) {
                            foreach ($brows as $a => $b) {
                                if ($r['Parent'] == $a) {
                                    $yu = array_merge($yu, $b, $r, $t);
                                    foreach ($yu as $d => $z) {
                                        $l = strtolower($d);
                                        $hu[$l] = $z;
                                    }
                                }
                            }
                        }
                    }
                    break;
                }
            }

            $this->_info = $hu;
        }

        return $this->_info;
    }

    public function get_name()
    {
        $browserInfo = $this->get_info();
        return $browserInfo['browser'];
    }

    public function get_version()
    {
        $browserInfo = $this->get_info();
        return $browserInfo['version'];
    }

//  BROWSERS

    public function is_firefox($version = false)
    {
        $browserInfo = $this->get_info();
        if (isset($browserInfo['browser']) and $browserInfo['browser'] == 'Firefox') {
            return ((false === $version) or ($browserInfo['majorver'] == $version));
        } else {
            return false;
        }
    }

    public function is_safari($version = false)
    {
        $browserInfo = $this->get_info();
        if (isset($browserInfo['browser']) and $browserInfo['browser'] == 'Safari') {
            return ((false === $version) or ($browserInfo['majorver'] == $version));
        } else {
            return false;
        }
    }

    public function is_chrome($version = false)
    {
        $browserInfo = $this->get_info();
        if (isset($browserInfo['browser']) and $browserInfo['browser'] == 'Chrome') {
            return ((false === $version) or ($browserInfo['majorver'] == $version));
        } else {
            return false;
        }
    }

    public function is_opera($version = false)
    {
        $browserInfo = $this->get_info();
        if (isset($browserInfo['browser']) and $browserInfo['browser'] == 'Opera') {
            return ((false === $version) or ($browserInfo['majorver'] == $version));
        } else {
            return false;
        }
    }

    public function is_ie($version = false)
    {
        $browserInfo = $this->get_info();
        if (isset($browserInfo['browser']) and $browserInfo['browser'] == 'IE') {
            return ((false === $version) or ($browserInfo['majorver'] == $version));
        } else {
            return false;
        }
    }

//  MOBILE

    public function is_mobile()
    {
        $browserInfo = $this->get_info();
        return (isset($browserInfo['ismobiledevice']) and $browserInfo['ismobiledevice'] == 1);
    }

    public function is_android($version = false)
    {
        $browserInfo = $this->get_info();
        if (preg_match("/Android/", $browserInfo['browser_name_pattern'], $matches) || strpos($_SERVER['HTTP_USER_AGENT'], 'Android')) {
            return ((false === $version) or ($browserInfo['majorver'] == $version));
        } else {
            return false;
        }
    }

    public function is_iphone($version = false)
    {
        $browserInfo = $this->get_info();
        if (( isset($browserInfo['browser']) and $browserInfo['browser'] == 'iPhone' ) || strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')) {
            return ((false === $version) or ($browserInfo['majorver'] == $version));
        } else {
            return false;
        }
    }

    public function is_ipad($version = false)
    {
        $browserInfo = $this->get_info();
        if (preg_match("/iPad/", $browserInfo['browser_name_pattern'], $matches) || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')) {
            return ((false === $version) or ($browserInfo['majorver'] == $version));
        } else {
            return false;
        }
    }

    public function is_ipod()
    {
        $browserInfo = $this->get_info();
        if (preg_match("/iPod/", $browserInfo['browser_name_pattern'], $matches)) {
            return ((false === $version) or ($browserInfo['majorver'] == $version));
        } else {
            return false;
        }
    }

//  TEST FOR FEATURES

    public function supports_javascript()
    {
        $browserInfo = $this->get_info();
        return (isset($browserInfo['javascript']) and $browserInfo['javascript'] == '1');
    }

    public function supports_cookies()
    {
        $browserInfo = $this->get_info();
        return (isset($browserInfo['cookies']) and $browserInfo['cookies'] == '1');
    }

    public function supports_css()
    {
        $browserInfo = $this->get_info();
        return (isset($browserInfo['supportscss']) and $browserInfo['supportscss'] == '1');
    }

}