<?php

namespace Larangular\EmailRecord\Http\Controllers\Emails;

use Larangular\EmailRecord\Models\EmailRequest;
use Larangular\Support\Instance;

class EmailPreview {

    use RecordableEmailLoader;

    public function preview(int $id, $emailType = null) {
        $mailable = $this->getMailable($id, $emailType);
        if (isset($mailable) && Instance::instanceOf($mailable, RecordableEmail::class)) {
            return view($mailable->templatePath(), $mailable->content());
        }

        return $this->emailPreviewFailure();
    }

    private function getMailable(int $id, $emailType = null): RecordableEmail {
        $contentId = $id;
        if (is_null($emailType)) {
            $emailRequest = EmailRequest::find($id);
            $contentId = $emailRequest->content_id;
            $emailType = $emailRequest->email_type_class;
        }

        return $this->getRecordableEmail(config('email-record.email_types.' . $emailType . '.type_class'), $contentId);
    }

    private function emailPreviewFailure() {
        return 'email preview error';
    }

}
