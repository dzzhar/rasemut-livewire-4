<?php

return [
    // custom validation message
    'required' => 'Kolom :attribute wajib diisi.',
    'string' => 'Kolom :attribute harus berupa string.',
    'min' => ['string' => 'Kolom :attribute minimal :min karakter.'],
    'after_or_equal' => 'Kolom :attribute harus setelah atau sama dengan hari ini.',
    'after' => 'Kolom :attribute harus setelah tanggal mulai.',
    'email' => 'Kolom :attribute harus berupa email yang valid.',
    'unique' => 'Kolom :attribute sudah digunakan.',
    'confirmed' => 'Kolom :attribute konfirmasi tidak cocok.',
    'different' => 'Kolom :attribute harus berbeda dengan :other.',
    'password' => [
        'letters' => 'Kolom :attribute harus mengandung minimal satu huruf.',
        'mixed' => 'Kolom :attribute harus mengandung huruf besar dan kecil.',
        'numbers' => 'Kolom :attribute harus mengandung minimal satu angka.',
        'symbols' => 'Kolom :attribute harus mengandung minimal satu simbol.',
        'uncompromised' => 'Kolom :attribute pernah bocor di internet. Gunakan yang lain.',
    ],

    // custom attribute
    'attributes' => [
        'permission_type' => 'jenis izin',
        'start_date' => 'tanggal mulai',
        'end_date' => 'tanggal selesai',
        'description' => 'keterangan',
        'fullname' => 'nama lengkap',
        'employee_code' => 'kode karyawan',
        'email' => 'email',
        'password' => 'kata sandi',
        'current_password' => 'kata sandi saat ini',
        'password_confirmation' => 'konfirmasi kata sandi',
    ]
];
