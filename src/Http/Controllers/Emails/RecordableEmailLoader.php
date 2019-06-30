<?php

namespace Larangular\EmailRecord\Http\Controllers\Emails;

trait RecordableEmailLoader {

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
