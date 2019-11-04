<?php

namespace Larangular\EmailRecord\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Larangular\EmailRecord\Http\Controllers\Emails\RecordableEmailLoader;
use Larangular\Installable\Facades\InstallableConfig;
use Larangular\RoutingController\CachableModel as RoutingModel;

class EmailRequest extends Model {

    use SoftDeletes, RoutingModel, RecordableEmailLoader;
    protected $dates    = ['deleted_at'];
    protected $fillable = [
        'content_id',
        'email_type',
        'to',
        'from',
        'bcc',
        'content',
        'sent_at',
    ];
    protected $with     = [
        'emailFailures',
    ];
    protected $appends  = [
        'email_type_class',
        'email_type_name',
    ];

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $installableConfig = InstallableConfig::config('Larangular\EmailRecord\EmailRecordServiceProvider');
        $this->connection = $installableConfig->getConnection('email_requests');
        $this->table = $installableConfig->getName('email_requests');
    }

    public function emailFailures() {
        return $this->hasMany(EmailFailures::class, 'email_request_id', 'id');
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

    public function getToAttribute($value) {
        return json_decode($value);
    }

    public function getFromAttribute($value) {
        return json_decode($value);
    }

    public function getBbcAttribute($value) {
        return json_decode($value);
    }

    public function getEmailTypeClassAttribute() {
        $type = $this->getTypes($this->attributes['email_type']);
        return $type['type_class'];
    }

    public function getEmailTypeNameAttribute() {
        $type = $this->getTypes($this->attributes['email_type']);
        return $type['type_name'];
    }

    public function scopeNotSent($query) {
        return $query->where('sent_at', null);
    }

    public function sent() {
        $this->sent_at = $this->freshTimestampString();
        return $this->save();
    }

}
