<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Debug Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Yura Loginov
 * @link		https://github.com/yuraloginoff/codeigniter-debug-helper.git
 */

// ------------------------------------------------------------------------

/**
 * Readable print_r
 *
 * Prints human-readable information about a variable
 *
 * @access	public
 * @param	mixed 
 */
if ( ! function_exists('dd'))
{
	function dd($var)
	{
		$CI =& get_instance();
		echo '<pre>' . print_r($var, TRUE) . '</pre>';
		exit;
	}
}
//
//
//
//
//
//
/**
 * Readable var_dump
 *
 * Readable dump information about a variable
 *
 * @access	public
 * @param	mixed * 
 */
if ( ! function_exists('vardump'))
{
	function vardump($var)
	{
		$CI =& get_instance();
		echo '<pre>';
		var_dump($var);
		echo '</pre>';
	}
}

if (!function_exists('encrypt_string')) {
    function encrypt_string($plain_text)
    {
        $CI =& get_instance();
        $CI->load->library('encryption');

        return $CI->encryption->encrypt($plain_text);
    }
}

if (!function_exists('decrypt_string')) {
    function decrypt_string($encrypted_text)
    {
        $CI =& get_instance();
        $CI->load->library('encryption');

        return $CI->encryption->decrypt($encrypted_text);
    }
}


/* End of file debug_helper.php */
/* Location: ./application/helpers/debug_helper.php */