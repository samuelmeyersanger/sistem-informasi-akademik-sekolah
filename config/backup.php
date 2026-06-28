<?php

return [

    'backup' => [
        /*
         * Nama aplikasi yang digunakan sebagai nama folder penyimpanan backup.
         * Kita ganti teks statis agar sinkron dengan BackupController.
         */
        'name' => 'backup-sekolah-pgsql',

        'source' => [
            'files' => [
                /*
                 * 🟢 SUDAH DIUBAH: Dikosongkan [] agar file kodingan tidak ikut dibackup.
                 * Ini membuat proses download/upload menjadi super cepat (hitungan detik).
                 */
                'include' => [],

                'exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                ],

                'follow_links' => false,

                'ignore_unreadable_directories' => false,

                'relative_path' => null,
            ],

            /*
             * 🟢 SUDAH DIUBAH: Memaksa sistem untuk selalu menggunakan koneksi 'pgsql'.
             */
            'databases' => [
                'pgsql',
            ],
        ],

        'database_dump_compressor' => null,

        'database_dump_file_timestamp_format' => null,

        'database_dump_filename_base' => 'database',

        /*
         * Menandakan file ekstensi sql di dalam zip.
         */
        'database_dump_file_extension' => 'sql',

        'destination' => [
            'compression_method' => ZipArchive::CM_DEFAULT,

            'compression_level' => 9,

            'filename_prefix' => '',

            'disks' => [
                'local',
            ],
        ],

        'temporary_directory' => storage_path('app/backup-temp-dir'),

        'password' => env('BACKUP_ARCHIVE_PASSWORD'),

        'encryption' => 'default',

        'tries' => 1,

        'retry_delay' => 0,
    ],

    'notifications' => [
        /*
         * 🟢 SUDAH DIUBAH: Semua notification class dikosongkan [] 
         * agar tidak memicu error SMTP Email saat Anda mencoba tombol backup di Sail.
         */
        'notifications' => [
            \Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification::class => [],
            \Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification::class => [],
            \Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification::class => [],
            \Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification::class => [],
            \Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification::class => [],
            \Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification::class => [],
        ],

        'notifiable' => \Spatie\Backup\Notifications\Notifiable::class,

        'mail' => [
            'to' => 'your@example.com',

            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                'name' => env('MAIL_FROM_NAME', 'Example'),
            ],
        ],

        'slack' => [
            'webhook_url' => '',
            'channel' => null,
            'username' => null,
            'icon' => null,
        ],

        'discord' => [
            'webhook_url' => '',
            'username' => '',
            'avatar_url' => '',
        ],
    ],

    'monitor_backups' => [
        [
            'name' => 'backup-sekolah-pgsql',
            'disks' => ['local'],
            'health_checks' => [
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 5000,
            ],
        ],
    ],

    'cleanup' => [
        /*
         * 🟢 SEKARANG SUDAH BENAR: Menggunakan DefaultCleanupStrategy::class 
         * yang sesuai dengan versi package laravel-backup terbaru Anda.
         */
        'strategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,

        'default_strategy' => [
            'keep_all_backups_for_days' => 7,

            'keep_daily_backups_for_days' => 16,

            'keep_weekly_backups_for_weeks' => 8,

            'keep_monthly_backups_for_months' => 4,

            'keep_yearly_backups_for_years' => 2,

            'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
        ],

        'tries' => 1,

        'retry_delay' => 0,
    ],

];