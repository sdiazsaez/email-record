<?php

namespace Larangular\EmailRecord\Http\Controllers\Emails;

use Illuminate\Support\Facades\Mail;
use Larangular\EmailRecord\Facades\EmailReport;
use Larangular\EmailRecord\Models\EmailFailures;
use Larangular\EmailRecord\Models\EmailRequest;
use Larangular\EmailRecord\Models\SentEmail;
use Larangular\Support\Instance;

class SendEmailController {

    use RecordableEmailLoader;

    private $defaultBCC;

    public function __construct() {
        $this->defaultBCC = config('email-record.mail_bcc_default', false);
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
            try {
                $this->send($emailRequest);
            } catch (\Exception $exception) {
                $this->reportEmailError($emailRequest->id, ['error' => 'invalid email']);
            }
        }
    }

    private function send(EmailRequest $request): void {
        $mailable = $this->getRecordableEmail($request->email_type_class, $request->content_id);

        if (!$mailable->isValid()) {
            $this->reportEmailError($request->id, ['error' => 'invalid email']);
            return;
        }

        if ($this->defaultBCC !== false) {
            $mailable->bcc($this->defaultBCC);
        }

        $this->setEmailTypeDefaultBcc($mailable, $request);

        $this->emailSetView($mailable, $request);
        Mail::send($mailable);

        if (count(Mail::failures()) <= 0) {
            $this->emailRequestSentUpdate($request, $mailable);
        } else {
            $this->reportEmailError($request->id, Mail::failures());
        }
    }

    private function reportEmailError(int $requestId, $failures, bool $reportToDev = false): void {
        $data = [
            'email_request_id' => $requestId,
            'failures'         => $failures,
        ];
        EmailFailures::create($data);

        if ($reportToDev) {
            EmailReport::report($data);
        }
    }

    private function emailRequestSentUpdate(EmailRequest $request, RecordableEmail $recordableEmail) {
        $request->content = $recordableEmail->content();
        $request->sent();
    }

    private function emailSetView(RecordableEmail &$recordableEmail, EmailRequest $emailRequest): RecordableEmail {
        $recordableEmail->view($recordableEmail->templatePath(), $emailRequest->content);
        return $recordableEmail;
    }

    private function setEmailTypeDefaultBcc(RecordableEmail &$mailable, EmailRequest $emailRequest): RecordableEmail {
        $type = $this->getTypes($emailRequest->email_type);
        if (array_key_exists('bcc', $type)) {
            $bcc = explode(',', $type['bcc']);
            $mailable->bcc($bcc);
        }

        return $mailable;
    }
}
