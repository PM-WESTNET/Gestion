<?php
/**
 * Created by PhpStorm.
 * User: cgarcia
 * Date: 24/07/15
 * Time: 23:23
 */

namespace app\modules\mailing\components\sender;


use app\modules\mailing\MailingModule;
use app\modules\mailing\models\EmailTransport;
use app\modules\mailing\services\ConfigMailing;
use Yii;
use yii\log\Logger;
use yii\mail\MailerInterface;
use yii\swiftmailer\Mailer;
use yii\swiftmailer\Message;

class MailSender
{
    /** @var MailSender $_instance  */
    public static $_instances = [];

    /** @var EmailTransport $email_transport */
    private $email_transport = null;

    public static function getInstance($name=null, $relation_class = null, $relation_id = null, $emailTransport = null)
    {
        if($emailTransport===null) {
            $emailTransport = ConfigMailing::getConfig($name,$relation_class, $relation_id);
        }

        // Si existe el mailer lo retorno, sino cargo la configuracion y lo retorno.
        if(array_key_exists($emailTransport->name, self::$_instances)===false) {
            self::$_instances[$emailTransport->name] = new MailSender($emailTransport);

        }
        return self::$_instances[$emailTransport->name];
    }

    /**
     * MailSender constructor.
     * @param EmailTransport $emailTransport
     */
    public function __construct(EmailTransport $emailTransport)
    {
        $this->email_transport = $emailTransport;
    }

    /**
     * Prepara un mensaje para ser enviado.
     * @param $to
     * @param string $subject
     * @param array $content
     * @param array $cc
     * @param array $bcc
     * @param array $attachment
     * @return Message
     */
    public function prepareMessage($to, $subject = '', $content = [], $cc = [], $bcc = [], $attachment = [])
    {
        try {
            Yii::$app->mail->setTransport($this->email_transport->getConfigArray());

            $view = (array_key_exists('view', $content) !== false ? ($content['view']!='' ? $content['view'] : $this->email_transport->layout ) : $this->email_transport->layout );
            $layout = (array_key_exists('layout', $content) !== false ? ($content['layout']!='' ? $content['layout'] : $this->email_transport->layout ) : $this->email_transport->layout );
            $params = (array_key_exists('params', $content) ?  $content['params'] : [] );
            $mailer = Yii::$app->mail;
            $mailer->htmlLayout = $layout;
            Yii::$app->view->params = $params;

            /** @var Message $message */
            $message = $mailer
                ->compose( $view, $params )
                ->setFrom($this->email_transport->from_email)
                ->setTo((is_array($to) ? [$to['email'] => $to['name']] : $to))
                ->setSubject($subject)
            ;
            if (empty($cc)) {
                $message->setCc($cc);
            }

            if (empty($bcc)) {
                $message->setBcc($bcc);
            }
            // Los attachmentes, pueden ser con un archivo del file system o con contenido on-defly
            if(!empty($attachment)) {
                foreach($attachment as $attach) {
                    if(is_array($attach)) {
                        $message->attachContent($attach['view'], $attach['options']);
                    } else {
                        if (file_exists($attach)) {
                            Yii::debug('1');
                            $message->attach($attach);
                        }
                    }
                }
            }
            return $message;
        }catch(\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @param $to
     * @param string $subject
     * @param array $content
     * @param array $cc
     * @param array $bcc
     * @param array $attachment
     * @throws \Exception
     */
    public function send($to, $subject = "", $content = [],
                         $cc = [], $bcc = [], $attachment = [])
    {
        try {
            Yii::$app->mail->setTransport($this->email_transport->getConfigArray());

            $mailer = Yii::$app->mail;
            // Si en la configuracion tengo un template lo pongo.
            if(trim($this->email_transport->layout)!="") {
                $mailer->htmlLayout = $this->email_transport->layout;
            }

            $message = $this->prepareMessage($to, $subject, $content, $cc, $bcc, $attachment);
            if(!$message->send($mailer)) {
                throw new \Exception(MailingModule::t('The email cant be sended.'));
            }
            return true;
        }catch(\Exception $ex) {
            throw $ex;
        }
    }


    /**
     * Envia un lote mails.
     *
     * @param $messages
     * @throws \Exception
     */
    public function sendMultiple($messages)
    {
        try {
            Yii::$app->mail->setTransport($this->email_transport->getConfigArray());

            /** @var Mailer $mailer */
            $mailer = Yii::$app->mail;

            return $mailer->sendMultiple($messages);
        } catch(\Exception $ex){
            throw $ex;
        }
    }


    /**
     * Prepara un mensaje para ser enviado.
     * @param $to
     * @param string $subject
     * @param array $content
     * @param array $cc
     * @param array $bcc
     * @param array $attachment
     * @return Message
     */
    public function prepareMessageAndSend($to, $subject = '', $content = [], $cc = [], $bcc = [], $attachment = [])
    {
        try {
            Yii::$app->mail->setTransport($this->email_transport->getConfigArray());

            $view = (array_key_exists('view', $content) !== false ? ($content['view']!='' ? $content['view'] : $this->email_transport->layout ) : $this->email_transport->layout );
            $layout = (array_key_exists('layout', $content) !== false ? ($content['layout']!='' ? $content['layout'] : $this->email_transport->layout ) : $this->email_transport->layout );
            $params = (array_key_exists('params', $content) ?  $content['params'] : [] );
            $mailer = Yii::$app->mail;
            $mailer->htmlLayout = $layout;
            Yii::$app->view->params = $params;

            /** @var Message $message */
            $message = $mailer
                ->compose( $view, $params )
                ->setFrom($this->email_transport->from_email)
                ->setTo((is_array($to) ? [$to['email'] => $to['name']] : $to))
                ->setSubject($subject)
            ;
            if (empty($cc)) {
                $message->setCc($cc);
            }

            if (empty($bcc)) {
                $message->setBcc($bcc);
            }
            // Los attachmentes, pueden ser con un archivo del file system o con contenido on-defly
            if(!empty($attachment)) {
                foreach($attachment as $attach) {
                    if(is_array($attach)) {
                        $message->attachContent($attach['view'], $attach['options']);
                    } else {
                        if (file_exists($attach)) {
                            Yii::debug('1');
                            $message->attach($attach);
                        }
                    }
                }
            }
            return $message->send();
        }catch(\Exception $ex) {
            throw $ex;
        }
    }
}