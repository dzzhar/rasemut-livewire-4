<?php

return [
    // validation messages
    'required' => 'Kolom :attribute wajib diisi.',
    'required_without' => 'Kolom :attribute wajib diisi jika :values tidak diisi.',
    'string' => 'Kolom :attribute harus berupa teks.',
    'min' => [
        'string' => 'Kolom :attribute minimal :min karakter.',
        'numeric' => 'Kolom :attribute minimal :min.',
    ],
    'max' => [
        'file' => 'Ukuran file :attribute maksimal :max KB.',
    ],
    'after_or_equal' => 'Kolom :attribute harus berupa tanggal hari ini atau setelahnya.',
    'after' => 'Kolom :attribute harus setelah tanggal mulai.',
    'email' => 'Kolom :attribute harus berupa alamat email yang valid.',
    'unique' => 'Kolom :attribute sudah digunakan.',
    'confirmed' => 'Konfirmasi :attribute tidak sesuai.',
    'different' => 'Kolom :attribute harus berbeda dengan :other.',
    'image' => 'File :attribute harus berupa gambar.',
    'mimes' => 'File :attribute harus berformat: :values.',

    // password validation
    'password' => [
        'letters' => 'Kolom :attribute harus mengandung minimal satu huruf.',
        'mixed' => 'Kolom :attribute harus mengandung huruf besar dan huruf kecil.',
        'numbers' => 'Kolom :attribute harus mengandung minimal satu angka.',
        'symbols' => 'Kolom :attribute harus mengandung minimal satu simbol.',
        'uncompromised' => 'Kolom :attribute pernah ditemukan dalam kebocoran data. Gunakan kata sandi lain.',
    ],

    // attribute names
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
    ],
];
