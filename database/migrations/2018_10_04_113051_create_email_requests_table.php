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
            $table->integer('sent_email_id')
                  ->foreign('sent_emails_id')
                  ->references('id')
                  ->on('sent_emails')
                  ->nullable();
            $table->string('email_type');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void {
        $this->drop();
    }
}

