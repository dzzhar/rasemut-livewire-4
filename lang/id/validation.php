<?php

return [
    // custom validation message
    'required' => ':attribute wajib diisi.',
    'min' => ['string' => ':attribute minimal :min karakter.'],
    'after_or_equal' => ':attribute harus setelah atau sama dengan hari ini.',
    'after' => ':attribute harus setelah tanggal mulai.',
    'email' => ':attribute harus berupa email yang valid.',
    'password' => [
        'letters' => ':attribute harus mengandung minimal satu huruf.',
        'mixed' => ':attribute harus mengandung huruf besar dan kecil.',
        'numbers' => ':attribute harus mengandung minimal satu angka.',
        'symbols' => ':attribute harus mengandung minimal satu simbol.',
        'uncompromised' => ':attribute pernah bocor di internet. Gunakan yang lain.',
    ],

    // custom attribute
    'attributes' => [
        'permission_type' => 'Jenis izin',
        'start_date' => 'Tanggal mulai',
        'end_date' => 'Tanggal selesai',
        'description' => 'Keterangan',
        'email' => 'Email',
        'password' => 'Kata sandi',
        'current_password' => 'Kata sandi saat ini',
        'password_confirmation' => 'Konfirmasi kata sandi',
    ]
];
