<?php

return [
    'title' => 'ربط المتاجر',
    'subtitle' => 'إدارة المتاجر المتصلة',
    'add_new' => 'ربط متجر جديد',
    'update_success' => 'تم تحديث المتجر بنجاح',
    'delete_success' => 'تم حذف المتجر بنجاح',
    'delete_confirm' => 'هل أنت متأكد من حذف ربط هذا المتجر؟',
    'delete_message' => 'لا يمكن التراجع عن هذا الإجراء. سيتم حذف جميع البيانات المرتبطة بهذا المتجر نهائياً.',
    'token_refresh_success' => 'تم تحديث رمز المتجر بنجاح',
    'token_refresh_failed' => 'فشل تحديث رمز المتجر',

    'fields' => [
        'platform' => 'المنصة',
        'merchant_id' => 'معرف التاجر',
        'store_name' => 'اسم المتجر',
        'domain' => 'النطاق',
        'store_email' => 'البريد الإلكتروني',
        'store_phone' => 'رقم الهاتف',
        'status' => 'الحالة',
        'token_expires_at' => 'تاريخ انتهاء الرمز',
        'user' => 'المستخدم',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
    ],

    'placeholders' => [
        'search' => 'البحث عن المتاجر...',
        'platform' => 'اختر المنصة',
        'status' => 'اختر الحالة',
    ],

    'status' => [
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'suspended' => 'معلق',
    ],

    'platforms' => [
        'salla' => 'سلة',
        'zid' => 'زد',
        'wordpress' => 'ووردبريس/ووكومرس',
    ],

    'actions' => [
        'refresh_token' => 'تحديث الرمز',
        'view_webhooks' => 'عرض السجلات',
        'disconnect' => 'قطع الاتصال',
    ],

    'validation' => [
        'status_required' => 'الحالة مطلوبة',
        'store_name_required' => 'اسم المتجر مطلوب',
        'store_email_invalid' => 'يرجى إدخال بريد إلكتروني صحيح',
    ],

    // خاص بسلة
    'salla' => [
        'authorization_success' => 'تم ربط متجر سلة بنجاح',
        'authorization_failed' => 'فشل ربط متجر سلة',
        'welcome_message' => 'مرحباً! تم ربط متجر سلة الخاص بك بنجاح.',
    ],

    // خاص بزد
    'zid' => [
        'authorization_success' => 'تم ربط متجر زد بنجاح',
        'authorization_failed' => 'فشل ربط متجر زد',
        'not_implemented' => 'ربط زد غير متوفر حالياً',
    ],

    // خاص بووردبريس
    'wordpress' => [
        'authorization_success' => 'تم ربط متجر ووردبريس بنجاح',
        'authorization_failed' => 'فشل ربط متجر ووردبريس',
        'not_implemented' => 'ربط ووردبريس غير متوفر حالياً',
    ],
];
