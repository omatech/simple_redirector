<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


if (php_sapi_name()!=='cli')
{
    $uri='';
    if (isset($_SERVER['REQUEST_URI']))  $uri=$_SERVER['REQUEST_URI'];
    
    if ($uri) 
    {
      Log::notice('1. '.$_SERVER['HTTP_HOST'].$uri);
      // Look for the uri
      $ret=DB::table(env('REDIRECTS_TABLE', 'omatech_simple_redirects'))->where('original_uri', "$uri")->first();
      if ($ret) basic_redirect($ret->redirect_uri);
    
      // Look for the uri and slash 
      $ret=DB::table(env('REDIRECTS_TABLE', 'omatech_simple_redirects'))->where('original_uri', "$uri/")->first();
      if ($ret) basic_redirect($ret->redirect_uri);
    
      // Not found, redirect to the root of the REDIRECT_DESTINATION_DOMAIN
      basic_redirect('/');
    }
    else
    {
      // no uri passed, redirect to the root of the REDIRECT_DESTINATION_DOMAIN
      basic_redirect('/');
    }    
}


function basic_redirect ($url)
{
    Log::notice('2. '.$url);
    header("HTTP/1.1 301 Moved Permanently"); 
    header("Location: ".env('REDIRECT_DESTINATION_DOMAIN', 'https://www.omatech.com')."$url"); 
    exit();   
}