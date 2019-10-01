<?php

namespace Classes;

class Image {
    public $width = 0;
    public $height = 0;
    public $file_name;
    
    private $src_image;
    private $dst_image;
    private $file_type;
    
    private $validImageTypes = array('image/pjpeg', 'image/jpeg', 'image/gif', 'image/png', 'image/x-png');
    
    private function getFileType($file_name, $file_type) {
        if (($file_type == 'image/jpeg') || ($file_type == 'image/pjpeg') || (stristr($file_name, '.jpg')) || (stristr($file_name, '.jpeg')) ) {
            return 'image/jpeg';
        } elseif (($file_type == 'image/png') || ($file_type == 'image/x-png') || (stristr($file_name, '.png')) ) {
            return 'image/png';
        } else {
            return false;
        }
    }

    public function __construct($file_name, $file_type = null){
        if (!is_file($file_name)) {
            print_error('Файл отсутствует !');
            return false;
        }
        $this->file_name = $file_name;
        $this->file_type = $this->getFileType($file_name, $file_type);
        if (!in_array($this->file_type, $this->validImageTypes)) {
            print_error('Неверный тип файла !');
            return false;
        }
        if ($this->file_type == 'image/jpeg' ) {
            $this->src_image = imagecreatefromjpeg($file_name);
        } elseif ($file_type == 'image/png'){
            $this->src_image = imagecreatefrompng($file_name);
        }
        if (!$this->src_image) {
            return false;
        }
        list($this->width, $this->height) = getimagesize($file_name);
    }
    
    private function checkAlpha() {
        if ($this->file_type == 'image/png') {
            $alpha = imagecolorallocatealpha($this->src_image, 255, 255, 255, 127);
            if ($alpha) {
                imagecolortransparent($this->dst_image, $alpha);
                imagefill($this->dst_image, 0, 0, $alpha);
            }
        }        
    }
    
    public function crop ($width = 0, $height = 0) {
        if(!$this->src_image) {
            return false;
        }
        if ($this->width < $this->height) {
            $aspect_ratio = $this->width / $width;
            $src_h = $height * $aspect_ratio;
            $src_y = ($this->height - $src_h) / 2;
            $src_w = $this->width;
            $src_x = 0;
        } else {
            $aspect_ratio = $this->height / $height;
            $src_w = $width * $aspect_ratio;
            $src_x = ($this->width - $src_w) / 2;
            $src_h = $this->height;
            $src_y = 0;
        }
        $this->dst_image = imagecreatetruecolor($width, $height);
        $this->checkAlpha();
        return imagecopyresampled($this->dst_image, $this->src_image, 0, 0, $src_x, $src_y, $width, $height, $src_w, $src_h);            
    }
    
    public function resize ($max_width = 0, $max_height = 0, $fix_width = 0, $fix_height = 0){
        if(!$this->src_image) {
            return false;
        }
        if ($fix_width && $fix_height) {
            if (($this->width !== $fix_width) || ($this->height !== $fix_height)) {
                $this->dst_image = imagecreatetruecolor($fix_width, $fix_height);
                $this->checkAlpha();
                return imagecopyresampled($this->dst_image, $this->src_image, 0, 0, 0, 0, $fix_width, $fix_height, $this->width, $this->height);
            } else {
                $this->dst_image = $this->src_image;
                return false;
            }
            
        } elseif ($max_width || $max_height) {
            $do_resize = false;
            if (($max_width > 0) && (!$max_height > 0) && (($this->width > $max_width) || ($this->height > $max_width))) {
                $width = $max_width;
                $height = $max_width;
                if ($this->width < $this->height) {
                    $width = ($max_width / $this->height) * $this->width;
                } else {
                    $height = ($max_width / $this->width) * $this->height;
                }
                $do_resize = true;
            } else if ($max_height && $this->height > $max_height) {
                $height = $max_height;
                $width = ($max_height / $this->height) * $this->width;
                $do_resize = true;
            }
            if ($do_resize) {
                $this->dst_image = imagecreatetruecolor($width, $height);
                $this->checkAlpha();
                return imagecopyresampled($this->dst_image, $this->src_image, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
            } else {
                $this->dst_image = $this->src_image;
                return false;
            }
        } else {
            return false;
        }
    }
    public function save ($dst_file){
        if(!file_exists(dirname($dst_file))) {
            if (!mkdir(dirname($dst_file), 0755, true)) {
                die('Не удалось создать директории...');
            }
        }
        if ($this->file_type == 'image/jpeg') {
            imagejpeg($this->dst_image, $dst_file, 100);
        } else {
            imagepng($this->dst_image, $dst_file, 0);
        }
        return is_file($dst_file);        
    }
}