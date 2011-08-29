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
 * @version 1.2
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
     * Local upload base path.
     *
     * @var string
     */
    public $basePath;
    /**
     * Local originals upload path.
     *
     * @var string
     */
    public $originalPath;
    /**
     * Public URL.
     *
     * @var string
     */
    public $baseUrl;
    /**
     * Public originals URL.
     *
     * @var string
     */
    public $originalUrl;
    /**
     * Image formats.
	 *
	 * @var array
     */
    public $formats = array();
    /**
     * Wheter to prebuild images in the various formats.
	 *
	 * @var boolean or array
     */
    public $prebuild = false;

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
        if (null === $this->originalPath) {
            $this->originalPath = $this->basePath . 'original/';
        }

        /*
         * Make public url absolute if not already.
         */
		if (strpos($this->baseUrl, 'http://') === false) {
			$this->baseUrl = Yii::app()->getBaseUrl(true) . '/' . $this->baseUrl;
		}
        if (null === $this->originalUrl) {
            $this->originalUrl = $this->baseUrl . 'original/';
        }

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
	 * @param mixed $prebuild Wheter to prebuild formats of the image, true means all, array contains names
     *
     * @return boolean false or filename
     */
    public function upload($image, $field = false, $path = false, $safe = true, $prebuild = false)
    {
        /*
         * No path, save in "original" folder.
         */
        if (false === $path) {
            $path = $this->originalPath;
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
			/*
			 * Prepare options
			 */
            if ($safe) {
                $this->_handle->file_safe_name = true;
                $this->_handle->file_overwrite = false;
                $this->_handle->file_auto_rename = true;
            }
            $this->_handle->auto_create_dir = true;
			/*
			 * Process image
			 */
            $this->_handle->process($path);
            if ($this->_handle->processed) {
				/*
				 * Destination filename may be different for uniqueness
				 */
				$original = $this->_handle->file_dst_name;
				/*
				 * Prepare formats ?
				 */
				if (($prebuild === true) or ($this->prebuild === true)) {
					foreach (array_keys($this->formats) as $format) {
						$this->alias($original, $format);
					}
				} elseif (is_array($prebuild)) {
					foreach ($prebuild as $format) {
						$this->alias($original, $format);
					}
				} elseif (is_array($this->prebuild)) {
					foreach ($this->prebuild as $format) {
						$this->alias($original, $format);
					}
				} else {
					foreach (array_keys($this->formats) as $format) {
						if (isset($format['prebuild']) and $format['prebuild'] === true) {
							$this->alias($original, $format);
						}
					}
				}

                return $original;
            }
        }

		/*
		 * Return error
		 */
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
    public function alias($image, $alias = false)
    {
        if (empty($image)) {
            return $image;
        }
        set_time_limit(60);
        /*
         * Check configuration for alias details
         */
        if (false !== $alias and array_key_exists($alias, $this->formats)) {
            /*
             * Check if file already exists
             */
			if (isset($this->formats[$alias]['path'])) {
				$path = $this->basePath . $this->formats[$alias]['path'];
			} else {
				$path = $this->basePath . $alias . '/';
			}
            if (!file_exists($path . $image)) {
                /*
                 * Get original
                 */
                if (!file_exists($this->originalPath . $image)) {
                    return $image;
                }
                $this->load($this->originalPath . $image);
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
                $this->_handle->process($path);
            }
			/*
			 * Use format path or autobuild from name
			 */
			$url = $this->baseUrl . $alias . '/';
			if (isset($this->formats[$alias]['path'])) {
				$url = $this->baseUrl . $this->formats[$alias]['path'];
			}
            return $url . $image;
        }
        /*
         * Return as original
         */
        return $this->originalUrl . $image;
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
    public function html_alias($image, $alias = false, $alt = '', $htmlOptions = array())
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
         * Iterate through formats
         */
        if (file_exists($this->originalPath . $image)) {
            @unlink($this->originalPath . $image);
        }
        foreach ($this->formats as $alias => $info) {
            if (file_exists($this->basePath . $alias . '/' . $image)) {
                @unlink($this->basePath . $alias . '/' . $image);
            }
        }
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
