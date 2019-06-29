<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Larangular\Installable\Facades\InstallableConfig;
use Larangular\MigrationPackage\Migration\Schematics;

class CreateSentEmailsTable extends Migration {
    use Schematics;

    public function __construct() {
        $installableConfig = InstallableConfig::config('Larangular\EmailRecord\EmailRecordServiceProvider');
        $this->connection = $installableConfig->getConnection('sent_emails');
        $this->name = $installableConfig->getName('sent_emails');

    }

    public function up(): void {
        $this->create(static function (Blueprint $table) {
            $table->increments('id');
            $table->longText('to');
            $table->longText('from')
                  ->nullable();
            $table->longText('bbc')
                  ->nullable();
            $table->longText('content')
                  ->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void {
        $this->drop();
    }
}

