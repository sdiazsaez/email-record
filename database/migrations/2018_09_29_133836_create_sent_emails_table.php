<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Larangular\MigrationPackage\Migration\Schematics;

class CreateSentEmailsTable extends Migration {
    use Schematics;
    protected $name = 'sent_emails';

    public function __construct() {
        $this->connection = config('email-record.connection');

    }

    public function up() {
        $this->create(function (Blueprint $table) {
            $table->increments('id');
            $table->text('to');
            $table->string('from')
                  ->nullable();
            $table->string('bbc')
                  ->nullable();
            $table->text('content')
                  ->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down() {
        $this->drop();
    }
}

