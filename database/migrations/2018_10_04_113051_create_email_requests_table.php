<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Larangular\MigrationPackage\Migration\Schematics;

class CreateEmailRequestsTable extends Migration {
    use Schematics;
    protected $name = 'email_requests';

    public function __construct() {
        $this->connection = config('email-record.connection');

    }

    public function up() {
        $this->create(function (Blueprint $table) {
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

    public function down() {
        $this->drop();
    }
}

