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
    
      // Not found, redirect to the uri of the REDIRECT_DESTINATION_DOMAIN
      basic_redirect($uri);
    }
    else
    {
      // no uri passed, redirect to the root of the REDIRECT_DESTINATION_DOMAIN
      basic_redirect(env('REDIRECT_HOME_URI','/'));
    }    
}


function basic_redirect ($uri)
{
    $destination_domain=env('REDIRECT_DESTINATION_DOMAIN', 'https://www.omatech.com');
    $url=$destination_domain.$uri;
    Log::notice('2. '.$url);

    if (env('CHECK_EXISTENCE_BEFORE_REDIRECT', false))
    {
      $curl = curl_init($url); 
      curl_setopt($curl, CURLOPT_NOBODY, true); 
      $result = curl_exec($curl); 
      if ($result === false) 
      { 
        Log::notice('3. '.$url.' not found, redirecting to root');
        header("HTTP/1.1 301 Moved Permanently"); 
        header("Location: ".$destination_domain.env('REDIRECT_HOME_URI','/')); 
        exit();   
      }
      else
      {
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  
        if ($statusCode == 404) 
        { 
          Log::notice('3. '.$url.' not found, redirecting to root');
          header("HTTP/1.1 301 Moved Permanently"); 
          header("Location: ".$destination_domain.env('REDIRECT_HOME_URI','/')); 
          exit();             
        } 
      }
      Log::notice('3. '.$url.' exists, redirecting');
    }

    header("HTTP/1.1 301 Moved Permanently"); 
    header("Location: ".$url); 
    exit();   
}