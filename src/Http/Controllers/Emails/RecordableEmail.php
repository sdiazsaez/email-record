<?php

namespace Larangular\EmailRecord\Http\Controllers\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

abstract class RecordableEmail extends Mailable {

    use Queueable, SerializesModels;

    abstract public function isValid(): boolean;

    abstract public function templatePath(): string;

    abstract public function content(): array;

}
