<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Cdns\CdnsController;
use App\Models\Account;
use App\Models\Cheque;
use App\Models\Useful;
use App\Models\Chunk;
use App\Models\Doc;
use App\Models\Edate;
use App\Models\Order;
use App\Models\Organ;
use App\Models\Pattern;
use App\Models\Shop;
use App\Models\Sms;
use App\Models\User;
use App\Packages\Venesh;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Madzipper;
use Intervention\Image\Facades\Image;

class DocController extends Controller
{

  public function upload_cheque(Request $request)
  {
    $oid = $request->oid;
    $type = strtolower($request->type);
    $badge = $request->badge;
    $formats = ['jpeg', 'jpg', 'png'];

    $order = Order::oid($oid);

    (new OrderController())->guard($order);

    $file = $request->file;
    $newFolder = Edate::edate('Ym') . '/' . $type;

    $format = $file->extension();
    if (!in_array($format, $formats))
      return Useful::nok('لطفاً فقط تصویر بارگذاری کنید');

    $move = Storage::put(Doc::docs_folder($newFolder), $file);
    $newAddress = explode(Doc::docs_folder(), $move)[1];

    if ($move) {
      $file_path = Doc::docs_path($newAddress);
      $file_path = self::modify_image($file_path);
      $newAddress = explode('dox/', $file_path)[1];

      $size = ceil(Storage::size(Doc::docs_folder($newAddress)) / 1024);

      $account_id = Doc::select('account_id')->where([
        'type' => 'cheque',
        'order_id' => $order->id,
        'is_verified' => 1
      ])->first()->account_id;

      if ($type === 'chf')
        $cheque = Cheque::updateOrCreate([
          'order_id' => $order->id,
          'type' => $type,
          'badge' => $badge
        ], [
          'account_id' => $account_id,
          'address' => $newAddress,
          'format' => $format,
          'size' => $size,
          'cdn' => null,
          'uploaded_at' => Carbon::now(),
          'uploaded_by' => Auth::user()->id,
          'is_verified' => 0,
          'is_readable' => 0,
          'reason' => null,
        ]);
      else {
        $cheque = Cheque::updateOrCreate([
          'order_id' => $order->id,
          'type' => $type,
          'series' => $badge
        ], [
          'address' => $newAddress,
          'format' => $format,
          'size' => $size,
          'cdn' => null,
          'uploaded_at' => Carbon::now(),
          'uploaded_by' => Auth::user()->id,
          'is_verified' => 0,
        ]);
      }

      CdnsController::rfg($cheque->id, $newAddress, $size, 'cheques');

      if ($type == 'chf') {
        //Image Processing
        $cheque_url = self::downloadFile($cheque->id, 'cheques');
        $venesh = Venesh::chequeReader(self::getFileContent($cheque_url), false);

        if (isset($venesh['Message']) && $venesh['Message'] == 'Success') {
          $array = [
            'isbn' => $venesh['Data']['ChequeNumber'],
            'is_readable' => 1,
            'isbn_verified_at' => Carbon::now(),
          ];
          if (empty($venesh['Data']['Inquiry'])) {
            $array['isbn_verified_at'] = null;

            Useful::report("⁉️" . " " . "نتیجه پردازش تصویر چک مشکوک بود", [
              'شماره سفارش' => $order->oid,
              'آیدی سفارش' => $order->id,
              'آیدی مدرک در جدول چکها' => $cheque->id,
              'شماره تشخیص داده شده' => Useful::enum($venesh['Data']['ChequeNumber'], 'card')
            ], [
              'type' => 'photo',
              'photo' => $cheque_url
            ]);

            Useful::report($venesh['Data']['ChequeNumber']);
          } else if (
            isset($venesh['Data']['Inquiry']['OwnerName']) &&
            abs(strlen(Useful::std($venesh['Data']['Inquiry']['OwnerName'])) - strlen($order->user->full_name)) > 2
          ) {
            Useful::report("‼️" . " " . "چک پردازش شده به نام شخص دیگری است!", [
              'شماره سفارش' => $order->oid,
              'آیدی سفارش' => $order->id,
              'آیدی مدرک در جدول چکها' => $cheque->id,
              'شماره تشخیص داده شده' => Useful::enum($venesh['Data']['ChequeNumber'], 'card'),
              'نام تشخیص داده شده' => Useful::std($venesh['Data']['Inquiry']['OwnerName']),
              'نام واقعی کاربر' => $order->user->full_name,
            ], [
              'type' => 'photo',
              'photo' => $cheque_url
            ]);

            Useful::report($venesh['Data']['ChequeNumber']);
          }

          if (isset($venesh['Data']['Inquiry']['StatusDescription'])) {
            $venesh['Data']['Inquiry']['FaStatus'] = '';
            $venesh['Data']['Inquiry']['StatusDescription'] = '';
          }
        } else {
          $array = ['is_readable' => 0];

          Useful::report("📸" . " " . "پردازش تصویر چک ناموفق بود", [
            'شماره سفارش' => $order->oid,
            'آیدی سفارش' => $order->id,
          ], [
            'type' => 'photo',
            'photo' => $cheque_url
          ]);
        }

        $cheque->update(array_merge($array, [
          'provider' => 'venesh',
          'meta' => $venesh,
        ]));

        return Useful::ok(['isbn' => $venesh['Message'] == 'Success' ? $venesh['Data']['ChequeNumber'] : null]);
      }

      return Useful::ok(['chb' => 1]);
    }

    return Useful::nok('متأسفانه هنگام ذخیره سازی فایل مشکلی پیش آمده');
  }

  public static function modify_image($file_path, $w = 1920, $h = 1080, $rotate = null, $convert_format = 'jpg')
  {
    $img = Image::make($file_path);
    $ext = substr($file_path, -4);
    switch ($ext) {
      case '.jpg':
      case '.png':
        $ext = substr($file_path, -3);
        $fp_without_ext = substr($file_path, 0, -3);
        break;
      case 'jpeg':
      default:
        $fp_without_ext = substr($file_path, 0, -4);
        break;
    }
    $file_path = $fp_without_ext . $convert_format;

    if ($ext != $convert_format) $img->encode('jpg', 100);

    if (is_null($rotate) && $img->width() < $img->height()) $img->rotate(90);
    else if (is_numeric($rotate) && $rotate > 0) $img->rotate($rotate);

    if ($img->width() > $w || $img->height() > $h)
      $img->resize($w, $h, function ($const) {
        $const->aspectRatio();
      });

    $img->save($file_path);

    return $file_path;
  }

  public static function getFileContent($url)
  {
    if (config('app.env') == 'production')
      return file_get_contents($url);
    else
      return File::get(Doc::temps_path(explode(Doc::temps_folder(), $url)[1]));
  }

  public function upload(Request $request)
  {
    $uri = Route::current()->uri;
    $urix = explode('/', $uri);
    $role = $urix[1];

    $extra = $request->extra;
    if (!empty($extra)) $user = User::find($extra);
    else $user = Auth::user();
    $uploader = Auth::user();

    $user_id = $user->id;
    $type = $request->type;
    $uuid = $request->dzuuid . $user_id;
    $index = $request->dzchunkindex;

    switch ($role) {
      case 'orders':
        $oid = $request->oid;
        $order = Order::oid($oid);
        if ($uploader->id != $user->id && !User::is_admin()) {
          if (User::is_shop()) {
            $shop_id = Shop::select('id')->where('user_id', $uploader->id)->first()->id;
            if ($order->shop_id != $shop_id)
              return Useful::nok(['forbidden_order', 'دسترسی به اطلاعات این سفارش غیرمجاز است']);
          } else if (User::is_organ()) {
            return Useful::nok(['forbidden_order', 'دسترسی به اطلاعات این سفارش غیرمجاز است']);
          } else {
            return Useful::nok(['forbidden_order', 'دسترسی به اطلاعات این سفارش غیرمجاز است']);
          }
        }
        $where = ['order_id' => $order->id, 'type' => $type];
        if (in_array($type, Doc::$user_all)) {
          $where = ['user_id' => $user_id, 'order_id' => $order->id, 'type' => $type];
        }
        $allowed_types = Doc::$order_all;
        break;
      case 'shop':
        $shop_id = Shop::query()->select('id')->where('user_id', $user_id)->first()->id;
        $where = ['shop_id' => $shop_id, 'type' => $type];
        $allowed_types = Doc::$shop_all;
        break;
      case 'organ':
        $organ_id = Organ::select('id')->where('user_id', $user_id)->first()->id;
        $where = ['organ_id' => $organ_id, 'type' => $type];
        $allowed_types = Doc::$organ_all;
        break;
      default:
        $where = ['user_id' => $user_id, 'type' => $type];
        $allowed_types = Doc::$user_all;
        break;
    }

    if (!in_array($type, $allowed_types))
      return Useful::nok(['invalid_type', 'نوع مدرک بارگذاری شده معتبر نیست']);

    if (Doc::where($where)->exists()) {
      $doc = Doc::where($where)->first();
      if ($doc->is_verified > -1) return abort(403, 'این فایل قبلاً بارگذاری شده است');
    }

    $file = $request->file('file');
    $client_name = $file->getClientOriginalName();
    $ext = $file->extension();
    $formats = Doc::$types[$type]['formats'];
    $formats[] = 'bin';
    if (!in_array($ext, $formats)) abort(500, 'Format is invalid!');
    if (mb_strlen($client_name) > 100) abort(500, 'Filename is too long!');

    $chunk = Chunk::create([
      'user_id' => $user_id,
      'file_id' => $uuid,
      'part' => $index,
      'client_name' => $client_name
    ]);

    $uploaded = Storage::put(Doc::chunks_folder($uuid), $file);
    $filename = explode('/', $uploaded);
    $file_path = Doc::chunks_path($uuid) . '/' . end($filename);

    if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
      if (in_array($type, ['report', 'backup']))
        self::modify_image($file_path, 4000, 3000, 0);
      else if (in_array($type, ['logo']))
        self::modify_image($file_path, 512, 512, 0, 'png');
      else
        self::modify_image($file_path, 1920, 1080, 0);
    }

    if ($uploaded && $chunk->update(['name' => end($filename)])) {
      return Useful::ok(['type' => $type, 'track_id' => $uuid]);
    }

    return abort(500);
  }

  public function concat(Request $request)
  {
    $uri = Route::current()->uri;
    $urix = explode('/', $uri);
    $mode_location = count($urix) - 3;
    $type = strtolower($request->type);
    $extra = $request->extra;
    $bank_id = $request->bank_id;
    $formats = Doc::$types[$type]['formats'];

    $skip = ($urix[$mode_location] == 'skip' && $type == 'backup') ? true : false;

    if (!empty($extra)) $user = User::find($extra);
    else $user = Auth::user();
    $user_id = $user->id;
    $oid = $request->oid;
    $uploader = Auth::user();
    empty($oid) ? $order = null : $order = Order::oid($oid);
    if ($order && $uploader->id != $user->id && !User::is_admin()) {
      if (User::is_shop()) {
        $shop_id = Shop::select('id')->where('user_id', $uploader->id)->first()->id;
        if ($order->shop_id != $shop_id)
          return Useful::nok(['forbidden_order', 'دسترسی به این بخش غیرمجاز است']);
      } else if (User::is_organ())
        return Useful::nok(['forbidden_order', 'دسترسی به اطلاعات این سفارش غیرمجاز است']);
      else
        return Useful::nok(['forbidden_order', 'دسترسی به اطلاعات این سفارش غیرمجاز است']);
    }

    if ($skip) {
      $newAddress = 'skip';
      $format = null;
      $move = true;
      $file_ids = [];
    } else {
      $file_ids = $request->input('files');
      $address = $this->concat_chunks($file_ids);
      $filename_parts = explode('/', $address);
      $Ym = Edate::edate('Ym');
      $newAddress = $Ym . '/' . $type . '/' . end($filename_parts);
      $tmp = explode('.', $newAddress);
      $format = end($tmp);
      $move = Storage::move($address, Doc::docs_folder($newAddress));
    }

    $scope = Doc::$types[$type]['scope'];
    if ($move) {
      switch ($scope) {
        case 'user':
          $array = ['user_id' => $user_id, 'type' => $type];
          if ($oid) $array = ['user_id' => $user_id, 'order_id' => $order->id, 'type' => $type];
          break;
        case 'order':
          $array = ['order_id' => $order->id, 'type' => $type];
          break;
        case 'shop':
          $shop_id = Shop::select('id')->where('user_id', $user_id)->first()->id;
          $array = ['shop_id' => $shop_id, 'type' => $type];
          break;
        case 'organ':
          $organ_id = Organ::select('id')->where('user_id', $user_id)->first()->id;
          $array = ['organ_id' => $organ_id, 'type' => $type];
          break;
      }
      if ($type == 'cheque') {
        $request->validate([
          'iban' => ['required', 'string', 'min:24', 'max:26'],
          'branch_name' => ['required', 'string', 'min:1', 'max:30'],
          'branch_code' => ['required', 'string', 'min:1', 'max:10'],
        ]);

        $iban = Useful::enum($request->iban);
        $branch_name = Useful::std($request->branch_name);
        $branch_code = Useful::std($request->branch_code);

        $bank_id = substr($iban, 2, 3);

        $account = Account::updateOrCreate([
          'user_id' => $order->user_id,
          'iban' => $iban,
        ], [
          'branch_name' => Useful::std($branch_name),
          'branch_code' => Useful::std($branch_code),
          'bank_id' => $bank_id
        ]);

        $account_id = $account->id;
      } else if ($type == 'report') {
        $account_id = Doc::select('account_id')->where(['order_id' => $order->id, 'type' => 'cheque'])->orderBy('id', 'desc')->first()->account_id;
      }

      $size = $newAddress == 'skip' ? null : ceil(Storage::size(Doc::docs_folder($newAddress)) / 1024);
      $guess = in_array($type, ['report', 'backup']) ? (($format != 'pdf' || $size > 2000) ? 0 : null) : null;

      /* if (!$skip && in_array($format, ['xls', 'xlsx']) && in_array($type, ['report', 'backup'])) {
        $guess = null;
      } else {
        $guess = null;
      } */

      $old = DB::table('docs')->select('id', 'cdn', 'address')->where($array)->first();

      $doc = Doc::updateOrCreate($array, [
        'uploaded_by' => Auth::user()->id,
        'account_id' => $account_id ?? null,
        'address' => $newAddress,
        'format' => $format,
        'is_readable' => $guess,
        'is_readable_guess' => $guess,
        'size' => $size,
        'cdn' => null,
        'uploaded_at' => Carbon::now(),
        'decided_at' => null,
        'is_verified' => 0,
        'bank_id' => $bank_id,
        'reason' => null,
      ]);

      if ($newAddress != 'skip') CdnsController::rfg($doc->id, $newAddress, $size);

      if ($scope == 'order' && $order->status == 'wait_for_cheques') {
        $review_docs = OrderController::review_docs($order);
        if ($review_docs[0]['new_status'] == 'check_secondary') {
          if (empty($order->secondary_uploaded_at)) {
            $order->update(['secondary_uploaded_at' => Carbon::now()]);
            Sms::send(
              config('globals.support.mobile'),
              "تصویر تضامین برای سفارش " . $order->oid . " بارگذاری شد."
            );
          }
        } else {
          $order->update(['secondary_uploaded_at' => null]);
        }
      }

      if ($old && $old->address != 'skip') {
        $old->cdn ? CdnsController::remove($old->address) : Storage::delete(Doc::docs_folder($old->address));
      }

      return $type === 'cheque' ? Useful::ok(null, ['force_update']) : Useful::ok();
    }
    return Useful::nok('متأسفانه هنگام ذخیره سازی فایل مشکلی پیش آمده');
  }

  public function concat_chunks($file_ids)
  {
    $files = [];
    foreach ($file_ids as $index => $file_id) {
      $chunks = Chunk::where('file_id', $file_id)->orderBy('part', 'asc')->orderBy('id', 'asc')->get(); //TODO: dbo
      $rel_path = Doc::chunks_folder($file_id . '/');
      $ext = explode('.', $chunks[0]['name']);
      $ext = end($ext);
      if ($ext == 'bin') {
        $ext_client = explode('.', $chunks[0]['client_name']);
        $ext = end($ext_client);
      }
      $newFilename = 'f' . Useful::zerofill($index + 1, 3) . Useful::randomLetters(4) . $file_id . '.' . $ext;
      foreach ($chunks as $chunk) {
        $chunk_path = Doc::chunks_path($file_id . '/' . $chunk->name);
        $file = fopen($chunk_path, 'rb');
        $buff = fread($file, filesize($chunk_path));
        fclose($file);
        $final = fopen(Doc::chunks_path($file_id . '/' . $newFilename), 'ab');
        $write = fwrite($final, $buff);
        fclose($final);
      }
      $files[] = $rel_path . $newFilename;
    }
    if (count($files) == 1) $file = $files[0];
    else $file = $this->zip_chunks($files);
    return $file;
  }

  public function zip_chunks($files)
  {
    $zipAddress =  explode('.', $files[0])[0] . Useful::randomString(4) . '.zip';
    $xfiles = [];
    foreach ($files as $file) $xfiles[] = storage_path('app/') . $file;
    Madzipper::make(storage_path('app/') . $zipAddress)->add($xfiles)->close();
    return $zipAddress;
  }

  public function download(Request $request)
  {
    $name = $request->id;
    $format = $request->format;
    $cdn = $request->cdn;

    if ($cdn)
      return CdnsController::download(str_replace('-slash-', '/', $name), $cdn);
    return redirect('https://qestaa.ir/' . Doc::temps_folder($name . '.' . $format));
  }

  public function download_it(Request $request)
  {
    $link = DocController::downloadFile($request->id, 'cheques');
    return Useful::ok(['url' => $link], ['action_type' => 'open_url']);
  }

  //ADMIN
  public function single(Request $request)
  {
    if (!User::is_admin()) abort(403);
    $table = $request->scope ? $request->scope : 'docs';
    $doc = DB::table($table)->select('id', 'address', 'cdn', 'format')->where('id', $request->id)->first();

    if ($doc->address == 'skip')
      return Useful::nok(['nadare', 'کاربر اظهار کرده که پرینت حساب پشتیبان ندارد.']);

    if ($doc->cdn) {
      return '/dox/' . str_replace('/', '-slash-', $doc->address) . '?cdn=' . $doc->cdn;
    } else {
      $file = Doc::docs_path($doc->address);
      $name = md5(Useful::randomString(6) . $doc->id) . $doc->id;
      $filename =  $name . '.' . $doc->format;
      $destination = Doc::temps_path($filename);

      File::copy($file, $destination);

      return '/dox/' . $name . '?format=' . $doc->format;
    }
  }

  //ADMIN
  public function accept(Request $request)
  {
    $id = $request->id;
    $operator_id = Auth::user()->id;
    $doc = Doc::find($id);
    if ($doc->update([
      'is_verified' => 1,
      'decided_by' => $operator_id,
      'decided_at' => Carbon::now()
    ])) {
      if ($doc->type == 'ncf') User::find($doc->user_id)->update(['verified_at' => Carbon::now()]);
      if ($doc->order_id) {
        $order = Order::find($doc->order_id);
        OrderController::review_docs($order);
      } else if ($doc->shop_id) {
        $shop = Shop::find($doc->shop_id);
        ShopController::review_docs($shop);
      } else if ($doc->organ_id) {
        $organ = Organ::find($doc->organ_id);
        OrganController::review_docs($organ);
      }
      return Useful::ok();
    }
    return Useful::nok(['dbdoc-a', 'خطا در اتصال به دیتابیس']);
  }

  //ADMIN
  public function reject(Request $request)
  {
    $id = $request->id;
    $reason = $request->reason;
    $operator_id = Auth::user()->id;
    $doc = Doc::find($id);

    $doc->update([
      'is_verified' => -1,
      'reason' => $reason,
      'decided_by' => $operator_id,
      'decided_at' => Carbon::now(),
    ]);

    if ($doc->order_id) {
      $note_type = 'order';
      $note_id = $doc->order_id;
      $order = Order::find($doc->order_id);
      OrderController::review_docs($order);

      //SMS
      if ($reason) {
        $extend = $doc->type == 'cheques' ? '(پس از اصلاح چک موردنظر، تصویر همه چکها را مجدداً آپلود کنید)' : '';
        $user = User::info($order->user_id);
        if ($order->shop_id) {
          Sms::send(
            Shop::mobile($order->shop_id),
            Pattern::user_order_by_shop_doc_rejected,
            [
              'name' => $user->full_name,
              'doc' => Doc::$types[$doc->type]['title'],
              'reason' => $reason,
              'oid' => $order->oid,
              'extend' => $extend
            ]
          );
        } else {
          Sms::send(
            $user->mobile,
            Pattern::user_order_doc_rejected,
            [
              'name' => $user->fname,
              'doc' => Doc::$types[$doc->type]['title'],
              'reason' => $reason,
              'extend' => $extend
            ]
          );
        }
      }
    } else if ($doc->shop_id) {
      $note_type = 'shop';
      $note_id = $doc->shop_id;
      $shop = Shop::find($doc->shop_id);
      ShopController::review_docs($shop);

      //SMS
      if ($reason) {
        Sms::send(
          Shop::mobile($doc->shop_id),
          Pattern::shop_doc_rejected,
          [
            'doc' => Doc::$types[$doc->type]['title'],
            'reason' => $reason,
          ]
        );
      }
    } else if ($doc->organ_id) {
      $note_type = 'organ';
      $note_id = $doc->organ_id;
      $organ = Organ::find($doc->organ_id);
      OrganController::review_docs($organ);

      //SMS
      if ($reason) {
        Sms::send(
          Organ::mobile($doc->organ_id),
          Pattern::organ_doc_rejected,
          [
            'doc' => Doc::$types[$doc->type]['title'],
            'reason' => $reason,
          ]
        );
      }
    } else if ($doc->user_id) {
      $note_type = 'user';
      $note_id = $doc->user_id;
      $user = User::info($doc->user_id);
      if ($reason) {
        Sms::send(
          $user->mobile,
          Pattern::user_order_doc_rejected,
          [
            'name' => $user->fname,
            'doc' => Doc::$types[$doc->type]['title'],
            'reason' => $reason,
          ]
        );
      }
    }
    if ($reason) NoteController::make($note_type, $note_id, $reason, $doc->type, 'reject_doc');
    return Useful::ok();
  }

  //ADMIN
  public function accept_cheque(Request $request)
  {
    $id = $request->id;
    $case = $request->case;
    $operator_id = Auth::user()->id;
    $ch = Cheque::find($id);

    if ($case === 'reset') {
      $ch->update([
        'is_verified' => 0,
        'decided_at' => null,
        'photo_verified_at' => null,
        'submit_verified_at' => null,
      ]);
      return Useful::ok();
    }

    if (!in_array($case, ['photo', 'data', 'submit']))
      return Useful::nok('مورد نامشخص');

    $ch->update([
      'is_verified' => 0,
      $case . '_verified_at' => Carbon::now(),
      'decided_by' => $operator_id,
      'decided_at' => null,
    ]);

    if ($ch->photo_verified_at && $ch->submit_verified_at) {
      $ch->update([
        'is_verified' => 1,
        'is_submitted' => 1,
        'decided_by' => $operator_id,
      ]);
    }

    return Useful::ok();
  }

  //ADMIN
  public function reject_cheque(Request $request)
  {
    $id = $request->id;
    $reason = $request->reason;

    if (count($reason) === 0) return Useful::nok('دلیل رد وجود ندارد');

    $operator_id = Auth::user()->id;
    $ch = Cheque::find($id);

    $arr = [
      'is_verified' => 0,
      'reason' => $reason,
      'decided_by' => $operator_id,
      'photo_verified_at' => null,
      'submit_verified_at' => null,
      'decided_by' => $operator_id,
      'decided_at' => Carbon::now(),
    ];

    $reject_it = false;
    $r = '';
    foreach ($reason as $rsn) {
      $reject_option = Useful::list_search($rsn, Doc::reject_options);
      $r .= "<li>" . $reject_option['text'] . "</li>";

      if (isset($reject_option['updates'])) {
        foreach ($reject_option['updates'] as $key => $value) $arr[$key] = $value;
      }

      if (isset($reject_option['options'])) {
        if (in_array('reject', $reject_option['options'])) $reject_it = true;
        if (in_array('delete', $reject_option['options'])) {
          $ch->delete();
          return Useful::ok();
        }
      }
    }
    $arr['comment'] = $r;

    $ch->update($arr);

    if (isset($arr['need_chb']) && $arr['need_chb'] === 1){
      Cheque::where(['type' => 'chb', 'series' => $ch->id, 'order_id' => $ch->order_id])->delete();
      $reject_it = true;
    }

    if ($reject_it)
      $ch->update([
        'series' => null,
        'data_verified_at' => null,
        'isbn' => null,
        'isbn_verified_at' => null,
        'is_submitted' => null,
        'photo_verified_at' => null,
        'submit_verified_at' => null,
        'is_verified' => -1,
      ]);

    NoteController::make('order', $ch->order_id, $r, 'chf', 'reject_ch', ['badge' => $ch->badge]);

    return Useful::ok();
  }

  //ADMIN
  public function is_readable(Request $request)
  {
    $id = $request->id;
    $is_readable = $request->is_readable;
    if (Doc::whereId($id)->update(['is_readable' => $is_readable]))
      return Useful::ok();
    return Useful::nok(['dbdoc-r', 'خطا در اتصال به دیتابیس']);
  }

  public static function archive_old_heavy()
  {
    return Doc::sum('size');
    $docs = Doc::select('id', 'type', 'address')->where('address', 'like', 'old/%')->where('is_archived', 0)->orderBy('id', 'asc')->get();
    foreach ($docs as $doc) {
      $file = $doc->address;
      $file_parts = explode('/', $file);
      if (count($file_parts) < 2) continue;
      if (Doc::where('address', 'like', '%' . end($file_parts))->count() > 1) return end($file_parts);
      continue;
      $dest_file = $file_parts[0] . '/' . $doc->type . '/' . end($file_parts);
      Storage::move(Doc::docs_folder($doc->address), Doc::docs_folder($dest_file));
      $doc->update(['address' => $dest_file, 'is_archived' => 1]);
    }
    return 1;
  }

  //ADMIN JUST FOR DEBUG AND TEST
  public static function downloadFile($doc_id, $table = 'docs')
  {
    $doc = DB::table($table)->select('id', 'address', 'cdn', 'format')->where('id', $doc_id)->first();

    if ($doc->address == 'skip')
      return Useful::nok('کاربر اظهار کرده که پرینت حساب پشتیبان ندارد.');

    if ($doc->cdn)
      return CdnsController::create_link($doc->address . '---' . $doc->id, $doc->cdn);
    else {
      $file = Doc::docs_path($doc->address);
      $name = $doc->id . '-' . md5(Useful::randomString(6) . $doc->id);
      $filename =  $name . '.' . $doc->format;
      $destination = Doc::temps_path($filename);

      File::copy($file, $destination);

      // return redirect('https://qestaa.ir/' . Doc::temps_folder($name . '.' . $doc->format));
      return (config('app.env') == 'production' ? 'https://qestaa.ir/' : config('app.url') . '/') . Doc::temps_folder($name . '.' . $doc->format);
    }
  }
}
