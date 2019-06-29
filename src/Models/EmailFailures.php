<?php

namespace Larangular\EmailRecord\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Larangular\Installable\Facades\InstallableConfig;
use Larangular\RoutingController\CachableModel as RoutingModel;

class EmailFailures extends Model {

    use SoftDeletes, RoutingModel;
    protected $fillable = [
        'email_request_id',
        'failures',
    ];

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $installableConfig = InstallableConfig::config('Larangular\EmailRecord\EmailRecordServiceProvider');
        $this->connection = $installableConfig->getConnection('email_failures');
        $this->table = $installableConfig->getName('email_failures');
    }

    public function emailRequest() {
        return $this->belongsTo(EmailRequest::class, 'id', 'email_request_id');
    }

    public function setFailuresAttribute($value) {
        $this->attributes['failures'] = json_encode($value);
    }

    public function getFailuresAttribute($value) {
        return json_decode($value);
    }

}
