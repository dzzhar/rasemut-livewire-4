<?php

return [
    // custom validation message
    'required' => 'Kolom :attribute wajib diisi.',
    'required_without' => 'Kolom :attribute wajib diisi apabila :values tidak diisi.',
    'string' => 'Bidang :attribute harus berupa teks.',
    'min' => ['string' => 'Bidang :attribute minimal :min karakter.'],
    'after_or_equal' => 'Input :attribute harus tanggal hari ini atau setelahnya.',
    'max' => ['file' => 'Ukuran :attribute maksimal :max KB.'],
    'after' => 'Input :attribute harus setelah tanggal mulai.',
    'email' => 'Alamat :attribute harus berupa email yang valid.',
    'unique' => 'Data :attribute sudah digunakan.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'different' => 'Bidang :attribute harus berbeda dengan :other.',
    'image' => 'File :attribute harus berupa gambar.',
    'mimes' => 'File :attribute harus dengan format: :values.',

    // password validation
    'password' => [
        'letters' => 'Kolom :attribute harus mengandung minimal satu huruf.',
        'mixed' => 'Kolom :attribute harus mengandung huruf besar dan kecil.',
        'numbers' => 'Kolom :attribute harus mengandung minimal satu angka.',
        'symbols' => 'Kolom :attribute harus mengandung minimal satu simbol.',
        'uncompromised' => 'Bidang :attribute pernah bocor di internet. Gunakan yang lain.',
    ],

    // custom attribute
    'attributes' => [
        'permission_type' => 'jenis izin',
        'file_path' => 'bukti',
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
