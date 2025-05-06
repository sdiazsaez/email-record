<?php
namespace Larangular\EmailRecord\Commands;

use Illuminate\Console\Command;
use Larangular\EmailRecord\Http\Controllers\Emails\SendEmailController;

class SendCommand extends Command {

    // Add an optional {id?} argument
    protected $signature = 'email-record:send {id?}';
    protected $description = 'Send every pending email request or a specific one by ID';

    private $sendEmailController;

    public function __construct() {
        parent::__construct();
        $this->sendEmailController = new SendEmailController();
    }

    public function handle() {
        $id = $this->argument('id');

        if ($id) {
            $this->sendEmailController->sendEmailById($id);
        } else {
            $this->sendEmailController->sendEmails();
        }
    }
}
