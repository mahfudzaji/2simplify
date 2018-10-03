<?php

return [
    'id' => [
        'activityReport' => [
            'title' => 'Laporan aktifitas',
            'accessRight' => [
                'create' => 'Anda tidak memiliki hak untuk membuat laporan aktivitas',
                'view' => 'Anda tidak memiliki hak untuk melihat laporan aktivitas',
                'update' => 'Anda tidak memiliki hak untuk memperbaharui laporan aktivitas',
                'delete' => 'Anda tidak memiliki hak untuk menghapus laporan aktivitas',
                'print' => 'Anda tidak memiliki hak untuk mencetak laporan aktivitas'
            ],
            'createSuccess' => 'Laporan aktifitas berhasil disimpan',
            'createFail' => 'Laporan aktifitas gagal disimpan. Mohon cek dan ulangi kembali' 
        ],

        'receiveForm' => [
            'title' => 'Tanda terima',
            'accessRight' => [
                'create' => 'Anda tidak memiliki hak untuk membuat tanda terima',
                'view' => 'Anda tidak memiliki hak untuk melihat tanda terima',
                'update' => 'Anda tidak memiliki hak untuk memperbaharui tanda terima',
                'delete' => 'Anda tidak memiliki hak untuk menghapus tanda terima',
                'print' => 'Anda tidak memiliki hak untuk mencetak tanda terima'
            ],
            'createSuccess' => 'Data tanda terima berhasil disimpan',
            'createFail' => 'Data tanda terima gagal disimpan. Mohon cek dan ulangi kembali', 
            'createDataNotValid' => 'Pendaftaran data tanda terima gagal. Produk, serial number dan kondisi harus diisi dengan benar',
            
        ],

        'vacationForm' => [
            'title' => 'Cuti',
            'accessRight' => [
                'create' => 'Anda tidak memiliki hak untuk membuat data cuti',
                'view' => 'Anda tidak memiliki hak untuk melihat data cuti',
                'update' => 'Anda tidak memiliki hak untuk memperbaharui data cuti',
                'delete' => 'Anda tidak memiliki hak untuk menghapus data cuti',
                'print' => 'Anda tidak memiliki hak untuk mencetak data cuti',
                'approval' => 'Anda tidak memiliki hak untuk menyetujui ataupun menolak permohonan cuti',
                'specificApproval' => 'Anda tidak memiliki hak untuk menyetujui ataupun menolak permohonan cuti ini'
            ],
            'createSuccess' => 'Data cuti berhasil disimpan',
            'createFail' => 'Data cuti gagal disimpan. Mohon cek dan ulangi kembali', 
            'createDataNotValid' => 'Pendaftaran data cuti gagal. Mohon data diisi dengan benar',
            'updateSuccess' => 'Pembaharuan data cuti berhasil disimpan',
            'updateFail' => 'Pembaharuan data cuti gagal disimpan. Mohon ulangi lagi.'
            
        ],

        'reimburseForm' => [
            'title' => 'Reimburse',
            'accessRight' => [
                'create' => 'Anda tidak memiliki hak untuk membuat data reimburse',
                'view' => 'Anda tidak memiliki hak untuk melihat data reimburse',
                'update' => 'Anda tidak memiliki hak untuk memperbaharui data reimburse',
                'delete' => 'Anda tidak memiliki hak untuk menghapus data reimburse',
                'print' => 'Anda tidak memiliki hak untuk mencetak data reimburse',
                'approval' => 'Anda tidak memiliki hak untuk menyetujui ataupun menolak permohonan reimburse',
                'specificApproval' => 'Anda tidak memiliki hak untuk menyetujui ataupun menolak permohonan reimburse ini'
            ],
            'createSuccess' => 'Data reimburse berhasil disimpan',
            'createFail' => 'Data reimburse gagal disimpan. Mohon cek dan ulangi kembali', 
            'createDataNotValid' => 'Pendaftaran data reimburse gagal. Mohon data diisi dengan benar',
            'updateSuccess' => 'Pembaharuan data reimburse berhasil disimpan',
            'updateFail' => 'Pembaharuan data reimburse gagal disimpan. Mohon ulangi lagi.',
            'deleteSuccess' => 'Hapus data item reimburse berhasil.'
        ],

        'receiptForm' => [
            'title' => 'Receipt',
            'accessRight' => [
                'create' => 'Anda tidak memiliki hak untuk membuat Receipt',
                'view' => 'Anda tidak memiliki hak untuk melihat Receipt',
                'update' => 'Anda tidak memiliki hak untuk memperbaharui Receipt',
                'delete' => 'Anda tidak memiliki hak untuk menghapus Receipt',
                'print' => 'Anda tidak memiliki hak untuk mencetak Receipt',
                'approval' => 'Anda tidak memiliki hak untuk menyetujui ataupun menolak rancangan Receipt'
            ],
            'createSuccess' => 'Data Receipt berhasil disimpan',
            'createFail' => 'Data Receipt gagal disimpan. Mohon cek dan ulangi kembali', 
            'createDataNotValid' => 'Pendaftaran data Receipt gagal.',
            'updateSuccess' => 'Memperbaharui data Receipt berhasil.',
            'updateFail' => 'Maaf, memperbaharui data Receiptgagal.',
            'deleteSuccess' => 'Menghapus data item Receipt berhasil.',
            'deleteFail' => 'Maaf, menghapus data item Receipt gagal. Mohon ulangi atau hubungi admin.'
        ],

        'poForm' => [
            'title' => 'Purchase order',
            'accessRight' => [
                'create' => 'Anda tidak memiliki hak untuk membuat purchase order',
                'view' => 'Anda tidak memiliki hak untuk melihat purchase order',
                'update' => 'Anda tidak memiliki hak untuk memperbaharui purchase order',
                'delete' => 'Anda tidak memiliki hak untuk menghapus purchase order',
                'print' => 'Anda tidak memiliki hak untuk mencetak purchase order',
                'approval' => 'Anda tidak memiliki hak untuk menyetujui ataupun menolak rancangan purchase order',
            ],
            'createSuccess' => 'Data purchase order berhasil disimpan',
            'createFail' => 'Data purchase order gagal disimpan. Mohon cek dan ulangi kembali', 
            'createDataNotValid' => 'Pendaftaran data purchase order gagal.',
            'updateSuccess' => 'Memperbaharui data purchase order berhasil.',
            'updateFail' => 'Maaf, memperbaharui data purchase order gagal.',
            'deleteSuccess' => 'Menghapus data item PO berhasil.',
            'deleteFail' => 'Maaf, menghapus data item PO gagal. Mohon ulangi atau hubungi admin.'
        ],

        'doForm' => [
            'title' => 'Delivery order',
            'accessRight' => [
                'create' => 'Anda tidak memiliki hak untuk membuat delivery order',
                'view' => 'Anda tidak memiliki hak untuk melihat delivery order',
                'update' => 'Anda tidak memiliki hak untuk memperbaharui delivery order',
                'delete' => 'Anda tidak memiliki hak untuk menghapus delivery order',
                'print' => 'Anda tidak memiliki hak untuk mencetak delivery order',
                'approval' => 'Anda tidak memiliki hak untuk menyetujui ataupun menolak rancangan delivery order'
            ],
            'createSuccess' => 'Data delivery order berhasil disimpan',
            'createFail' => 'Data delivery order gagal disimpan. Mohon cek dan ulangi kembali', 
            'createDataNotValid' => 'Pendaftaran data delivery order gagal.',
            'updateSuccess' => 'Memperbaharui data delivery order berhasil.',
            'updateFail' => 'Maaf, memperbaharui data delivery order gagal.',
            'deleteSuccess' => 'Menghapus data item DO berhasil.',
            'deleteFail' => 'Maaf, menghapus data item DO gagal. Mohon ulangi atau hubungi admin.'
        ],

        'quoForm' => [
            'title' => 'Quotation',
            'accessRight' => [
                'create' => 'Anda tidak memiliki hak untuk membuat Quotation',
                'view' => 'Anda tidak memiliki hak untuk melihat Quotation',
                'update' => 'Anda tidak memiliki hak untuk memperbaharui Quotation',
                'delete' => 'Anda tidak memiliki hak untuk menghapus Quotation',
                'print' => 'Anda tidak memiliki hak untuk mencetak Quotation',
                'approval' => 'Anda tidak memiliki hak untuk menyetujui atau tidak menyetujui data Quotation'
            ],
            'createSuccess' => 'Data Quotation  berhasil disimpan',
            'createFail' => 'Data Quotation  gagal disimpan. Mohon cek dan ulangi kembali', 
            'createDataNotValid' => 'Pendaftaran data Quotation  gagal.',
            'updateSuccess' => 'Memperbaharui data Quotation  berhasil.',
            'updateFail' => 'Maaf, memperbaharui data  Quotation gagal.',
            'deleteSuccess' => 'Menghapus data item Quotation berhasil.',
            'deleteFail' => 'Maaf, menghapus data item Quotation gagal. Mohon ulangi atau hubungi admin.'
        ],

        'stock' => [
            'title' => 'Stock',
            'accessRight' => [
                'create' => 'Anda tidak memiliki hak untuk membuat stock',
                'view' => 'Anda tidak memiliki hak untuk melihat stock',
                'update' => 'Anda tidak memiliki hak untuk memperbaharui stock',
                'delete' => 'Anda tidak memiliki hak untuk menghapus stock',
                'print' => 'Anda tidak memiliki hak untuk mencetak stock',
                'approval' => 'Anda tidak memiliki hak untuk menyetujui atau tidak menyetujui data stock'
            ],
            'createSuccess' => 'Data stock  berhasil disimpan',
            'createFail' => 'Data stock  gagal disimpan. Mohon cek dan ulangi kembali', 
            'createDataNotValid' => 'Pendaftaran data stock  gagal.',
            'updateSuccess' => 'Memperbaharui data stock  berhasil.',
            'updateFail' => 'Maaf, memperbaharui data  stock gagal.',
            'deleteSuccess' => 'Menghapus data item stock berhasil.',
            'deleteFail' => 'Maaf, menghapus data item stock gagal. Mohon ulangi atau hubungi admin.'
        ],

        'project' => [
            'title' => 'Project',
            'accessRight' => [
                'create' => 'Anda tidak memiliki hak untuk membuat Project',
                'view' => 'Anda tidak memiliki hak untuk melihat Project',
                'update' => 'Anda tidak memiliki hak untuk memperbaharui Project',
                'delete' => 'Anda tidak memiliki hak untuk menghapus Project',
                'print' => 'Anda tidak memiliki hak untuk mencetak Project',
                'approval' => 'Anda tidak memiliki hak untuk menyetujui atau tidak menyetujui data Project'
            ],
            'createSuccess' => 'Data Project  berhasil disimpan',
            'createFail' => 'Data Project  gagal disimpan. Mohon cek dan ulangi kembali', 
            'createDataNotValid' => 'Pendaftaran data Project  gagal.',
            'updateSuccess' => 'Memperbaharui data Project  berhasil.',
            'updateFail' => 'Maaf, memperbaharui data  Project gagal.',
            'deleteSuccess' => 'Menghapus data item Project berhasil.',
            'deleteFail' => 'Maaf, menghapus data item Project gagal. Mohon ulangi atau hubungi admin.'
        ],

        'formNotPassingRequirements' => 'Mohon isi form dengan benar dan sesuai ketentuan',
        
        'databaseOperationFailed' => 'Maaf, terjadi kesalahan pada operasi database, mohon ulangi lagi atau hubungi administrator.',

        'pagination' => [
            'unknown' => 'Halaman yang anda tuju tidak diketahui'
        ],

        'dashboard' => [
            'title' => 'Dashboard',
            'accessRight' => [
                'view' => 'Anda tidak memiliki hak untuk melihat dashboard'
            ],
            'unknown' => 'Halaman yang anda cari tidak tersedia'
        ]
    ],

    'en' =>[

    ]
]

?>
