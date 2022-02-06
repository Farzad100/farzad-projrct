<?php

namespace App\Http\Controllers\Cdns;

use App\Http\Controllers\Controller;
use App\Models\Doc;
use App\Models\Useful;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class CdnsController extends Controller
{
    //Change these constants as you change download-host
    protected const cdn_name = "cdn1";
    protected const cdn_array = [
        'cdn1' => "https://cdn1.qestaa.ir/",
    ];

    //Request to CDN For grabbing a file from main host
    public static function rfg($doc_id, $address, $size, $table = 'docs')
    {
        if (config('app.env') != 'production') return true;
        $filename = self::create_temp_file($doc_id, $address);
        if ($filename) {
            //Request to cdn for download the file
            $res = Http::withToken(self::token())
                ->post(self::host() . "grabber.php", [
                    'id' => $doc_id,
                    'address' => $address,
                    'size' => $size,
                    'filename' => $filename,
                ]);

            //Delete the file from the temp folder
            File::delete(public_path($filename));

            if (substr($res, 0, 4) == 'done') {
                $id = explode('-', $res)[1];
                $doc_id = is_numeric($id) ? $id : $doc_id;
                DB::table($table)->where('id', $doc_id)->update(['cdn' => self::cdn_name]);
                Storage::delete(Doc::docs_folder($address));
                return 1;
            }
            return $doc_id;
        }
        return $doc_id . ' ctf';
    }

    //Download a file from CDN
    public static function download($address, $cdn = null)
    {
        $res = self::create_link($address, $cdn = null);
        return strlen($res) > 15 ? redirect($res) : $res;
    }

    //Create temp link of a file from CDN
    public static function create_link($address, $cdn = null)
    {
        $res = Http::withToken(self::token($cdn))
            ->post(self::host($cdn) . "downloader.php", [
                'address' => $address,
            ]);

        return strlen($res) > 15 ? (self::host($cdn) . $res) : $res;
    }

    //Remove a file on CDN
    public static function remove($address)
    {
        $res = Http::withToken(self::token())
            ->post(self::host() . "remover.php", [
                'address' => $address,
            ]);

        return $res == 'done';
    }

    //Clean CDNs temp folder (daily cronjob)
    public static function clean_cdn_temps()
    {
        $start = time();
        $cdns = self::cdn_array;
        foreach ($cdns as $name => $host) {
            Http::withToken(self::token($name))
                ->post($host . "cleaner.php", [
                    'mode' => 'all',
                ]);
            if (time() - $start > 50) continue;
        }
        return 1;
    }

    public static function create_temp_file($doc_id, $origin)
    {
        $ext =  explode('.', $origin)[1];
        $name = md5(Useful::random_str(10) . $doc_id) . $doc_id;
        $filename =  $name . '.' . $ext;
        $destination = Doc::temps_path($filename);
        if (File::copy(Doc::docs_path($origin), $destination))
            return Doc::temps_folder($filename);
        return false;
    }

    public static function rfg_batch($condition)
    {
        $s = time();
        $docs = DB::table('docs')->select('id', 'address', 'size')->where($condition)
            ->whereNull('cdn')->where('is_verified', '!=', -1)->get();

        $x = 'a' . "\n";
        foreach ($docs as $doc) {
            $res = self::rfg($doc->id, $doc->address, $doc->size);
            if ($res != 1) $x .= $res . "\n";
            if (time() - $s > 50) break;
        }
        return $x . "\n" . (time() - $s);
    }

    protected static function host($name = null)
    {
        return $name ? self::cdn_array[$name] : self::cdn_array[self::cdn_name];
    }

    protected static function token($name = null)
    {
        return config('globals.cdn.' . ($name ?? self::cdn_name) . '.token');
    }

    //FIXME: 
    public static function grab($init,$file_path)
    {
        $fp = fopen($file_path, 'w+');
        $ch = curl_init($init);
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: ' . TOKEN]);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $res = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return 1;
    }
}
