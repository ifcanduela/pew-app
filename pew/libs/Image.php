<?php

namespace pew\libs;

/**
 * The Image library class contains simple methods to manage pictures.
 * 
 * The user can load a picture from the local drives or use a submitted form
 * array to upload a file. The methods in this class copy files and resize or
 * crop images.
 *
 * The class uses the GD image library functions.
 *
 * @package pew/libs
 * @author ifcanduela <ifcanduela@gmail.com>
 */
class Image
{
    /*
     * Error conditions.
     */
    const IMAGE_NOT_FOUND = 1;
    const UNSUPPORTED_FORMAT = 2;
    const IMAGE_WRITE_ERROR = 3;
    const NO_IMAGE_LOADED = 4;
    const IMAGE_CROP_ERROR = 5;
    const IMAGE_RESIZE_ERROR = 6;
    const INVALID_SIZE = 7;

    /**
     * @var stream The image bitstream data.
     */
    private $image_resource;
    
    /**
     * @var string Fully-qualified path.
     */
    private $path;
    
    /**
     * @var string Image file name, without extension.
     */
    private $file_name;
    
    /**
     * @var string File name with extension.
     */
    private $base_name;
    
    /**
     * @var string Original file extension.
     */
    private $extension;

    /**
     * @var string Original file MIME type.
     */
    private $mime_type;
    
    /**
     * @var string Destination file name, without extension.
     */
    private $dst_file_name;

    /**
     * @var int Image resource width, in pixels.
     */
    private $width;
    
    /**
     * @var int Image resource height, in pixels.
     */
    private $height;
    
    /**
     * @var int Thumbnail width, in pixels.
     */
    private $thumb_width;
    
    /**
     * @var int Thumbnail height, in pixels.
     */
    private $thumb_height;
    
    /**
     * @var bool Flag that tells if an image was previously loaded
     */
    private $loaded;
    
    /**
     * @var string Last error message.
     */
    private $error_message;
    
    /**
     * @var int Last error code.
     */
    private $error_code;

    /**
     * @var int Color from the GIF palette that makes pixels transparent.
     */
    private $gif_transparent_color;
    
    /**
     * Creates an instance of the Img class and optionally loads an image.
     *
     * @param string $file Optional file name of image to process
     * @access public
     */
    public function __construct($file = null)
    {
        $this->error_message = null;
        $this->error_code = null;
        $this->thumb_width  = 100;
        $this->thumb_height = 100;
        $this->loaded = false;

        $this->set_error(0);

        # Make sure the parameter is somewhat valid
        if (is_string($file)) {
            # Check if we can find the file
            if (file_exists($file)) {
                # Initialize the instance
                $this->load($file);
            } else {
                $this->set_error(self::IMAGE_NOT_FOUND, 'Image file could not be found: ' . $file);
            }
        } elseif (is_array($file) && isset($file['tmp_name'])) {
            $this->upload($file);
        }        
    }

    /**
     * Loads an image file for processing.
     *
     * @param string $file The image file name
     * @return mixed Returns true if the image was loaded, false otherwise
     * @access public
     */
    public function load($file)
    {
        $this->loaded = false;
        
        # Check if the image file exists
        if (file_exists($file)) {
            # Get file info
            $path_info = pathinfo($file);
            
            if (!isset($path_info['extension']) || !in_array($path_info['extension'], array('jpeg', 'jpg', 'png', 'gif'))) {
                $this->set_Error(self::UNSUPPORTED_FORMAT, 'For the moment, only JPG, GIF and PNG image files are supported [' . $path_info['extension'] . ']');
                return false;
            }
            
            # Get image info
            $img_info = GetImageSize($file);
            
            # Create a binary image stream according to the image type
            if (!$this->load_image_resource($file, $img_info['mime'])) {
                $this->set_error(self::UNSUPPORTED_FORMAT, 'The provided image could not be loaded.');
                return false;
            }
            
            $this->path          = $path_info['dirname'];
            $this->file_name     =
            $this->dst_file_name = $path_info['filename'];
            $this->base_name     = $path_info['basename'];
            $this->extension     = $path_info['extension'];
            $this->width         = $img_info[0];
            $this->height        = $img_info[1];
            $this->error         = '';
            
            $this->loaded = true;
            
        } else {
            $this->loaded = false;
            $this->set_error(self::IMAGE_NOT_FOUND, 'Image file could not be found: ' . $file);
        }

        return $this->loaded;
    }

    public function upload(array $uploaded_file)
    {
        # Load image resource
        if (!$this->load_image_resource($uploaded_file['tmp_name'], $uploaded_file['type'])) {
            $this->set_error(self::UNSUPPORTED_FORMAT, 'The image file could not be loaded');
            $this->loaded = false;
            return false;
        }

        $this->loaded = true;

        # Set destination name
        $this->set_destination_name($uploaded_file['name']);

        $path_info = pathinfo($uploaded_file['name']);
        $img_info = GetImageSize($uploaded_file['tmp_name']);
            
        # set the path to the temporary uploads directory
        $this->path          = dirname($uploaded_file['tmp_name']);
        $this->file_name     =
        $this->dst_file_name = $path_info['filename'];
        $this->base_name     = $path_info['basename'];
        $this->extension     = $path_info['extension'];
        $this->width         = $img_info[0];
        $this->height        = $img_info[1];
        $this->error         = '';
        
        return $this->loaded = true;
    }

    /**
     * Load an image file.
     * 
     * @param string $file Filesystem location and name of the image file
     * @param string $mime_type MIME type of the image file
     * @return boolean false on error, true otherwise
     */
    protected function load_image_resource($file, $mime_type)
    {
        $result = true;

        # Create a binary image stream according to the image type
        switch($mime_type) {
            case 'image/jpeg':
                $result = $this->image_resource = ImageCreateFromJPEG($file);
                break;
            case 'image/gif':
                $result = $this->image_resource = ImageCreateFromGIF($file);
                # Preserve GIF transparency
                $result = $result && $transparent_index = ImageColorTransparent($this->image_resource);
                if ($transparent_index != -1) {
                    $this->transparent_color = ImageColorsForIndex($this->image_resource, $transparent_index);
                }
                break;
            case 'image/png':
                $this->image_resource = ImageCreateFromPNG($file);
                # Preserve PNG alpha transparency
                $result = ImageAlphaBlending($this->image_resource, true);
                $result = $result && ImageSaveAlpha($this->image_resource, true);
                break;
            default:
                $this->set_error(self::UNSUPPORTED_FORMAT, 'Invalid image file type');
                $result = false;
                break; 
        }

        $this->mime_type = $mime_type;

        return $result;
    }
    
    /**
     * Saves a copy of the loaded image file to a folder.
     *
     * @param string $folder The absolute or relative filesystem location to
     *               copy the loaded image. Leave empty to copy to Current
     *               Working Directory.
     * @return mixed Returns false if the image could not be copied, or the 
     *               created image path and file name
     * @access public
     */
    public function save_to($folder = '')
    {   
        if (!$this->loaded) {
            $this->set_error(self::NO_IMAGE_LOADED, 'No image was loaded (save_to)');
            return false;
        }
        
        # Clean the output folder name
        if (empty($folder)) {
            $folder = getcwd();
        }
        $folder = rtrim($folder, '/') . '/';
        
        # Create folders if necessary
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }
        
        # Copy the source file to destination
        # Potential enhancement: allow the user to specify an output format,
        # and use the image_resource to save the copy.
        if (copy($this->path . DIRECTORY_SEPARATOR . $this->base_name, $folder . $this->dst_file_name . '.' . $this->extension)) {
            return $folder . $this->dst_file_name . '.' . $this->extension;
        } else {
            $this->set_error(self::IMAGE_WRITE_ERROR, 'Image file could not be copied');
            return false;
        }
    }

    /**
     * Updates the name assigned to saved images.
     *
     * @param string $new_name The file name, without extension
     * @return mixed Returns The Img object instance ($this)
     */
    protected function set_destination_name($new_name)
    {
        if (!$this->loaded) {
            $this->set_error(self::NO_IMAGE_LOADED, 'No image was loaded (set_destination_name)');
            return false;
        }
        
        # Check that a valid parameter was received
        if (is_string($new_name)) {
            $this->dst_file_name = $new_name;
        } else {
            $this->set_error(self::INVALID_FILE_NAME, 'Invalid destination file name');
            return false;
        }
        
        
        return $this;
    }
    
    /**
     * Gets the current destination name.
     *
     * @param string $folder The absolute or relative filesystem location to
     *               copy the loaded image. Leave empty to copy to Current
     *               Working Directory.
     * @return string The full path and filename of the destination image
     */    
    protected function get_destination_name($folder = null)
    {
        # Clean the destination folder
        if (empty($folder)) {
            $folder = getcwd();
        }
        
        $folder = rtrim($folder, '/') . '/';
        
        return $folder . $this->dst_file_name . '.' . $this->extension;
    }

    public function destination_name($name = null)
    {
        if (!is_null($name)) {
            $this->set_destination_name($name);
        }

        return $this->get_destination_name();
    }

    /**
     * Updates the output size for the thumbnails.
     *
     * @param int $width The width in pixels
     * @param int $height The height in pixels. By default, it's the same as the 
     *                    width
     * @return mixed Returns The Img object instance ($this) or false if $width
     *               is invalid
     * @access public
     */
    public function thumb_size($width = null, $height = null)
    {
        if (!$this->loaded) {
            $this->set_error(self::NO_IMAGE_LOADED, 'No image was loaded (thumb_size)');
            return false;
        }

        if (is_null($width)) {
            return array($this->thumb_width, $this->thumb_height);
        }

        # Ensure the parameter is valid
        if (!is_numeric($width)) {
            $this->set_error(self::INVALID_SIZE, 'Specified thumbnail width is invalid (thumb_size)');
            return false;
        } else {
            # If no valid height is provided, make it equal to the width
            if (!is_numeric($height)) {
                # Square thumbnail
                $height = $width;
            }
            
            $this->thumb_width = $width;
            $this->thumb_height = $height;
            
            return $this;
        }
    }
        
    /**
     * Resets the thumbnail size to the default.
     *
     * @return object The Img object instance ($this)
     * @access public
     */
    public function reset_thumb_size()
    {
        $this->thumb_width  = 100;
        $this->thumb_height = 100;
        
        return $this;
    }

    public function width()
    {
        if (!$this->loaded) {
            $this->set_error(self::NO_IMAGE_LOADED, 'No image was loaded (width)');
            return false;
        }

        if (!$this->width) {
            $this->width = imagesx($this->resource);
        }

        return $this->width;
    }

    public function height()
    {
        if (!$this->loaded) {
            $this->set_error(self::NO_IMAGE_LOADED, 'No image was loaded (height)');
            return false;
        }

        if (!$this->height) {
            $this->height = imagesy($this->resource);
        }

        return $this->height;
    }

    /**
     * Creates a resized and cropped copy of the loaded image.
     *
     * This function will create an image based on the loaded image. The result
     * is always a proportinally-resized and cropped image, and the method
     * supports thumbnails bigger than the source image.
     *
     * The destination file name can be controlled by calling
     * setDestinationName(),and the output size can be set with setthumbSize()
     * or reset with reset_thumb_size(). By default THUMBNAIL_WIDTH and
     * THUMBNAIL_HEIGHT are used, and those constants default to a 100x100px
     * image.
     *
     * @param string $folder The absolute or relative filesystem location to
     *               copy the loaded image. Leave empty to copy to Current
     *               Working Directory.
     * @return mixed Returns The Img object instance ($this) or the created
     *               image path and file name
     * @access public
     */
    public function save_thumb_to($folder = '')
    {
        if (!$this->loaded) {
            $this->set_error(self::NO_IMAGE_LOADED, 'No image was loaded (save_thumb_to)');
            return false;
        }
        
        if (!is_resource($this->image_resource)) {
            $this->set_error(self::NO_IMAGE_LOADED, 'Image resource is not loaded (save_thumb_to)');
            return false;
        }
        
        # Get image size properties
        $s_w = $this->width;
        $s_h = $this->height;
        $d_w = $this->thumb_width;
        $d_h = $this->thumb_height;
        $s_ratio = $s_w / $s_h;
        $d_ratio = $d_w / $d_h;
        
        # Check whether to crop vertically or horizontally
        if ($s_ratio < $d_ratio) {
            # Crop top and bottom
            $t_w = $d_w;
            $t_h = round($s_h * ($d_w / $s_w));
        } elseif($s_ratio > $d_ratio) {
            # Crop left and right
            $t_h = $d_h;
            $t_w = round($s_w * ($d_h / $s_h));
        } else {
            # Don't need to crop
            $t_h = $d_h;
            $t_w = $d_w;
        }
    
        # Crop coordinates
        $dif_w = abs($d_w - $t_w);
        $dif_h = abs($d_h - $t_h);
        $pos_x = floor($dif_w / 2);
        $pos_y = floor($dif_h / 2);
        
        # Create destination canvas in memory to paste the resized image
        $resized = ImageCreateTrueColor($t_w, $t_h);
        
        # Resize the image to target dimensions
        if (!ImageCopyResampled($resized, $this->image_resource, 0, 0, 0, 0, $t_w, $t_h, $s_w, $s_h)) {
            $this->set_error(self::IMAGE_RESIZE_ERROR, 'Image could not be resized');
            return false;
        }
        
        # A second destination canvas to paste the cropped image
        $cropped = ImageCreateTrueColor($d_w, $d_h);
        
        # Crop the image to thumbnail dimensions
        if (!ImageCopyResampled($cropped, $resized, 0, 0, $pos_x, $pos_y, $d_w, $d_h, $d_w, $d_h)) {
            $this->set_error(self::IMAGE_CROP_ERROR, 'Image could not be cropped');
            return false;
        }
        
        # We don't need this anymore
        ImageDestroy($resized);
        
        # Clean the destination folder
        if (empty($folder)) {
            $folder = getcwd();
        }
        
        $folder = rtrim($folder, '/') . '/';
        
        # Create folders if necessary
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }
        
        # Create final image, with 80% of compression quality
        if (!ImageJPEG($cropped, $folder . $this->dst_file_name . '.' . $this->extension, 80)) {
            $this->set_error(self::IMAGE_WRITE_ERROR, 'Image could not be written to destination');
            return false;
        }
        
        # We don't need this anymore
        ImageDestroy($cropped);
        
        return $folder . $this->dst_file_name . '.' . $this->extension;
    }

    /**
     * Get loaded image MIME type
     * 
     * @return string MIME type
     */
    public function mime_type()
    {
        return $this->mime_type;
    }

    /**
     * Set the error code and message.
     * 
     * @param int $code Error code
     * @param string $message Error message
     */
    protected function set_error($code, $message = '')
    {
        $this->error_code = $code;
        $this->error_message = $message;
    }

    /**
     * Get the last error message.
     * 
     * @return string The error message
     */
    public function error_message()
    {
        return $this->error_message;
    }

    /**
     * Get the last error code.
     * 
     * @return string The error code
     */
    public function error_code()
    {
        return $this->error_code;
    }
}
