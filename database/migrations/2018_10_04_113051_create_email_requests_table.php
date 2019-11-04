<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Larangular\Installable\Facades\InstallableConfig;
use Larangular\MigrationPackage\Migration\Schematics;

class CreateEmailRequestsTable extends Migration {
    use Schematics;

    public function __construct() {
        $installableConfig = InstallableConfig::config('Larangular\EmailRecord\EmailRecordServiceProvider');
        $this->connection = $installableConfig->getConnection('email_requests');
        $this->name = $installableConfig->getName('email_requests');
    }

    public function up(): void {
        $this->create(static function (Blueprint $table) {
            $table->increments('id');
            $table->integer('content_id');
            $table->string('email_type');
            $table->longText('to');
            $table->longText('from')
                  ->nullable();
            $table->longText('bcc')
                  ->nullable();
            $table->longText('content')
                  ->nullable();
            $table->timestamp('sent_at')
                  ->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void {
        $this->drop();
    }
}

