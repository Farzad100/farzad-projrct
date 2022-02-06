<?php

namespace App\Http\Controllers;

use App\Models\Edate;
use App\Models\Useful;
use App\Helpers\XLSMaker;
use App\Models\Doc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ExportController extends Controller
{
    public function download(Request $request)
    {
        return response()->download('TemPerorY/exPs/' . $request->filename)->deleteFileAfterSend();
    }

    public static function excel($data, $title, $mode = null)
    {
        $xls = new XLSMaker();
        if ($mode == 'sheets') {
            foreach ($data as $sheetname => $content)
                $xls->writeSheet($content, $sheetname);
        } else
            $xls->writeSheet($data);
            
        if ($mode == 'strict') $filename = $title . '.xls'; 
        
        else $filename = $title . '__' . Edate::edate('Y-m-d__His') . '.xlsx';
        $directory_name = Useful::randomString(12);
        $directory_path = Doc::temps_path($directory_name);
        if (!File::isDirectory($directory_path)) File::makeDirectory($directory_path, 0755, true, true);
        $xls->writeToFile($directory_path . '/' . $filename);
        return '/' . Doc::temps_folder($directory_name . '/' . $filename);
    }

    public static function csv($data, $title, $mode = null, $type = 'csv')
    {
        $content = "\xEF\xBB\xBF";
        foreach ($data as $row) {
            $str = '';
            foreach ($row as $item)
                $str .= $item . ',';
            $content .= mb_substr($str, 0, -1, 'UTF-8') . "\r\n";
        }
        $directory_name = Useful::randomString(12);
        $directory_path = Doc::temps_path($directory_name);
        if (!File::isDirectory($directory_path)) File::makeDirectory($directory_path, 0755, true, true);
        if ($mode == 'strict') $filename = $title . '.' . $type;
        else $filename = $title . '__' . Edate::edate('Y-m-d__His') . '.' . $type;
        File::put(Doc::temps_path($directory_name . '/' . $filename), $content);
        return '/' . Doc::temps_folder($directory_name . '/' . $filename);
    }

    public function xlsPayments(Request $request)
    {
        $payments = Payment::with(['user', 'order', 'discount'])->orderBy('id', 'desc')->get();
        $data[0] = [
            'ردیف',
            'موبایل',
            'نام کاربر',
            'شماره سفارش',
            'مبلغ (تومان)',
            'بابت',
            'وضعیت',
            'تاریخ پرداخت',
            'شماره پیگیری',
            'شماره کارت',
            'تاریخ شارژ اضافی',
            'توضیحات',
        ];
        $n = 1;
        foreach ($payments as $payment) {
            switch ($payment->type) {
                case 'prepayment':
                    $type = 'پیش پرداخت';
                    break;
                case 'ghest':
                    $type = 'پرداخت قسط';
                    break;
                case 'extra':
                    $type = 'شارژ اضافی';
                    break;
                default:
                    $type = '';
                    break;
            }
            switch ($payment->status) {
                case 100:
                    $status = 'موفق';
                    break;
                case 0:
                    $status = 'ناتمام';
                    break;
                default:
                    $status = 'ناموفق';
                    break;
            }
            $payment->discount ? $discount = "کد تخفیف: " . $payment->discount->code : $discount = '';
            $data[$n] = [
                $n,
                $payment->user->username,
                $payment->user->fname . " " . $payment->user->lname,
                $payment->order->oid,
                $payment->amount,
                $type,
                $status,
                Edate::edateFromCarbon('Y/m/d - H:i', $payment->paid_at),
                $payment->ref_id,
                $payment->card_mask,
                Edate::edateFromCarbon('Y/m/d - H:i', $payment->charged_at),
                $discount,
            ];
            ++$n;
        }
        $xls = new XLSMaker();
        $xls->writeSheet($data);
        $filename = 'payments_' . Edate::edate('Y-m-d__Hi__') . Useful::randomString(6) . '.xlsx';
        $xls->writeToFile(Doc::temp_path() . '/exPs/' . $filename);
        return Useful::ok($filename);
    }
}
