<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class MY_Exceptions extends CI_Exceptions
{
     function show_error($heading, $message, $template = 'error_general', $status_code = 500)
     {
         log_message( 'debug', print_r( $message, TRUE ) );
         throw new Exception(is_array($message) ? $message[1] : $message, $status_code );
     }
}