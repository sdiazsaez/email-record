<?php

namespace Larangular\EmailRecord\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Larangular\Installable\Facades\InstallableConfig;
use Larangular\RoutingController\Model as RoutingModel;

class SentEmail extends Model {

    use SoftDeletes, RoutingModel;

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'to',
        'from',
        'bbc',
        'content',
    ];

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);

        $installableConfig = InstallableConfig::config('Larangular\EmailRecord\EmailRecordServiceProvider');
        $this->connection = $installableConfig->getConnection('sent_emails');
        $this->table = $installableConfig->getName('sent_emails');
    }


    public function setToAttribute($value) {
        $this->attributes['to'] = json_encode($value);
    }

    public function setFromAttribute($value) {
        $this->attributes['from'] = json_encode($value);
    }

    public function setBbcAttribute($value) {
        $this->attributes['bbc'] = json_encode($value);
    }


    public function setContentAttribute($value) {
        $this->attributes['content'] = json_encode($value);
    }

    public function getContentAttribute($value) {
        return json_decode($value);
    }

    public function emailRequest() {
        return $this->belongsTo(EmailRequest::class, 'id', 'sent_email_id');
    }

}
