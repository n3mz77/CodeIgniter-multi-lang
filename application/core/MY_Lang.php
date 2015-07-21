<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * Language Identifier
 *
 * Adds a language identifier prefix to all site_url links
 *
 * @copyright     Copyright (c) 2011 Wiredesignz
 * @version         0.29
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
class MY_Lang extends CI_Lang
{
    function __construct()
    {
        parent::__construct();
        global $URI, $CFG, $IN;

        $config =& $CFG->config;

        $index_page = $config['index_page'];
        $lang_ignore = $config['lang_ignore'];
        $default_abbr = $config['language_abbr'];
        $lang_uri_abbr = $config['lang_uri_abbr'];
        $lang_ignore_url = $config['lang_ignore_url'];

        $uri_abbr = $URI->segment(1);

        if (in_array($uri_abbr, $lang_ignore_url)) return;// ignore url

        $URI->uri_string = preg_replace("|^\/?|", '/', $URI->uri_string);

        if ($lang_ignore) {

            if (isset($lang_uri_abbr[$uri_abbr])) {

                $IN->set_cookie('user_lang', $uri_abbr, $config['sess_expiration']);
            } else {
                $lang_abbr = $IN->cookie($config['cookie_prefix'] . 'user_lang');
            }

            if (strlen($uri_abbr) == 2) {

                $index_page .= empty($index_page) ? '' : '/';

                $URI->uri_string = preg_replace("|^\/?$uri_abbr\/?|", '', $URI->uri_string);

                header('Location: ' . $config['base_url'] . $index_page . $URI->uri_string);
                exit;
            }

        } else {

            $lang_abbr = $uri_abbr;
        }

        if (isset($lang_uri_abbr[$lang_abbr])) {

            $this->_set_segment();
            $URI->uri_string = preg_replace("|^\/?$lang_abbr|", '', $URI->uri_string);

            $config['language'] = $lang_uri_abbr[$lang_abbr];
            $config['language_abbr'] = $lang_abbr;

            if (!$lang_ignore) {

                $index_page .= empty($index_page) ? $lang_abbr : "/$lang_abbr";

                $config['index_page'] = $index_page.'/';
            }

            $IN->set_cookie('user_lang', $lang_abbr, $config['sess_expiration']);

        } else {

            if (!$lang_ignore) {

                $index_page .= empty($index_page) ? $default_abbr : "/$default_abbr";

                if (strlen($lang_abbr) == 2) {
                    $URI->uri_string = preg_replace("|^\/?$lang_abbr|", '', $URI->uri_string);
                }

                header('Location: ' . $config['base_url'] . $index_page . $URI->uri_string);
                exit;
            }

            $IN->set_cookie('user_lang', $default_abbr, $config['sess_expiration']);
        }
    }
    // end here

    private function _set_segment(){
        global $URI, $CFG, $RTR;

        $countSegments = count($URI->segments);
        $lang_uri_abbr = $CFG->config['lang_uri_abbr'];
        if ($countSegments > 0) {
            $lang = $URI->segments[1];
            if (array_key_exists($lang, $lang_uri_abbr)) {
                $URI->uri_string = str_replace('/'.$lang, '', $URI->uri_string);

                $segments[0] = null;
                foreach ($URI->segments as $s) {
                    $segments[] = $s;
                }
                unset ($segments[0]);
                $URI->segments = $segments;

                $URI->rsegments[1] = $RTR->default_controller;
                if ($RTR->class == $lang) {
                    $RTR->class = $RTR->default_controller;
                }
            }

        }
    }
}

/* End of file */