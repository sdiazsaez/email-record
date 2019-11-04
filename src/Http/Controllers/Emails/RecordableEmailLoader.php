<?php

namespace Larangular\EmailRecord\Http\Controllers\Emails;

use Larangular\EmailRecord\Models\EmailRequest;

trait RecordableEmailLoader {

    public function getRecodableEmailWithRequest(EmailRequest $request, string $emailType = null): RecordableEmail {
        if (is_null($emailType)) {
            $emailType = $request->email_type;
        }

        $type = $this->getTypes($emailType);
        return $this->getRecordableEmail($type['type_class'], $request->content_id);
    }

    public function getRecordableEmail(string $type, int $contentId): RecordableEmail {
        return new $type($contentId);
    }

    public function getTypes(int $id = null) {
        $types = config('email-record.email_types');
        if (isset($id)) {
            $filter = array_filter($types, static function ($type) use ($id) {
                return $type['id'] === $id;
            });
            $filter = array_values($filter);
            if (count($filter) > 0) {
                $types = $filter[0];
            }
        }

        return $types;
    }

}
