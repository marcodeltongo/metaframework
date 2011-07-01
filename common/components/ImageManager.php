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
     * Language name in 'en_EN' format.
     *
     * @var string
     */
    private $_language;
    /**
     * class.upload.php library instance.
     *
     * @var object
     */
    private $_handle;
    /**
     * class.upload.php library path.
     *
     * @var object
     */
    public $uploadClassPath;
    /**
     * Local upload path.
     *
     * @var string
     */
    public $basePath;
    /**
     * Public URL.
     *
     * @var string
     */
    public $baseUrl;
    /**
     * Image formats.
     */
    public $formats = array();

    /**
     * Initialize widget.
     */
    public function init()
    {
        parent::init();

        /*
         * Check configuration
         */
        if (null === $this->basePath) {
            throw new CHttpException(500, 'Missing configuration parameter "basePath".');
        }
        if (null === $this->baseUrl) {
            throw new CHttpException(500, 'Missing configuration parameter "baseUrl".');
        }
        if ((null === $this->uploadClassPath) and !class_exists('upload', false)) {
            throw new CHttpException(500, 'Missing configuration parameter "uploadClassPath".');
        }

        /*
         * Make public url absolute
         */
        $this->baseUrl = Yii::app()->getBaseUrl(true) . '/' . $this->baseUrl;

        /*
         * Load Upload class
         */
        require_once $this->uploadClassPath . 'class.upload.php';
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

    /**
     * Linearize $_FILES array from FormModel classes.
     *
     * @param array $src
     * @return array
     */
    private function normalize_files_array($src)
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
     * Language set function.
     *
     * @param string $lang
     */
    public function setLanguage($lang)
    {
        $this->_language = $lang;
    }

    /**
     * Main extension loader.
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
     * Main extension loader for uploads.
     *
     * @param mixed $image $_FILES array usually
     * @param string $field Optional key for $image array
     *
     * @return boolean
     */
    public function uploaded($image, $field = false)
    {
        /*
         * Find input
         */
        if (is_array($image)) {
            $image = $this->normalize_files_array($image);
            if (false !== $field) {
                $image = $image[$field];
            } else {
                $image = array_shift($image);
            }
        }

        /*
         * Check and return
         */
        $this->load($image);
        return $this->_handle->uploaded;
    }

    /**
     * Main extension loader for uploads.
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
        /*
         * No path, save in "original" folder.
         */
        if (false === $path) {
            $path = $this->basePath . 'original';
        }

        /*
         * Find input
         */
        if (is_array($image)) {
            $image = $this->normalize_files_array($image);
            if (false !== $field) {
                $image = $image[$field];
            } else {
                $image = array_shift($image);
            }
        }

        /*
         * Something to process ?
         */
        $this->load($image);
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
     * Returns aliased image path.
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
        if (array_key_exists($alias, $this->formats)) {
            /*
             * Check if file already exists
             */
            if (!file_exists($this->basePath . $alias . '/' . $image)) {
                /*
                 * Get original
                 */
                if (!file_exists($this->basePath . 'original/' . $image)) {
                    return $image;
                }
                $this->load($this->basePath . 'original/' . $image);
                /*
                 * Create new aliased image
                 */
                foreach ($this->formats[$alias] as $property => $value) {
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
                $this->_handle->process($this->basePath . $alias . '/');
            }
            return $this->baseUrl . $alias . '/' . $image;
        }
        /*
         * Return as original
         */
        return $this->baseUrl . 'original/' . $image;
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
     * Returns all aliased image paths.
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
        foreach ($this->formats as $alias => $settings) {
            $paths[$alias] = $this->alias($image, $alias);
        }

        return $paths;
    }

    /**
     * Removes image files.
     *
     * @param string $image
     */
    public function deleteAll($image)
    {
        /*
         * 
         */
    }

    /**
     * Downloads and prepares a YouTube cover image.
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
        $local = $this->basePath . 'video_cover/' . $youtubeID . '.jpg';
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

            $original->process($this->basePath . 'youtube-cover');
        }

        return $this->baseUrl . 'youtube-cover/' . $youtubeID . '.jpg';
    }

}