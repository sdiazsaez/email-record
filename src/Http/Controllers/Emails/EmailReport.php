<?php


namespace Larangular\EmailRecord\Http\Controllers\Emails;

use Illuminate\Mail\Mailable;

class EmailReport {

    public function report($failure) {
        $reportEmail = config('email-record.failure-report');
        if (!empty($reportEmail)) {
            (new Mailable())->to($reportEmail)
                            ->subject('Error notification')
                            ->text(json_encode($failure))
                            ->send();
        }
    }

}
