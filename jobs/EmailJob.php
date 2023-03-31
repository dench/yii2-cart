<?php

namespace dench\cart\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class EmailJob extends BaseObject implements JobInterface
{
    public $emailFrom;
    public $emailTo;
    public $subject;
    public $body;

    public function execute($queue)
    {
        try {
            Yii::$app->mailer->compose()
                ->setFrom($this->emailFrom)
                ->setTo($this->emailTo)
                ->setSubject($this->subject)
                ->setTextBody($this->body)
                ->send();
        } catch (Exception $e) {
            Yii::error('Ошибка отправки почты. ' . $this->subject);
            Yii::$app->mailer2->compose()
                ->setFrom($this->emailFrom)
                ->setTo($this->emailTo)
                ->setSubject('Ошибка отправки почты. ' . $this->subject)
                ->setTextBody('Ошибка отправки почты, сообщите разработчику. ' . $this->body)
                ->send();
        }
    }
}