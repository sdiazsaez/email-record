<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 10/22/18
 * Time: 17:37
 */

namespace Larangular\EmailRecord\Http\Controllers\Emails;

use Illuminate\Contracts\Mail\Mailable;
use Larangular\EmailRecord\Models\EmailRequest;
use \Illuminate\Support\Facades\Mail;
use Larangular\EmailRecord\Models\SentEmail;
use Larangular\Support\Instance;
use Larangular\EmailRecord\Http\Controllers\Emails\RecordableEmail;

class SendEmailController {

    public function test() {
        $request = EmailRequest::NotSent()
                               ->first();
        $this->send($request);
    }

    public function preview($id, $emailType = null) {
        if (!is_null($emailType)) {
            $mailable = $this->getRecordableEmail(config('email-record.email_types.' . $emailType . '.type_class'), $id);
            if (Instance::instanceOf($mailable, RecordableEmail::class)) {
                return view($mailable->templatePath(), $mailable->content());
            }
        }

    }

    public function sendEmails() {
        $emailRequests = EmailRequest::notSent()
                                     ->get();

        foreach ($emailRequests as $emailRequest) {
            $this->send($emailRequest);
        }
    }

    private function send(EmailRequest $request) {
        $mailable = $this->getRecordableEmail($request->email_type_class, $request->content_id);
        Mail::send($mailable);


        if (count(Mail::failures()) <= 0) {
            $record = $this->recordSentEmail($mailable);
            $request->sent_email_id = $record->id;
            $request->save();
        } else {
            mail('sdiaz.sz@gmail.com', 'error en envio de correos', 'base.misegurodirecto.cl ' . $request->id);
        }
    }

    private function recordSentEmail(RecordableEmail $recordableEmail) {
        return SentEmail::create([
                              'to'      => $recordableEmail->to,
                              'from'    => $recordableEmail->from,
                              'bbc'     => $recordableEmail->bcc,
                              'content' => $recordableEmail->content(),
                          ]);
    }

    private function getRecordableEmail(string $type, int $contentId): RecordableEmail {
        return new $type($contentId);
    }


}
