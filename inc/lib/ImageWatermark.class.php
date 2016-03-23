<?php

/**
 * @author duchanh
 * @copyright 2014
 */
/*
  example;
  'url' => 'watermak.png', // duong dan file logo
  'width' => 40, // chieu rong bang 40% file goc
  vi du:
  $Image_Watermark = new Image_Watermark();
  $a = $Image_Watermark->do_watermark('Jellyfish.jpg');
 */

class Image_Watermark {

    private $is_admin = TRUE;
    private $_messages = array();
    private $_image_sizes = array();
    private $_watermark_positions = array(
        'x' => array('left', 'center', 'right'),
        'y' => array('top', 'middle', 'bottom'),
    );
    private $_allowed_mime_types = array(
        'image/jpeg',
        'image/pjpeg',
        'image/png'
    );
    protected $_options = array(
        'df_watermark_on' => array(),
        'df_watermark_cpt_on' => array('everywhere'),
        'df_watermark_image' => array(
            'url' => 'watermak.png',
            'width' => 40,
            'plugin_off' => 0,
            'frontend_active' => FALSE,
            'manual_watermarking' => 0,
            'position' => 'bottom_right',
            'watermark_size_type' => 2,
            'offset_width' => 0,
            'offset_height' => 0,
            'absolute_width' => -10,
            'absolute_height' => -10,
            'transparent' => 80,
            'quality' => 90,
            'jpeg_format' => 'baseline',
            'deactivation_delete' => FALSE
        ),
        'df_image_protection' => array(
            'rightclick' => 0,
            'draganddrop' => 0,
            'forlogged' => 0,
        ),
        'version' => '1.3.1'
    );

    /**
     * Apply watermark to certain image
     *
     * @param string $filepath
     */
    public function do_watermark($filepath) {
        $options = array();

        //get watermark settings
        foreach ($this->_options as $option => $value) {
            $options = $this->_options;
        }

        //update-fix from 1.1.2
        $options['df_watermark_image']['quality'] = (isset($options['df_watermark_image']['quality']) ? $options['df_watermark_image']['quality'] : $this->_options['df_watermark_image']['quality']);

        //get image mime type

        $mime_type['type'] = custom_mime_content_type($filepath);


        //get image resource
        if (($image = $this->get_image_resource($filepath, $mime_type['type'])) !== FALSE) {
            //add watermark image to image
            if ($this->add_watermark_image($image, $options) !== FALSE) {

                //update-fix from 1.1.2
                $options['df_watermark_image']['jpeg_format'] = (isset($options['df_watermark_image']['jpeg_format']) ? $options['df_watermark_image']['jpeg_format'] : $this->_options['df_watermark_image']['jpeg_format']);

                if ($options['df_watermark_image']['jpeg_format'] === 'progressive') {
                    imageinterlace($image, true);
                }

                //save watermarked image
                $this->save_image_file($image, $mime_type['type'], $filepath, $options['df_watermark_image']['quality']);
                return $filepath;
            }
        }
    }

    /**
     * Get image resource accordingly to mimetype
     *
     * @param string $filepath
     * @param string $mime_type
     * @return resource
     */
    private function get_image_resource($filepath, $mime_type) {
        switch ($mime_type) {
            case 'image/jpeg':
            case 'image/pjpeg':
                return imagecreatefromjpeg($filepath);

            case 'image/png':
                $res = imagecreatefrompng($filepath);
                $transparent = imagecolorallocatealpha($res, 255, 255, 254, 127);
                imagefilledrectangle($res, 0, 0, imagesx($res), imagesy($res), $transparent);
                return $res;

            default:
                return FALSE;
        }
    }

    /**
     * Add watermark image to image
     *
     * @param resource $image
     * @param array $opt
     * @return resource
     */
    private function add_watermark_image($image, array $opt) {
        $url = $opt['df_watermark_image']['url'];
        $watermark_file_info = getimagesize($url);

        switch ($watermark_file_info['mime']) {
            case 'image/jpeg':
            case 'image/pjpeg':
                $watermark = imagecreatefromjpeg($url);
                break;

            case 'image/gif':
                $watermark = imagecreatefromgif($url);
                break;

            case 'image/png':
                $watermark = imagecreatefrompng($url);
                break;

            default:
                return FALSE;
        }

        $watermark_width = imagesx($watermark);
        $watermark_height = imagesy($watermark);
        $img_width = imagesx($image);
        $img_height = imagesy($image);
        $size_type = $opt['df_watermark_image']['watermark_size_type'];

        if ($size_type === 1) { //custom
            $w = $opt['df_watermark_image']['absolute_width'];
            $h = $opt['df_watermark_image']['absolute_height'];
        } elseif ($size_type === 2) { //scale
            $ratio = $img_width * $opt['df_watermark_image']['width'] / 100 / $watermark_width;

            $w = (int) ($watermark_width * $ratio);
            $h = (int) ($watermark_height * $ratio);

            //if watermark scaled height is bigger then image watermark
            if ($h > $img_height) {
                $w = (int) ($img_height * $w / $h);
                $h = $img_height;
            }
        } else { //original
            $w = $watermark_width;
            $h = $watermark_height;
        }

        switch ($opt['df_watermark_image']['position']) {
            case 'top_left':
                $dest_x = $dest_y = 0;
                break;

            case 'top_center':
                $dest_x = ($img_width / 2) - ($w / 2);
                $dest_y = 0;
                break;

            case 'top_right':
                $dest_x = $img_width - $w;
                $dest_y = 0;
                break;

            case 'middle_left':
                $dest_x = 0;
                $dest_y = ($img_height / 2) - ($h / 2);
                break;

            case 'middle_right':
                $dest_x = $img_width - $w;
                $dest_y = ($img_height / 2) - ($h / 2);
                break;

            case 'bottom_left':
                $dest_x = 0;
                $dest_y = $img_height - $h;
                break;

            case 'bottom_center':
                $dest_x = ($img_width / 2) - ($w / 2);
                $dest_y = $img_height - $h;
                break;

            case 'bottom_right':
                $dest_x = $img_width - $w;
                $dest_y = $img_height - $h;
                break;

            default:
                $dest_x = ($img_width / 2) - ($w / 2);
                $dest_y = ($img_height / 2) - ($h / 2);
        }

        $dest_x += $opt['df_watermark_image']['offset_width'];
        $dest_y += $opt['df_watermark_image']['offset_height'];

        $this->imagecopymerge_alpha($image, $this->resize($watermark, $url, $w, $h, $watermark_file_info), $dest_x, $dest_y, 0, 0, $w, $h, $opt['df_watermark_image']['transparent']);

        return $image;
    }

    /**
     * Creates new image
     */
    private function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct) {
        //creating a cut resource
        $cut = imagecreateTRUEcolor($src_w, $src_h);

        //copying relevant section from background to the cut resource
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);

        //copying relevant section from watermark to the cut resource
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);

        //insert cut resource to destination image
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
    }

    /**
     * Resizes image
     */
    private function resize($im, $path, $nWidth, $nHeight, $imgInfo) {
        $newImg = imagecreateTRUEcolor($nWidth, $nHeight);

        //check if this image is PNG, then set if transparent
        if ($imgInfo[2] === 3) {
            imagealphablending($newImg, FALSE);
            imagesavealpha($newImg, TRUE);
            $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
            imagefilledrectangle($newImg, 0, 0, $nWidth, $nHeight, $transparent);
        }

        imagecopyresampled($newImg, $im, 0, 0, 0, 0, $nWidth, $nHeight, $imgInfo[0], $imgInfo[1]);

        return $newImg;
    }

    /**
     * Save image from image resource
     *
     * @param resource $image
     * @param string $mime_type
     * @param string $filepath
     * @return boolean
     */
    private function save_image_file($image, $mime_type, $filepath, $quality) {
        switch ($mime_type) {
            case 'image/jpeg':
            case 'image/pjpeg':
                imagejpeg($image, $filepath, $quality);
                break;

            case 'image/png':
                imagepng($image, $filepath, (int) round(9 * $quality / 100));
                break;
        }
    }

}

if (!function_exists("custom_mime_content_type")) {

    function custom_mime_content_type($filename) {
        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',
            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.', $filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        } elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        } else {
            ob_start();
            system('file -i -b ' . $filename);
            $output = ob_get_clean();
            $output = explode("; ", $output);
            if (is_array($output)) {
                return trim($output[0]);
            } else {
                return 'application/octet-stream';
            }
        }
    }

}