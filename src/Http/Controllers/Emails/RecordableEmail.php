<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 10/22/18
 * Time: 16:26
 */

namespace Larangular\EmailRecord\Http\Controllers\Emails;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

abstract class RecordableEmail extends Mailable {

    use Queueable, SerializesModels;

    abstract public function templatePath(): string;

    abstract public function content(): array;

}
