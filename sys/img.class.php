<?php if (!defined('PEWPEWPEW')) exit('Forbidden');

/**
 * @package sys
 */
 
defined('THUMBNAIL_WIDTH')  or define('THUMBNAIL_WIDTH',  100);
defined('THUMBNAIL_HEIGHT') or define('THUMBNAIL_HEIGHT', 100);

/**
 * The Img library class contains simple methods to manage pictures.
 * 
 * The user can load a picture from the local drives or use a submitted form
 * array to upload a file. The methods in this class comprise copying files, and
 * resizing and cropping images.
 *
 * The Img class methods employ the GD image library functions, since installing
 * ImageMagick is such a pain in the ass -- at least for Windows.
 *
 * @version 0.4 22-jul-2011
 * @author ifcanduela <ifcanduela@gmail.com>
 * @package sys
 */
class Img
{
    /**
     * The image bitstream data.
     *
     * @var stream
     * @access protected
     */
    protected $image_resource;
    
    /**
     * Fully-qualified path.
     * 
     * @var string
     * @access protected
     */
    protected $path;
    
    /**
     * Image file name, without extension.
     * 
     * @var string
     * @access protected
     */
    protected $file_name;
    
    /**
     * File name with extension.
     * 
     * @var string
     * @access protected
     */
    protected $base_name;
    
    /**
     * Original file extension.
     * 
     * @var string
     * @access protected
     */
    protected $extension;

    /**
     * Original file MIME type.
     * 
     * @var string
     * @access protected
     */
    protected $mime;
    
    /**
     * Destination file name, without extension.
     * 
     * @var string
     * @access protected
     */
    protected $dst_file_name;

    /**
     * Image resource width, in pixels.
     * 
     * @var int
     * @access public
     */
    public $width;
    
    /**
     * Image resource height, in pixels.
     * 
     * @var int
     * @access public
     */
    public $height;
    
    /**
     * Thumbnail width, in pixels.
     * 
     * @var int
     * @access protected
     */
    protected $thumb_width;
    
    /**
     * Thumbnail height, in pixels.
     * 
     * @var int
     * @access protected
     */
    protected $thumb_height;
    
    /**
     * Flag that tells if an image was previously loaded
     *
     * @var bool
     * @access protected
     */
    protected $loaded;
    
    /**
     * Error message, accesible from the user context when a method returns
     * false.
     * 
     * @var string
     * @access public
     */
    public $error;
    
    /**
     * Color from the GIF indexed palette that makes pixels transparent.
     * 
     * @var int
     * @access protected
     */
    protected $gif_transparent_color;
    
    /**
     * Creates an instance of the Img class and optionally loads an image.
     *
     * @param string $file Optional file name of image to process
     * @access public
     */
    public function __construct($file = null)
    {
        $this->loaded = false;
        
        # Make sure the parameter is somewhat valid
        if (is_string($file)) {
            # Check if we can find the file
            if (file_exists($file)) {
                # Initialize the instance
                $this->load($file);
            } else {
                $this->error = 'File could not be found';
            }
        } elseif (is_array($file) && isset($file['tmp_name'])) {
            $this->upload($file['tmp_name']);
            $this->dst_file_name = $file['name'];
        }
        
        $this->thumb_width  = THUMBNAIL_WIDTH;
        $this->thumb_height = THUMBNAIL_HEIGHT;
    }
    
    /**
     * Loads an image file for processing.
     *
     * @param string $file The image file name
     * @return mixed Returns false if the image cannot be loaded, or the Img
     *               object instance ($this) if result is correct
     * @access public
     */
    public function load($file)
    {
        $this->loaded = false;
        $this->error = '';
        
        # Check if the image file exists
        if (file_exists($file)) {
            # Get file info
            $path_info = pathinfo($file);
            
            if (!in_array($path_info['extension'], array('jpeg', 'jpg', 'png', 'gif'))) {
                $this->error = 'For the moment, only JPG and PNG image files are supported';
                return false;
            }
            
            # Get image info
            $img_info = GetImageSize($file);
            
            # Create a binary image stream according to the image type
            switch($this->mime = $img_info['mime']) {
                case 'image/jpeg':
                    $this->image_resource = ImageCreateFromJPEG($file);
                    break;
                case 'image/gif':
                    $this->image_resource = ImageCreateFromGIF($file);
                    # Preserve GIF transparency
                    $transparent_index = ImageColorTransparent($this->image_resource);
                    if ($transparent_index != -1) {
                        $this->transparent_color = ImageColorsForIndex($this->image_resource, $transparent_index);
                    }
                    break;
                case 'image/png':
                    $this->image_resource = ImageCreateFromPNG($file);
                    # Preserve PNG alpha transparency
                    ImageAlphaBlending($this->image_resource, true);
                    ImageSaveAlpha($this->image_resource, true);
                    break;
                default:
                    $this->error = 'Invalid image file type';
                    return false;
                    break; 
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
            
            return $this;
        } else {
            $this->loaded = false;
            
            $this->error = 'Image file could not be found';
            return false;
        }
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
    public function saveTo($folder = '')
    {
        $this->error = '';
        
        if (!$this->loaded) {
            $this->error = 'No image was loaded';
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
        if (copy($this->path . DS . $this->base_name, $folder . $this->dst_file_name . '.' . $this->extension)) {
            return $folder . $this->dst_file_name . '.' . $this->extension;
        } else {
            $this->error = 'Image file could not be copied';
            return false;
        }
    }

    /**
     * Updates the name assigned to saved images.
     *
     * @param string $new_name The file name, without extension
     * @return mixed Returns The Img object instance ($this)
     * @access public
     */
    public function setDestinationName($new_name)
    {
        $this->error = '';
        
        if (!$this->loaded) {
            $this->error = 'No image was loaded';
            return false;
        }
        
        # Check that a valid parameter was received
        if (is_string($new_name)) {
            $this->dst_file_name = $new_name;
        } else {
            $this->error = 'Invalid destination file name';
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
     * @access public
     */    
    public function getDestinationName($folder = null) {
        # Clean the destination folder
        if (empty($folder)) {
            $folder = getcwd();
        }
        
        $folder = rtrim($folder, '/') . '/';
        
        return $folder . $this->dst_file_name . '.' . $this->extension;
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
    public function setThumbSize($width, $height = null)
    {
        $this->error = '';
        
        if (!$this->loaded) {
            $this->error = 'No image was loaded';
            return false;
        }
        
        # Ensure the parameter is valid
        if (!is_numeric($width)) {
            $this->error = 'Specified thumbnail width is invalid';
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
    public function resetThumbSize()
    {
        $this->thumb_width  = THUMBNAIL_WIDTH;
        $this->thumb_height = THUMBNAIL_HEIGHT;
        
        return $this;
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
     * or reset with resetThumbSize(). By default THUMBNAIL_WIDTH and
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
    public function saveThumbnailTo($folder = '')
    {
        $this->error = '';
        
        if (!$this->loaded) {
            $this->error = 'No image was loaded';
            return false;
        }
        
        if (!is_resource($this->image_resource)) {
            $this->error = 'Image resource is not loaded';
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
            $this->error = 'Image could not be resized';
            return false;
        }
        
        # A second destination canvas to paste the cropped image
        $cropped = ImageCreateTrueColor($d_w, $d_h);
        
        # Crop the image to thumbnail dimensions
        if (!ImageCopyResampled($cropped, $resized, 0, 0, $pos_x, $pos_y, $d_w, $d_h, $d_w, $d_h)) {
            $this->error = 'Image could not be cropped';
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
            $this->error = 'Image could not be written to destination';
            return false;
        }
        
        # We don't need this anymore
        ImageDestroy($cropped);
        
        return $folder . $this->dst_file_name . '.' . $this->extension;
    }
}
