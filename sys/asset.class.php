<?php if (!defined('PEWPEWPEW')) exit('Forbidden');

/**
 * @package sys
 */

/**
 * Gathers css and js files and outputs them where requested.
 *
 * Use this class by calling the add_css and add_js static methods in the
 * controllers and the print_css and print_js static methods in the head and
 * before the closing body tag of the layout, respectively. For it to work,
 * css files must be placed in www/css (or a sub-folder) and script files must
 * be in www/js (or a sub-folder).
 * 
 * @version 0.3 6-jul-2011
 * @author ifcanduela <ifcanduela@gmail.com>
 * @package sys
 */
class Asset
{
    /**
     * Holds the collected css files.
     *
     * @var array
     * @access private
     * @static
     */
    private static $css = array();
    
    /**
     * Holds the collected javascript files.
     *
     * @var array
     * @access private
     * @static
     */
    private static $js = array();
    
    /**
     * Holds the collected javascript substitution blocks.
     *
     * @var array
     * @access private
     * @static
     */
    private static $js_blocks = array();
    
    /**
     * Adds a stylesheet to the assets list
     *
     * @param string $slug the filename without extension of the stylesheet,
     *                     relative to the www/css folder
     * @return boolean true if the file exists, false otherwise
     * @access public
     * @static
     */
    public static function add_css($slug)
    {
        $slug = str_replace($slug, '.css', '');
        
        if (file_exists(root('www' . DS . 'css' . DS . $slug . '.css', false))) {
            if (!in_array($slug, self::$css, true)) {
                self::$css[] = $slug;
            }
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Adds a javascript file to the assets list
     *
     * @param string $slug the filename without extension of the javascript
     *                     file, relative to the www/css folder
     * @return boolean true if the file exists, false otherwise
     * @access public
     * @static
     */
    public static function add_js($slug)
    {
        $slug = str_replace($slug, '.js', '');
        
        if (file_exists(root('www' . DS . 'js' . DS . $slug . '.js', false))) {
            if (!in_array($slug, self::$js, true)) {
                self::$js[] = $slug;
            }
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Adds a javascript file to the assets list
     *
     * @param string $slug the filename without extension of the javascript
     *                     file, relative to the www/css folder
     * @return boolean true if the file exists, false otherwise
     * @access public
     * @static
     */
    public static function add_js_block($block)
    {
        self::$js_blocks[] = $block;
        return true;
    }
    
    /**
     * Prints well-formatted LINK elements to include CSS files into the page.
     *
     * @param boolean $print set to false to retrieve the HTML without printing
     * @return string|void $output the generated code, if $print is false
     * @access public
     * @static
     */
    public static function print_css($print = true)
    {
        $output = '';
        
        foreach (self::$js as $key => $value) {
            $output .= '<link rel"stylesheet" href="'
                     . url('www/css/' . $value
                     . '.css', false) . '"> ' . PHP_EOL;
        }
        
        if ($print) {
            echo $output;
        } else {
            return $output;
        }
    }
    
    /**
     * Prints well-formatted SCRIPT elements to include JS files into the page.
     *
     * @param boolean $print set to false to retrieve the HTML without printing
     * @return string|void $output the generated code, if $print is false
     * @access public
     * @static
     */
    public static function print_js($print = true)
    {
        $output = '';
        
        foreach (self::$js as $key => $value) {
            $output .= '<script type="text/javascript" src="'
                     . url('www/js/' . $value
                     . '.js', false) . '"></script> ' . PHP_EOL;
        }
        
        if ($print) {
            echo $output;
        } else {
            return $output;
        }
    }
    
    /**
     * Prints the Javascript code blocks inside SCRIPT elements.
     *
     * @param boolean $print set to false to retrieve the HTML without printing
     * @return mixed the generated code, if $print is false
     * @access public
     * @static
     */
    public static function print_js_blocks($print = true)
    {
        $output = '';
        
        foreach (self::$js_block as $value) {
            $output .= '<script type="text/javascript">' . PHP_EOL
                     . $value . PHP_EOL
                     . '</script> ' . PHP_EOL;
        }
        
        if ($print) {
            echo $output;
        } else {
            return $output;
        }
    }
}