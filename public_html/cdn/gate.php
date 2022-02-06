<?php

const DEBUG_MODE = 1;
const DOX_RELPATH = '../dox/';
const TEMPS_FOLDER = 't3mpDox/';
const MAIN_HOST = 'https://cdns.ghesta.ir/';
const IP_ALLLOWED = '176.9.160.185'; 
const TOKEN = "Bearer Y4CSCxik3q7d69fabET5BdfYicauUU6wiD2ZcorWUHRqAijMfjTY1tlQfBgtPeJTd0A5cV45E9jM42NWlIKQ4Svp0CaJfdhJUETpodvc325KcFu6T5ZNmRE";

defence();

$input = json_decode(file_get_contents('php://input'), true);

function defence()
{
    if ($_SERVER['REMOTE_ADDR'] != IP_ALLLOWED) abort('ip');

    if ($_SERVER['SERVER_PORT'] != 443) abort('ssl');

    if ($_SERVER['HTTP_AUTHORIZATION'] != TOKEN) abort('bilakh');

    return true;
}

function abort($text = '')
{
    echo DEBUG_MODE == 0 ? 'Error: ' . $text : 403;
    exit;
}

function post($route, $fields = null)
{
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => MAIN_HOST . $route,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => 2,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($fields),
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Authorization: " . TOKEN
        ],
    ]); 

    $result = curl_exec($ch);
    curl_close($ch); 
    
    return json_decode($result, true);
}
