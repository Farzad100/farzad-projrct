<?php

namespace App\Http\Controllers;

use App\Models\Edate;
use App\Models\Permission;
use App\Models\Useful;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $perms = Permission::with(['user' => function ($q) {
            $q->select('id', 'fname', 'lname', 'username');
        }])->paginate(100);

        $new = [];
        $n = 0;
        foreach ($perms as $perm) {
            $new[$n]['id'] = $perm->id;

            $col['name'] = [
                'title' => 'کاربر',
                'value' => $perm->user->full_name,
                'addition' =>  $perm->user->mobile
            ];

            $col['scope'] = [
                'title' => 'محدوده دسترسی',
                'value' => Permission::$scopes[$perm->scope],
                'addition' => ''
            ];

            $col['can_read'] = [
                'title' => 'مشاهده',
                'value' => '',
                'icon' => $perm->can_read > 0 ? 'check' : 'times',
                'css_classes' => 'text-center ' . ($perm->can_read > 0 ? 'text-success' : 'text-danger'),
            ];

            $col['can_write'] = [
                'title' => 'ویرایش',
                'value' => '',
                'icon' => $perm->can_write > 1 ? 'check' : 'times',
                'css_classes' => 'text-center ' . ($perm->can_write > 1 ? 'text-success' : 'text-danger'),
            ];

            $col['created_at'] = [
                'title' => 'تاریخ اعطا',
                'value' => Edate::edateFromCarbon('j F Y', $perm->created_at),
                'addition' => Edate::edateFromCarbon('H:i', $perm->created_at)
            ];

            $col['action'] = [
                'type' => 'button',
                'buttons' => [
                    [
                        'value' => 'حذف دسترسی',
                        'icon' => 'trash',
                        'css_classes' => 'text-danger',
                        'type' => 'confirm',
                        'method' => 'post',
                        'endpoint' => '/salar/permissions/' . $perm->id . '/delete',
                    ],
                ],
            ];

            $new[$n]['td'] = $col;
            $col = null;
            ++$n;
        }
        return Useful::recollect($perms, $new);
    }

    public function delete(Request $request)
    {
        if (Permission::find($request->id)->delete()) return Useful::ok();
        return Useful::nok(['dbsh', 'خطا در اتصال به دیتابیس']);
    }
}
