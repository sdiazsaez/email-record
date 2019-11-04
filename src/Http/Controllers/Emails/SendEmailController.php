<?php

namespace Larangular\EmailRecord\Http\Controllers\Emails;

use Illuminate\Support\Facades\Mail;
use Larangular\EmailRecord\Models\EmailFailures;
use Larangular\EmailRecord\Models\EmailRequest;
use Larangular\EmailRecord\Models\SentEmail;
use Larangular\Support\Instance;

class SendEmailController {

    use RecordableEmailLoader;

    private $defaultBCC;

    public function __construct() {
        $this->defaultBCC = config('email-record.mail_default_bcc', false);
    }

    public function preview($id, $emailType = null) {
        if (!is_null($emailType)) {
            $type = $this->getTypes($emailType);
            $mailable = $this->getRecordableEmail($type['type_class'], $id);
            if (Instance::instanceOf($mailable, RecordableEmail::class)) {
                return view($mailable->templatePath(), $mailable->content());
            }
        }

    }

    public function sendEmails(): void {
        $emailRequests = EmailRequest::notSent()
                                     ->get();

        foreach ($emailRequests as $emailRequest) {
            $this->send($emailRequest);
        }
    }

    private function send(EmailRequest $request): void {
        $mailable = $this->getRecordableEmail($request->email_type_class, $request->content_id);

        if ($this->defaultBCC !== false) {
            array_push($mailable->bcc, $this->defaultBCC);
        }

        Mail::send($mailable);

        if (count(Mail::failures()) <= 0) {
            $this->emailRequestSentUpdate($request, $mailable);
        } else {
            EmailFailures::create([
                'email_request_id' => $request->id,
                'failures'         => Mail::failures(),
            ]);
        }
    }

    private function emailRequestSentUpdate(EmailRequest $request, RecordableEmail $recordableEmail) {
        $request->content = $recordableEmail->content();
        $request->sent();
    }

}
