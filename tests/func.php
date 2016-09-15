<?php
/**
 * get http header
 * @return array
 */
function get_http_header(){
    if (function_exists('xdebug_get_headers')) {
        return xdebug_get_headers();
    }

    return headers_list();
}