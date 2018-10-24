<?php

namespace Larangular\EmailRecord\Commands;

use Illuminate\Console\Command;
use Larangular\EmailRecord\Http\Controllers\Emails\SendEmailController;

class SendCommand extends Command {

    protected $signature = 'email-record:send';
    protected $description = 'Send every pending email request';
    private $sendEmailController;

    public function __construct() {
        parent::__construct();
        $this->sendEmailController = new SendEmailController();
    }

    public function handle() {
        $this->sendEmailController->sendEmails();
    }
}
