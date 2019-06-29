<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Larangular\Installable\Facades\InstallableConfig;
use Larangular\MigrationPackage\Migration\Schematics;

class CreateEmailFailuresTable extends Migration {
    use Schematics;

    private $installableConfig;

    public function __construct() {
        $this->installableConfig = InstallableConfig::config('Larangular\EmailRecord\EmailRecordServiceProvider');
        $this->connection = $this->installableConfig->getConnection('email_failures');
        $this->name = $this->installableConfig->getName('email_failures');
    }

    public function up(): void {
        $emailRequestTableName = $this->installableConfig->getName('email_requests');
        $this->create(static function (Blueprint $table) use ($emailRequestTableName) {
            $table->increments('id');
            $table->integer('email_request_id')
                  ->foreign($emailRequestTableName . '_id')
                  ->references('id')
                  ->on($emailRequestTableName);
            $table->longText('failures');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void {
        $this->drop();
    }
}

