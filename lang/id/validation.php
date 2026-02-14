<?php

return [
    // custom validation message
    'required' => ':attribute wajib diisi.',
    'min' => ['string' => ':attribute minimal :min karakter.'],
    'after_or_equal' => ':attribute harus setelah atau sama dengan hari ini.',
    'after' => ':attribute harus setelah tanggal mulai.',

    // custom attribute
    'attributes' => [
        'permission_type' => 'Jenis izin',
        'start_date' => 'Tanggal mulai',
        'end_date' => 'Tanggal selesai',
        'description' => 'Keterangan',
    ]
];
