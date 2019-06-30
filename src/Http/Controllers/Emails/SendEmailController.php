<?php

namespace Larangular\EmailRecord\Http\Controllers\Emails;

use Illuminate\Support\Facades\Mail;
use Larangular\EmailRecord\Models\EmailFailures;
use Larangular\EmailRecord\Models\EmailRequest;
use Larangular\EmailRecord\Models\SentEmail;
use Larangular\Support\Instance;

class SendEmailController {

    use RecordableEmailLoader;

    public function preview($id, $emailType = null) {
        if (!is_null($emailType)) {
            $type = $this->getTypes($emailType);
            $mailable = $this->getRecordableEmail($type['type_class'], $id);
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
            EmailFailures::create([
                'email_request_id' => $request->id,
                'failures'         => Mail::failures(),
            ]);
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

}
