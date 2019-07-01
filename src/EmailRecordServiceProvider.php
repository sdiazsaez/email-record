<?php

namespace Larangular\EmailRecord;

use EmailTypesBuilder;
use Larangular\EmailRecord\Commands\SendCommand;
use Larangular\Installable\{Contracts\HasInstallable, Contracts\Installable, Installer\Installer};
use Larangular\Installable\Support\{InstallableServiceProvider as ServiceProvider, PublisableGroups};

class EmailRecordServiceProvider extends ServiceProvider implements HasInstallable {

    public function boot() {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->publishes([
            __DIR__ . '/../config/email-record.php' => config_path('email-record.php'),
        ]);

        $this->declareMigrationGlobal();
        $this->declareMigrationSentEmails();
        $this->declareMigrationEmailRequests();
        $this->declareMigrationEmailFailures();
    }

    public function register() {
        $this->commands(SendCommand::class);
        $this->app->bind('EmailRecordTypeBuilder', static function () {
            return new EmailTypesBuilder();
        });
    }

    public function installer(): Installable {
        return new Installer(__CLASS__);
    }

    private function declareMigrationGlobal(): void {
        $this->declareMigration([
            'connection'   => 'mysql',
            'migrations'   => [
                'local_path' => base_path() . '/vendor/larangular/email-record/database/migrations',
            ],
            'seeds'        => [
                'local_path' => __DIR__ . '/../database/seeds',
            ],
            'seed_classes' => [],
        ]);
    }

    private function declareMigrationSentEmails() {
        $this->declareMigration([
            'name'      => 'sent_emails',
            'timestamp' => true,
        ]);
    }

    private function declareMigrationEmailRequests() {
        $this->declareMigration([
            'name'      => 'email_requests',
            'timestamp' => true,
        ]);
    }

    private function declareMigrationEmailFailures() {
        $this->declareMigration([
            'name'      => 'email_failures',
            'timestamp' => true,
        ]);
    }

}
