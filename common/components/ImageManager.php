<?php

/**
 * Manages images aliases and acts as a proxy to class.upload.php
 *
 * @uses http://www.verot.net/php_class_upload.htm
 *
 * @author Marco Del Tongo <info@marcodeltongo.com>
 * @copyright Copyright (c) 2011, Marco Del Tongo
 *
 * @license http://opensource.org/licenses/gpl-2.0.php Licensed under the GPLv2 license.
 * @version 1.0
 */
class ImageManager extends CApplicationComponent
{
    /**
     * Language name in 'en_EN' format
     *
     * @var string
     */
    private $_language;
    /**
     * class.upload.php library instance
     *
     * @var object
     */
    private $_handle;
    /**
     * Local upload path.
     *
     * @var string
     */
    private $_local_path;
    /**
     * Public path
     *
     * @var string
     */
    private $_public_url;
    /**
     * Image aliases
     */
    public $aliases;

    /**
     * init function
     */
    public function init()
    {
        parent::init();

        $this->_local_path = realpath(Yii::app()->getBasePath() . Yii::app()->params['basePhotoPath']) . DIRECTORY_SEPARATOR;
        $this->_public_url = Yii::app()->params['basePhotoURL'];
        $dir = dirname(__FILE__);
        $alias = md5($dir);
        Yii::setPathOfAlias($alias, $dir);
        Yii::import($alias . '.upload');
    }

    /**
     * Language set function
     *
     * @param string $lang
     */
    public function setLanguage($lang)
    {
        $this->_language = $lang;
    }

    /**
     * Main extension loader
     *
     * @param (string||$_FILE) $image
     *
     * @return upload
     */
    public function load($image)
    {
        $this->setLanguage(Yii::app()->getLanguage());
        $this->_handle = new upload($image, $this->_language);
        return $this->_handle;
    }

    /**
     * Main extension loader for uploads
     *
     * @param ($_FILE) $image
     *
     * @return boolean
     */
    public function uploaded($image)
    {
        $this->load($image);
        return $this->_handle->uploaded;
    }

    /**
     * Linearize $_FILES array from FormModel classes
     *
     * @param array $src
     * @return array
     */
    public function normalize_files_array($src)
    {
        $files = array();
        if (!array_key_exists('name', $src)) {
            return $src;
        }
        foreach ($src as $k => $l) {
            foreach ($l as $i => $v) {
                if (!array_key_exists($i, $files))
                    $files[$i] = array();
                $files[$i][$k] = $v;
            }
        }
        return $files;
    }

    /**
     * Main extension loader for uploads
     *
     * @param mixed $image $_FILES array usually
     * @param string $field Optional key for $image array
     * @param string $path Where to save output
     * @param boolean $safe Wheter to change name to ensure uniqueness
     *
     * @return boolean false or filename
     */
    public function upload($image, $field = false, $path = false, $safe = true)
    {
        $this->setLanguage(Yii::app()->getLanguage());

        /*
         * No path, save in "original" folder.
         */
        if (false === $path) {
            $path = $this->_local_path . 'original';
        }

        /*
         * Find imput
         */
        $image = $this->normalize_files_array($image);
        if (false !== $field) {
            $image = $image[$field];
        } else {
            $image = array_shift($image);
        }

        /*
         * Something to process ?
         */
        $this->_handle = new upload($image, $this->_language);
        if ($this->_handle->uploaded and $this->_handle->file_is_image) {
            if ($safe) {
                $this->_handle->file_safe_name = true;
                $this->_handle->file_overwrite = false;
                $this->_handle->file_auto_rename = true;
            }
            $this->_handle->auto_create_dir = true;

            $this->_handle->process($path);
            if ($this->_handle->processed) {
                return $this->_handle->file_dst_name;
            }
        }

        return false;
    }

    /**
     * Returns aliased image path
     *
     * @param string $image
     * @param string $alias
     *
     * @return string Public path to image
     */
    public function alias($image, $alias = 'original')
    {
        if (empty($image)) {
            return $image;
        }
        set_time_limit(60);
        /*
         * Check configuration for alias details
         */
        if (array_key_exists($alias, $this->aliases)) {
            /*
             * Check if file already exists
             */
            if (!file_exists($this->_local_path . $alias . '/' . $image)) {
                /*
                 * Get original
                 */
                if (!file_exists($this->_local_path . 'original/' . $image)) {
                    return $image;
                }
                $this->load($this->_local_path . 'original/' . $image);
                /*
                 * Create new aliased image
                 */
                foreach ($this->aliases[$alias] as $property => $value) {
                    /*
                     * Map config properties to class.upload.php properties
                     */
                    if (property_exists($this->_handle, $property)) {
                        $this->_handle->$property = $value;
                    }
                }
                /*
                 * Process image
                 */
                $this->_handle->file_overwrite = true;
                $this->_handle->process($this->_local_path . $alias . '/');
            }
            return $this->_public_url . $alias . '/' . $image;
        }
        /*
         * Return as original
         */
        return $this->_public_url . 'original/' . $image;
    }

    /**
     * Echoes HTML img tag with aliased image path.
     *
     * @param string $image
     * @param string $alias
     * @param string $alt
     * @param array $htmlOptions
     *
     * @return string
     */
    public function html_alias($image, $alias = 'original', $alt = '', $htmlOptions = array())
    {
        return CHtml::image($this->alias($image, $alias), $alt, $htmlOptions);
    }

    /**
     * Returns all aliased image paths
     *
     * @param string $image
     *
     * @return array
     */
    public function aliases($image)
    {
        if (empty($image)) {
            return $image;
        }

        $paths = array();
        foreach ($this->aliases as $alias => $settings) {
            $paths[$alias] = $this->alias($image, $alias);
        }

        return $paths;
    }

    /**
     * Downloads and prepares a YouTube cover image
     *
     * @param string $youtubeID
     * @param integer $width
     * @param integer $height
     * @param boolean $crop
     *
     * @return string
     */
    public function youtubeCover($youtubeID, $width = 170, $height = 170, $crop = true)
    {
        $local = $this->_local_path . 'video_cover/' . $youtubeID . '.jpg';
        if (!file_exists($local)) {
            $ch = curl_init("http://img.youtube.com/vi/$youtubeID/0.jpg");
            $fp = fopen($local, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);

            $original = new upload($local, $this->_language);
            $original->file_safe_name = false;
            $original->file_overwrite = true;
            $original->file_auto_rename = false;
            $original->auto_create_dir = true;
            $original->image_resize = true;
            if ($crop) {
                $original->image_ratio_crop = true;
            } else {
                $original->image_ratio = true;
            }
            $original->image_x = $width;
            $original->image_y = $height;
            $original->jpeg_quality = 95;

            $original->process($this->_local_path . 'youtube-cover');
        }

        return $this->_public_url . 'youtube-cover/' . $youtubeID . '.jpg';
    }

    /**
     * PHP getter magic method.
     *
     * @param string $name property name
     * @return mixed property value
     */
    public function __get($name)
    {
        return $this->_handle->$name;
    }

    /**
     * PHP setter magic method.
     *
     * @param string $name property name
     * @param mixed $value property value
     */
    public function __set($name, $value)
    {
        return $this->_handle->$name = $value;
    }

}