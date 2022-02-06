<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pattern extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public const otp = 1;
    public const user_auth_otp_registration = 2;
    public const user_order_submitted = 3;
    public const user_order_docs_uploaded = 4;
    public const admin_order_docs_uploaded = 5;
    public const user_order_doc_rejected = 6;
    public const user_order_docs_accepted = 7;
    public const user_order_secondary_accepted = 9;
    public const user_order_docs_received_prepayment = 10;
    public const user_order_docs_received_zero_prepayment = 11;
    public const user_order_prepaid = 12;
    public const user_order_prepaid_by_shop = 54;
    public const user_order_charged = 13;
    public const admin_order_prepaid = 14;
    public const admin_order_ghest_paid = 15;
    public const admin_order_secondary_uploaded = 16;
    public const user_order_cheque_reminder_1 = 17;
    public const user_order_cheque_reminder_2 = 18;
    public const user_order_cheque_reminder_2_alt = 19;
    public const user_order_ghest_paid = 46;
    public const user_order_ghest_reminder = 20;
    public const user_order_rejected_by_organ = 21;
    public const user_cta_next_order = 22;
    public const user_cta_silver_order = 23;
    public const user_cta_gold_order = 24;
    public const user_order_completed = 25;
    public const user_order_extra_charge_paid = 47;
    public const admin_order_extra_charge_paid = 48;
    public const user_order_extra_charged = 26;
    public const user_order_by_shop_otp = 27;
    public const user_order_by_shop_otp_passed = 28;
    public const user_registered_by_shop = 32;
    public const user_order_by_shop_submitted = 29;
    public const user_order_by_shop_cta_docs = 30;
    public const user_order_by_shop_doc_rejected = 31;
    public const user_order_by_shop_docs_uploaded = 51;
    public const user_order_by_shop_docs_accepted = 33;
    public const seller_order_by_shop_docs_accepted = 34;
    public const user_order_by_shop_secondary_accepted_prepayment = 35;
    public const seller_order_by_shop_secondary_accepted = 36;
    public const seller_order_by_shop_prepayment = 37;
    public const seller_order_by_shop_charged = 38;
    public const admin_shop_docs_uploaded = 40;
    public const shop_doc_rejected = 41;
    public const shop_activated = 42;
    public const organ_doc_rejected = 43;
    public const user_order_rejected = 44;
    public const seller_order_by_shop_rejected = 45;
    public const order_rejected_inq_sim = 55;
    public const order_rejected_inq_sim_xp = 56;
    public const order_rejected_inq_sim_seller = 58; //oid, mobile
    public const order_rejected_bch = 59; //oid
    public const order_rejected_bch_seller = 60; //oid, mobile

    public const list = [
        self::otp => 'کد یکبار مصرف: %otp%',
        self::user_auth_otp_registration => 'کاربر گرامی، کلیه مراحل ثبت نام و ثبت سفارش در قسطا رایگان است و تا مرحله نهایی هیچ وجهی دریافت نمیشود. کد یکبارمصرف: %otp%',
        self::user_order_submitted => '%name% عزیز، سفارش با شماره %oid% ثبت شد. لطفاً با مراجعه به صفحه سفارش، مدارک موردنیاز را بارگذاری نمایید.',
        self::user_order_docs_uploaded => '%name% عزیز، مدارک شما برای سفارش %oid% در صف بررسی توسط کارشناسان قسطا قرار گرفته و نتیجه حداکثر تا %date% به اطلاعتان خواهد رسید.',
        self::user_order_doc_rejected => 'بررسی کنید. مدارک برای سفارش %oid% بارگذاری شد. مهلت بررسی تا %date%',
        self::user_order_docs_accepted => '%name% عزیز، %doc% به دلیل %reason% رد شد. لطفاً با مراجعه به پنل خود، این مدرک را پس از اصلاح مجدداً بارگذاری نمایید. %extend%',
        self::user_order_docs_received_prepayment => '',
        self::user_order_docs_received_zero_prepayment => '',
        self::user_order_prepaid => '',
        self::user_order_charged => '',
        self::admin_order_prepaid => '',
        self::admin_order_ghest_paid => '',
        self::admin_order_secondary_uploaded => '',
        self::user_order_cheque_reminder_1 => '',
        self::user_order_cheque_reminder_2 => '',
        self::user_order_cheque_reminder_2_alt => '',
        self::user_order_ghest_reminder => '',
        self::user_order_rejected_by_organ => '',
        self::user_cta_next_order => '',
        self::user_cta_silver_order => '',
        self::user_cta_gold_order => '',
        self::user_order_completed => '',
        self::user_order_extra_charged => '',
        self::user_order_by_shop_otp => '',
        self::user_order_by_shop_otp_passed => '',
        self::user_order_by_shop_submitted => '',
        self::user_order_by_shop_cta_docs => '',
        self::user_order_by_shop_doc_rejected => '',
        self::user_registered_by_shop => '',
        self::user_order_by_shop_docs_accepted => '',
        self::seller_order_by_shop_docs_accepted => '',
        self::user_order_by_shop_secondary_accepted_prepayment => '',
        self::seller_order_by_shop_secondary_accepted => '',
        self::seller_order_by_shop_prepayment => '',
        self::seller_order_by_shop_charged => '',
        self::admin_shop_docs_uploaded => '',
        self::shop_doc_rejected => '',
        self::shop_activated => '',
        self::organ_doc_rejected => '',
        self::user_order_rejected => '',
        self::seller_order_by_shop_rejected => '',
        self::user_order_ghest_paid => '',
        self::user_order_extra_charge_paid => '',
        self::admin_order_extra_charge_paid => '',
        self::user_order_by_shop_docs_uploaded => '',
        self::user_order_prepaid_by_shop => '',
    ];
}
