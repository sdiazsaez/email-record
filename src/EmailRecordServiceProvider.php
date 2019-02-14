<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 3/24/18
 * Time: 21:41
 */

namespace Larangular\EmailRecord;

use Illuminate\Support\ServiceProvider;
use Larangular\EmailRecord\Commands\SendCommand;
use EmailTypesBuilder;

class EmailRecordServiceProvider extends ServiceProvider {

    public function boot() {
        $this->loadRoutesFrom(__DIR__ . '/Http/Routes/EmailRecordRoutes.php');
        $this->publishes([
                             __DIR__ . '/../config/email-record.php' => config_path('email-record.php'),
                         ]);

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    public function register() {
        $this->commands(SendCommand::class);

        $this->app->bind('EmailRecordTypeBuilder', function($app) {
            return new EmailTypesBuilder();
        });
    }
}
