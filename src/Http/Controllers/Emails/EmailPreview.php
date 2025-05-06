<?php

namespace Larangular\EmailRecord\Http\Controllers\Emails;

use Larangular\EmailRecord\Models\EmailRequest;
use Larangular\Support\Instance;

class EmailPreview {

    use RecordableEmailLoader;

    private $emailRequest;
    private $mailable;

    public function preview(int $id, $emailType = null) {
        $this->mailable = $this->getMailable($id, $emailType);
        if (isset($this->mailable) && Instance::instanceOf($this->mailable, RecordableEmail::class)) {
            return view($this->mailable->templatePath(), $this->mailContent($id, $emailType));
        }

        return $this->emailPreviewFailure();
    }

    private function mailContent(int $id, $emailType = null): array {
        if (!is_null($emailType) || !isset($this->emailRequest)) {
            return $this->mailable->emailData();
        }

        return $this->emailRequest->content;
    }

    private function getMailable(int $id, $emailType = null): RecordableEmail {
        $contentId = $id;
        if (is_null($emailType)) {
            $this->emailRequest = EmailRequest::find($id);
            $contentId = $this->emailRequest->content_id;
            $emailType = $this->emailRequest->email_type;
        }

        $type = $this->getTypes($emailType);
        return $this->getRecordableEmail($type['type_class'], $contentId);
    }

    private function emailPreviewFailure() {
        return 'email preview error';
    }

}
