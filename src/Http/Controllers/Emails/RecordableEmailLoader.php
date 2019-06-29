<?php

namespace Larangular\EmailRecord\Http\Controllers\Emails;

trait RecordableEmailLoader {

    public function getRecordableEmail(string $type, int $contentId): RecordableEmail {
        return new $type($contentId);
    }

}
