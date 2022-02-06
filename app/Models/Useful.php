<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class Useful extends Model
{
    //General
    public static function random_str($length = 10, $mode = null)
    {
        switch ($mode) {
            case 'letter':
            case 'letters':
                $x =  'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'char':
            case 'chars':
                $x =  '123456789abcdefghijklmnopqrstuvwxyz$#^&@({[]})-!ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            default:
                $x =  '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
        }
        return substr(str_shuffle(str_repeat($x, ceil($length / strlen($x)))), 1, $length);
    }

    public static function randomString($length = 10)
    {
        return substr(str_shuffle(str_repeat($x = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

    public static function randomStringX($length = 10)
    {
        $x = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$^&*()_';
        return substr(str_shuffle(str_repeat($x, ceil($length / strlen($x)))), 1, $length);
    }

    public static function randomLetters($length = 10)
    {
        return substr(str_shuffle(str_repeat($x = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

    public static function oum($num)
    {
        switch ($num) {
            case 1:
                $res = "اول";
                break;
            case 2:
                $res = "دوم";
                break;
            case 3:
                $res = "سوم";
                break;
            case 4:
                $res = "چهارم";
                break;
            case 5:
                $res = "پنجم";
                break;
            case 6:
                $res = "ششم";
                break;
            case 7:
                $res = "هفتم";
                break;
            case 8:
                $res = "هشتم";
                break;
            case 9:
                $res = "نهم";
                break;
            case 10:
                $res = "دهم";
                break;
            case 11:
                $res = "یازدهم";
                break;
            case 12:
                $res = "دوازدهم";
                break;
            case 13:
                $res = "سیزدهم";
                break;
            case 14:
                $res = "چهاردهم";
                break;
            case 15:
                $res = "پانزدهم";
                break;
            case 16:
                $res = "شانزدهم";
                break;
            case 17:
                $res = "هفدهم";
                break;
            case 18:
                $res = "هجدهم";
                break;
            case 19:
                $res = "نوزدهم";
                break;
            case 20:
                $res = "بیستم";
                break;
            default:
                $res = "";
                break;
        }
        return $res;
    }

    public static function enum($str = null, $mode = null)
    {
        $str = Edate::tr_num($str, 'en');
        if ($str === 0) return 0;
        if (empty($str)) return null;
        switch ($mode) {
            case 'username':
                $str = substr($str, -10);
                break;
            case 'phone':
            case 'mobile':
                $str = '0' . substr($str, -10);
                break;
            case 'jdate':
            case 'birth':
                $x = explode('-', $str);
                $str = $x[0] . '-' . str_pad($x[1], 2, "0", STR_PAD_LEFT) . '-' . str_pad($x[2], 2, "0", STR_PAD_LEFT);
                break;
            case 'birthx':
                $x = explode('-', $str);
                $str = $x[0] . '/' . str_pad($x[1], 2, "0", STR_PAD_LEFT) . '/' . str_pad($x[2], 2, "0", STR_PAD_LEFT);
                break;
            case 'jdatex':
                $x = explode('-', $str);
                $str = (int) ($x[0] . str_pad($x[1], 2, "0", STR_PAD_LEFT) . str_pad($x[2], 2, "0", STR_PAD_LEFT));
                break;
            case 'card':
                $str = substr($str, 0, 4) . '-' . substr($str, 4, 4) . '-' . substr($str, 8, 4) . '-' . substr($str, 12, 4);
                break;
            default:
                break;
        }
        return $str;
    }

    public static function std($str = null)
    {
        if (is_null($str)) return null;
        $str = Useful::enum($str);
        $arabic = array('ي', 'ك', 'ة');
        $farsi = array('ی', 'ک', 'ه');
        return str_replace($arabic, $farsi, $str);
    }

    public static function domain_extractor($str)
    {
        $website = Edate::tr_num(strtolower($str));
        $domain = str_replace(['www.', 'http://', 'https://'], ['', '', ''], $website);
        $domain = explode('/', $domain)[0];
        $xplode = explode('.', $domain);
        if (count($xplode) > 2) $domain == $xplode[count($xplode) - 2] . '.' . end($xplode);
        if (count($xplode) < 2) return false;
        return $domain;
    }

    public static function str_numbers($str)
    {
        preg_match_all('!\d+!', $str, $res);
        return $res[0];
    }

    public static function yes($result = null, $status = 200, $log_id = null)
    {
        if ($log_id) {
            ClientLog::whereId($log_id)->update([
                'ok' => 1,
                'status' => $status,
                'response' => ['result' => $result],
            ]);
        }
        return response()->json(['ok' => true, 'log_id' => $log_id, 'result' => $result], $status);
    }

    public static function no($error = null, $status = 400, $log_id = null)
    {
        if ($log_id) {
            if (isset($error['message']) && mb_strlen($error['message'], 'UTF-8') > 100) {
                $errorx = $error;
                $errorx['message'] = mb_substr($error['message'], 0, 100, 'UTF-8') . '...';
            }

            DB::table('client_logs')->whereId($log_id)->update([
                'ok' => 0,
                'status' => $status,
                'response' => json_encode(['error' => $errorx ?? $error]),
            ]);
        }
        return response()->json(['ok' => false, 'log_id' => $log_id, 'error' => $error], $status);
    }

    public static function force_update()
    {
        return response()->json(['force_update' => true], 200);
    }

    public static function cheque_number_helper($bank_id = null, $prepend = null, $number = null, $append = null)
    {
        if (!$bank_id || !$number) return null;
        switch ($bank_id) {
            case '012':
                return $prepend ? $prepend . '/' . $number . '/' . $append : $number;

            case '017':
                return $prepend ? $prepend . '-' . $append . '/' . $number : $number;

            default:
                return $prepend ? $prepend . '/' . $number : $number;
        }
    }

    public static function ok($result = null, $options = null)
    {
        $res = ['status' => true, 'result' => $result];
        if (is_array($options)) {
            foreach ($options as $key => $value) {
                if (is_numeric($key)) $res[$value] = true;
                else $res[$key] = $value;
            }
        }
        return $res;
    }

    public static function nok($error = null, $options = null)
    {
        if (is_null($error))
            $res = ['status' => false, 'error' => $error];
        else if (!isset($error['name']) && is_array($error))
            $error = ['name' => $error[0], 'message' => $error[1]];
        else if (!is_array($error))
            $error = ['name' => null, 'message' => $error];

        $res = ['status' => false, 'error' => $error];

        if (is_array($options)) {
            foreach ($options as $key => $value) {
                if (is_numeric($key)) $res[$value] = true;
                else $res[$key] = $value;
            }
        }
        return $res;
    }

    public static function paginate_holder($collection)
    {
        $arr = [];
        $arr['total'] = $collection->total();
        $arr['per_page'] = $collection->perPage();
        $arr['current_page'] = $collection->currentPage();
        $arr['last_page'] = $collection->lastPage();
        $arr['path'] = $collection->getOptions()['path'];
        $arr['next_page_url'] = $collection->nextPageUrl();
        $arr['prev_page_url'] = $collection->previousPageUrl();
        return $arr;
    }

    public static function recollect($collection, $new_data, $extra = null)
    {
        $arr = self::paginate_holder($collection);
        if ($extra) $arr[key($extra)] = $extra[key($extra)];
        $arr['data'] = $new_data;
        return $arr;
    }

    public static function str_has($str, $needle)
    {
        if (strpos($str, $needle) !== false) return true;
        return false;
    }

    public static function is_between($number, $min, $max)
    {
        $number = (int)$number;
        return (($min <= $number) && ($number <= $max));
    }

    public static function zerofill($number, $count)
    {
        return str_pad($number, $count, "0", STR_PAD_LEFT);
    }

    public static function array_search_keyword($keyword, $array, $offset = 0)
    {
        if ($offset > 0) $array = array_slice($array, $offset);
        foreach ($array as $index => $line) {
            if (Useful::str_has($line, $keyword)) return $index + $offset;
        }
        return false;
    }

    public static function date_spans()
    {
        return  [
            [
                'name' => 'today',
                'title' => 'امروز',
                'from_date' => Edate::edate('Y-m-d'),
                'to_date' => Edate::edate('Y-m-d'),
            ],
            [
                'name' => 'yesterday',
                'title' => 'دیروز',
                'from_date' => Edate::edate('Y-m-d', time() - 83000),
                'to_date' => Edate::edate('Y-m-d', time() - 83000),
            ],
            [
                'name' => 'this_month',
                'title' => 'ماه جاری' . ' (' . Edate::edate('F') . ')',
                'from_date' => Edate::this_month_span()[0],
                'to_date' => Edate::this_month_span()[1],
            ],
            [
                'name' => 'last_month',
                'title' => 'ماه گذشته' . ' (' . Edate::last_month_span()[2] . ')',
                'from_date' => Edate::last_month_span()[0],
                'to_date' => Edate::last_month_span()[1],
            ],
            [
                'name' => 'this_year',
                'title' => 'سال جاری',
                'from_date' => Edate::edate('Y-') . '01-01',
                'to_date' => Edate::edate('Y-m-d'),
            ],
            [
                'name' => 'last_year',
                'title' => 'سال گذشته',
                'from_date' => Edate::last_year_span()[0],
                'to_date' => Edate::last_year_span()[1],
            ],
        ];
    }

    public static function list_maker($array)
    {
        $arr = [];
        foreach ($array as $index => $label) {
            array_push($arr, ['label' => $label, 'value' => $index]);
        }
        return $arr;
    }

    public static function list_search($needle, $array, $search_by = 'value')
    {
        foreach ($array as $key => $val) {
            if (isset($val[$search_by]) && $val[$search_by] === $needle) return $array[$key];
        }
        return null;
    }

    public static function round($number, $precision = 1000, $mode = 'round')
    {
        if ($mode == 'up')
            return ceil($number / $precision) * $precision;
        else if ($mode == 'down')
            return round($number / $precision) * $precision;
        else
            return round($number / $precision) * $precision;
    }

    public static function report($text, $params = [], $extra = null)
    {
        foreach ($params as $key => $value)
            $text .= "\n\n" . $key . ":" . "\n" . $value;

        return self::callme($text, config('globals.telegram.report_channel_id'), $extra);
    }

    public static function callme($text, $chat_id = null, $extra = null, $token = null)
    {
        if ($extra) {
            $type = $extra['type'];
            $content = $extra[$type];
            switch ($type) {
                case 'audio':
                case 'photo':
                case 'video':
                    return self::tlgCurl($token, 'send' . str_replace('_', '', $type), [
                        'chat_id' => $chat_id,
                        $type => $content,
                        'caption' => $text,
                        'parse_mode' => 'HTML',
                    ]);
                default:
                    return self::tlgCurl($token, 'sendMessage', [
                        'chat_id' => $chat_id,
                        'text' => $text,
                        'parse_mode' => 'HTML',
                    ]);
            }
        }
        return self::tlgCurl($token, 'sendMessage', [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    public static function tlgCurl($token, $route, $body = [], $method = 'POST')
    {
        if (config('app.env') != 'production') return true;
        if (is_null($token)) $token = config('globals.telegram.bot_token');
        if (array_key_exists('chat_id', $body) && is_null($body["chat_id"])) $body["chat_id"] = config('globals.telegram.report_channel_id');
        if (array_key_exists('user_id', $body) && is_null($body["user_id"])) $body["user_id"] = self::user()->tlg;
        if (array_key_exists('callback_query_id', $body) && is_null($body["callback_query_id"]))
            $body["callback_query_id"] = self::callback()->id;

        $curl = curl_init();
        if ($method == 'GET' && count($body) > 0) {
            $append = '?';
            foreach ($body as $key => $value)
                $append .= '&' . $key . '=' . $value;
            $append = str_replace('?&', '?', $append);
        }
        $opt = [
            CURLOPT_URL => "https://api.telegram.org/bot" . $token . "/" . $route . ($append ?? ''),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CONNECTTIMEOUT => 40,
            CURLOPT_TIMEOUT => 40,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
        ];
        if ($method == 'POST') {
            $opt[CURLOPT_POSTFIELDS] = json_encode(array_filter($body));
            $opt[CURLOPT_HTTPHEADER] = ["Content-Type: application/json"];
        }
        curl_setopt_array($curl, $opt);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public static function ip($mode = null)
    {
        $IP_SERVER = Request::server("SERVER_ADDR");
        $IP_REMOTE = Request::server("REMOTE_ADDR");
        $IP_REAL_AR = Request::server("HTTP_AR_REAL_IP");
        $IP_REAL_X = Request::server("HTTP_X_REAL_IP");
        $IP_FORWARDED_FOR = Request::server("HTTP_X_FORWARDED_FOR");
        if ($mode == 'all') {
            return [];
        }
        return $IP_REAL_X ?? ($IP_REAL_AR ?? $IP_REMOTE ?? null);
    }

    //Image Tools
    public static function imageResize($file, $newWidth = null, $newHeight = null, $keepAR = 1, $justIfBigger)
    {

        $info = getimagesize($file);
        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image_create_func = 'imagecreatefromjpeg';
                $image_save_func = 'imagejpeg';
                $new_image_ext = 'jpg';
                break;

            case 'image/png':
                $image_create_func = 'imagecreatefrompng';
                $image_save_func = 'imagepng';
                $new_image_ext = 'png';
                break;

            case 'image/gif':
                $image_create_func = 'imagecreatefromgif';
                $image_save_func = 'imagegif';
                $new_image_ext = 'gif';
                break;

            default:
                $error = 1;
        }

        $img = $image_create_func($file);
        list($width, $height) = getimagesize($file);

        if (empty($newWidth)) $newWidth = ($newHeight * $width) / $height;
        else if (empty($newHeight)) $newHeight = ($height / $width) * $newWidth;
        else if ($keepAR == 1) {
            if ($width >= $height) $newWidth = ($newHeight * $width) / $height;
            else $newHeight = ($height / $width) * $newWidth;
        }

        $tmp = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        return ['image' => $tmp, 'w' => $newWidth, 'h' => $newHeight];
    }

    public static function imageCrop($file, $newWidth = null, $newHeight = null, $file_mode = 1)
    {
        if ($file_mode == 1) {
            $info = getimagesize($file);
            $mime = $info['mime'];
            switch ($mime) {
                case 'image/jpeg':
                    $image_create_func = 'imagecreatefromjpeg';
                    $image_save_func = 'imagejpeg';
                    $new_image_ext = 'jpg';
                    break;

                case 'image/png':
                    $image_create_func = 'imagecreatefrompng';
                    $image_save_func = 'imagepng';
                    $new_image_ext = 'png';
                    break;

                case 'image/gif':
                    $image_create_func = 'imagecreatefromgif';
                    $image_save_func = 'imagegif';
                    $new_image_ext = 'gif';
                    break;

                default:
                    $error = 1;
            }
            $image = $image_create_func($file);
            list($width, $height) = getimagesize($file);
        } else {
            $image = $file['image'];
            $width = $file['w'];
            $height = $file['h'];
        }

        empty($newWidth) ? $thumb_width = $width : $thumb_width = $newWidth;
        empty($newHeight) ? $thumb_height = $height : $thumb_height = $newHeight;

        $original_aspect = $width / $height;
        $thumb_aspect = $thumb_width / $thumb_height;

        if ($original_aspect >= $thumb_aspect) {
            // If image is wider than thumbnail (in aspect ratio sense)
            $new_height = $thumb_height;
            $new_width = $width / ($height / $thumb_height);
        } else {
            // If the thumbnail is wider than the image
            $new_width = $thumb_width;
            $new_height = $height / ($width / $thumb_width);
        }

        $thumb = imagecreatetruecolor($thumb_width, $thumb_height);

        // Resize and crop
        imagecopyresampled(
            $thumb,
            $image,
            0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
            0 - ($new_height - $thumb_height) / 2, // Center the image vertically
            0,
            0,
            $new_width,
            $new_height,
            $width,
            $height
        );
        return $thumb;
    }
}
