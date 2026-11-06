<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeletedByColumn extends Migration
{
    public function up()
    {
        $this->forge->addColumn('reports', [
            'deleted_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'deleted_at'
            ],
            'CONSTRAINT `reports_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE'
        ]);
    }

    public function down()
    {
        // Remove foreign key first
        $this->forge->dropForeignKey('reports', 'reports_deleted_by_foreign');
        
        // Then remove the column
        $this->forge->dropColumn('reports', 'deleted_by');
    }
}