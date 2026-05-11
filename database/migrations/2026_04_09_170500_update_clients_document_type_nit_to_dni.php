<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE clients MODIFY COLUMN document_type ENUM('CI','NIT','DNI','Pasaporte') NOT NULL DEFAULT 'CI'");
        DB::table('clients')->where('document_type', 'NIT')->update(['document_type' => 'DNI']);
        DB::statement("ALTER TABLE clients MODIFY COLUMN document_type ENUM('CI','DNI','Pasaporte') NOT NULL DEFAULT 'CI'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE clients MODIFY COLUMN document_type ENUM('CI','NIT','DNI','Pasaporte') NOT NULL DEFAULT 'CI'");
        DB::table('clients')->where('document_type', 'DNI')->update(['document_type' => 'NIT']);
        DB::statement("ALTER TABLE clients MODIFY COLUMN document_type ENUM('CI','NIT','Pasaporte') NOT NULL DEFAULT 'CI'");
    }
};
