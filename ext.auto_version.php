<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Use versioned filenames for better cache control.
 *
 * @package             Auto Version
 * @author              Michael Gilley (@michaelgilley)
 * @copyright           Copyright (c) 2013 Michael Gilley
 * @license             http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @see                 http://www.stevesouders.com/blog/?p=25
 * @see                 http://goo.gl/I1n3T
 */

class Auto_version_ext {

    public $name            = "Auto Version";
    public $version         = "0.1";
    public $description     = "Use versioned filenames for better cache control.";
    public $docs_url        = 'https://github.com/michaelgilley/ee_auto_versioning';
    public $settings        = array();
    public $settings_exist  = 'n';
    private $hooks          = array('template_post_parse');

    /**
     * Constructor
     * 
     * @param mixed $settings Settings array or empty string if non exists
     * @return void
     */
    public function __construct($settings=array())
    {
        $this->EE =& get_instance();
        $this->settings = $settings;
    }

    /**
     * Activate Extension
     *
     * @return void
     **/
    public function activate_extension()
    {
        foreach ($this->hooks as $hook)
        {
            $this->_add_hook($hook);
        }
    }

    /**
     * Disable Extension
     *
     * @return void
     **/
    public function disable_extension()
    {
        $this->EE->db->where('class', __CLASS__);
        $this->EE->db->delete('extensions');
    }

    /**
     * Update Extension
     *
     * @param string $current Value of the current version
     * @return mixed Void on update or FALSE on none
     **/
    public function update_extension($current='')
    {
        if ($current == '' OR (version_compare($current, $this->version) === 0))
        {
            // up to date
            return FALSE;
        }

        // update our table row data
        $this->EE->db->where('class', __CLASS__);
        $this->EE->db->update('extensions', array('version' => $this->version));
    }

    /**
     * Add extension hook
     *
     * @access private
     * @param string $name The name of the EE hook
     * @return void
     */
    private function _add_hook($name)
    {
        $this->EE->db->insert('extensions',
            array(
                'class'     => __CLASS__,
                'method'    => $name,
                'hook'      => $name,
                'settings'  => '',
                'priority'  => 10,
                'version'   => $this->version,
                'enabled'   => 'y'
            )
        );
    }

    /**
     * Method for template_post_parse hook
     *
     * @param   string  $temp       Parsed template string
     * @param   bool    $sub        Whether an embed or not
     * @param   integer $side_id    Site ID
     * @return  string              Finished template string
     */
    public function template_post_parse($temp, $sub, $side_id)
    {
        // the latest verion
        if (isset($this->EE->extensions->last_call) AND $this->EE->extensions->last_call)
        {
            $template = $this->EE->extensions->last_call;
        }

        // run only on final template for better performance
        if ( $sub === FALSE )
        {
            if ( strpos($temp, 'autoversion=') !== FALSE AND 
                preg_match_all("/".LD."\s*autoversion=[\042\047]?(.*?)[\042\047]?".RD."/", $temp, $file_matches))
            {
                $versioned = array();

                foreach($file_matches[1] as $filename)
                {
                    $this->EE->TMPL->log_item('autoversion: processing match ' . $filename);

                    $path = $_SERVER['DOCUMENT_ROOT'] . $filename;

                    if ( file_exists($path) )
                    {
                        $pathinfo = pathinfo($filename);
                        $version = '.' . filemtime($path);
                        $versioned[] = $pathinfo['dirname'] . '/' . substr_replace($pathinfo['basename'], $version, strrpos($pathinfo['basename'], '.'), 0);
                    }
                    else
                    {
                        $this->EE->TMPL->log_item('autoversion: ' . $filename . 'does not exist!');
                        $versioned[] = $filename;
                    }
                }

                for($i=0, $count = count($file_matches[0]); $i < $count; ++$i)
                {
                    $temp = str_replace($file_matches[0][$i], $versioned[$i], $temp);
                }
            }
        }

        return $temp;
    }
}

// End of file ext.auto_version.php
// Location: ./system/expressionengine/third_party/auto_version/ext.auto_version.php