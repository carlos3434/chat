<?php

class Message extends Eloquent {

    protected $table = 'messages';
    protected $fillable = array('body', 'created_at', 'user_id', 'conversation_id');

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('User', 'user_id');
    }

    public function conversation()
    {
        return $this->belongsTo('Conversation', 'conversation_id');
    }

    public function messages_notifications() {
        return $this->hasMany('MessageNotification', 'message_id', 'id');
    }
    public function getCreateAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function setCreateAtAttribute($value)
    {
        $this->attributes['create_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $value)->toDateString();
    }
}
