<?php
/*
|--------------------------------------------------------------------------
| Konfigurasi Paydisini
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendefinisikan semua opsi konfigurasi untuk integrasi
| Paydisini Anda. Ini termasuk API key dan endpoint API yang akan digunakan
| oleh aplikasi Anda untuk berkomunikasi dengan payment gateway Paydisini.
| Anda bisa mendapatkan API key Anda dari dashboard Paydisini.
|
*/

return [
    /*
    |--------------------------------------------------------------------------
    | API Key Paydisini
    |--------------------------------------------------------------------------
    |
    | Nilai ini adalah API key yang disediakan oleh Paydisini. Kunci ini
    | digunakan untuk mengautentikasi aplikasi Anda dengan payment gateway
    | Paydisini. Pastikan untuk menjaga keamanan kunci ini dan jangan pernah
    | membagikannya secara publik. Anda dapat mengaturnya di file ".env" 
    | menggunakan kunci "PAYDISINI_API_KEY".
    |
    */

    'api_key' => env('PAYDISINI_API_KEY', null),

    /*
    |--------------------------------------------------------------------------
    | Endpoint API Paydisini
    |--------------------------------------------------------------------------
    |
    | Ini adalah URL dasar dari API Paydisini. Anda dapat memodifikasinya jika
    | Anda perlu menggunakan versi API yang berbeda atau jika Paydisini 
    | menyediakan endpoint khusus untuk aplikasi Anda. Biasanya, Anda tidak 
    | perlu mengubah nilai ini.
    |
    */

    'api_endpoint' => 'https://api.paydisini.co.id/v1',
];
