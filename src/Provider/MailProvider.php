<?php

namespace MailProvider\Provider;

/**
 * Abstract Mail Provider
 *
 * Contains all the abstract methods needed for
 * the email services to retrieve and mail 
 * the desired data.
 *
 * @author Leo Flapper <info@leoflapper.nl>
 * @version 1.1.1
 * @since 1.0.0
 */
abstract class MailProvider implements MailInterface
{

    /**
     * Contains all 'to' email addresses.
     * @var array
     */
    protected $to = [];

    /**
     * The from email address.
     * @var string
     */
    protected $from;

    /**
     * The from email address name
     * @var string
     */
    protected $fromName;

    /**
     * The reply to email address
     * @var string
     */
    protected $replyTo;

    /**
     * Contains all 'cc' email addresses.
     * @var array
     */
    protected $cc = [];

    /**
     * Contains all 'bcc' email addresses.
     * @var array
     */
    protected $bcc = [];

    /**
     * The subject of the email.
     * @var string
     */
    protected $subject;

    /**
     * The text of the email.
     * @var string
     */
    protected $text;

    /**
     * The HTML of the email.
     * @var string
     */
    protected $html;

    /**
     * The email headers.
     * @var array
     */
    protected $headers = [];

    /**
     * The email attachments.
     * @var array
     */
    protected $attachments = [];

    /**
     * Errors related to the mail client
     *
     * @var array
     */
    protected $errors = [];

    /**
     * The name of the service
     * @var string
     */
    protected $name = 'MailService';


    /**
     * Returns the mail service name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc }
     */
    public function send()
    {
        return $this->doSend();
    }
    
    /**
     * Contains the logic for each service to send an email.
     * @return mixed the response of the email client.
     */
    abstract protected function doSend();

    /**
     * {@inheritdoc }
     */
    public function getTos()
    {
        return $this->to;
    }

    /**
     * Adds an 'to' email address.
     * @param string $email the email address.
     * @param string $name optional name for the email address.
     * @return MailProvider
     */
    public function addTo($email, $name = '')
    {
        if (!is_string($email)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($email) ? get_class($email) : gettype($email))
            ));
        }

        if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
        }

        if ($this->to === null) {
            $this->to = [];
        }

        $this->to[] = [
            'email' => $email,
            'name' => $name
        ];

        return $this;
    }

    /**
     * Sets the from email address.
     * @param string $email the email address.
     * @param string $email the name of the email address.
     * @return MailProvider
     */
    public function setFrom($email, $name = '')
    {
        if (!is_string($email)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($email) ? get_class($email) : gettype($email))
            ));
        }

        $this->from = $email;

        if($name !== ''){
            $this->setFromName($name);
        }

        return $this;
    }

    /**
     * {@inheritdoc }
     */
    public function getFrom()
    {
        return $this->from;    
    }

    /**
     * Sets the name of the from email address.
     * @param string $name the name.
     * @return MailProvider
     */
    public function setFromName($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
        }

        $this->fromName = $name;

        return $this;
    }

    /**
     * {@inheritdoc }
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * Sets the reply to email address.
     * @param string $email the reply to email address.
     * @return MailProvider
     */
    public function setReplyTo($email)
    {
        if (!is_string($email)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($email) ? get_class($email) : gettype($email))
            ));
        }

        $this->replyTo = $email;
        
        return $this;
    }

    /**
     * {@inheritdoc }
     */
    public function getReplyTo()
    {
        return $this->replyTo;
    }

    /**
     * {@inheritdoc }
     */
    public function getCcs()
    {
        return $this->cc;
    }

    /**
     * Adds a 'cc' email address.
     * @param string $email the email address.
     * @param string $name optional name for the email address.
     * @return MailProvider
     */
    public function addCc($email, $name = '')
    {
        if (!is_string($email)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($email) ? get_class($email) : gettype($email))
            ));
        }

        if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
        }

        if ($this->cc === null) {
            $this->cc = [];
        }

        $this->cc[] = [
            'email' => $email,
            'name' => $name
        ];

        return $this;
    }

    /**
     * Removes a 'cc' email address by the email address provided.
     * @param  string $email the email address to delete.
     * @return MailProvider
     */
    public function removeCc($email)
    {
        $this->removeFromlist($this->cc, $email, 'email');
        
        return $this;
    }

    /**
     * {@inheritdoc }
     */
    public function getBccs()
    {
        return $this->bcc;
    }

    /**
     * Adds a 'bcc' email address.
     * @param string $email the email address.
     * @param string $name optional name for the email address.
     * @return MailProvider
     */
    public function addBcc($email, $name = '')
    {
        if (!is_string($email)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($email) ? get_class($email) : gettype($email))
            ));
        }

        if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($name) ? get_class($name) : gettype($name))
            ));
        }

        if ($this->bcc === null) {
            $this->bcc = [];
        }

        $this->bcc[] = [
            'email' => $email,
            'name' => $name
        ];

        return $this;
    }

    /**
     * Removes a 'bcc' email address by the email address provided.
     * @param  string $email the email address to delete.
     * @return MailProvider
     */
    public function removeBcc($email)
    {
        $this->removeFromlist($this->bcc, $email, 'email');

        return $this;
    }

    /**
     * Sets the subject of the email.
     * @param string $subject the subject.
     * @return MailProvider
     */
    public function setSubject($subject)
    {
        if (!is_string($subject)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($subject) ? get_class($subject) : gettype($subject))
            ));
        }

        $this->subject = $subject;

        return $this;
    }

    /**
     * {@inheritdoc }
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Sets the text of the email.
     * @param string $text the text of the email.
     * @return MailProvider
     */
    public function setText($text)
    {
        if (!is_string($text)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($text) ? get_class($text) : gettype($text))
            ));
        }

        $this->text = $text;

        return $this;
    }

    /**
     * {@inheritdoc }
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Sets the HTML of the email.
     * @param string $html the HTML of the email.
     * @return MailProvider
     */
    public function setHtml($html)
    {
        if (!is_string($html)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($html) ? get_class($html) : gettype($html))
            ));
        }

        $this->html = $html;
       
        return $this;
    }

    /**
     * {@inheritdoc }
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * Adds an attachment to the email.
     * @param string $file the file path.
     * @param string $name optional name for the file.
     * @param string $type optional MIME type of the file.
     * @return MailProvider
     */
    public function addAttachment($file, $name = null, $type = null)
    {
        $this->attachments[] = $this->getAttachmentInfo($file, $name, $type);
        
        return $this;
    }

    /**
     * {@inheritdoc }
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Retrieves the file information of the file path given.
     * @param string $file the file path.
     * @param string $name optional name for the file.
     * @param string $type optional MIME type of the file.
     * @return array the file information.
     */
    private function getAttachmentInfo($file, $name = null, $type = null)
    {
        if (!is_string($file)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($file) ? get_class($file) : gettype($file))
            ));
        }

        $fileInfo = new \SplFileInfo($file); 
        
        if(!$fileInfo->isFile()){
            throw new \Exception(sprintf('File at path "%s" does not exist.', $file));
        }

        $info['file'] = $fileInfo;
            
        if ($name !== null) {
            $info['name'] = $name;
        }

        if ($type === null) {
            $type = mime_content_type($fileInfo->getRealPath());
        }
        $info['type'] = $type;
        
        return $info; 
    }

    /**
     * {@inheritdoc }
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Returns the email headers in JSON format.
     * @return string the email headers in json format.
     */
    public function getHeadersJson()
    {
        if (count($this->getHeaders()) <= 0) {
            return "{}";
        }
        return json_encode($this->getHeaders(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    }

    /**
     * Adds an email header by key and value
     * @param string $key   the key of the header value.
     * @param string $value the header value.
     * @return MailProvider
     */
    public function addHeader($key, $value)
    {
        if (!is_string($key)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($key) ? get_class($key) : gettype($key))
            ));
        }

        if (!is_string($value)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($value) ? get_class($value) : gettype($value))
            ));
        }

        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * Removes a header by the key given.
     * @param  string $key the header key.
     * @return MailProvider
     */
    public function removeHeader($key)
    {
        if (!is_string($key)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($key) ? get_class($key) : gettype($key))
            ));
        }

        unset($this->headers[$key]);
        
        return $this;

    }

    /**
     * Given a list of key/value pairs, removes the associated keys
     * where a value matches the given string ($item)
     *
     * @param Array $list - the list of key/value pairs
     * @param String $item - the value to be removed
     */
    private function removeFromList(&$list, $item, $keyField = null)
    {
        foreach ($list as $key => $val) {
            if ($keyField) {
                if ($val[$keyField] == $item) {
                    unset($list[$key]);
                }
            } else {
                if ($val == $item) {
                    unset($list[$key]);
                }
            }
        }
        //repack the indices
        $list = array_values($list);
    }

    /**
     * Returns the errors related to the mail client
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Sets the errors for the mail client
     * @param array $errors
     */
    public function setErrors($errors)
    {
        foreach ($errors as $error) {
            $this->addError($error);
        }
    }

    /**
     * Adds an error
     *
     * @param $error
     */
    public function addError($error)
    {
        $this->errors[] = $this->getName().': '.$error;
    }

    /**
     * Generates a multidimensional array containing the mail data.
     *
     * @return array $result - the multidimensional array containing the mail data
     */
    public function toArray()
    {
        $result = [
            'from' => [
                'email' => $this->getFrom(),
                'name' => $this->getFromName()
            ],
            'replyTo' => [
                'email' => $this->getReplyTo(),
                'name' => ''
            ],
            'subject' => $this->getSubject(),
            'text' => $this->getText(),
            'html' => $this->getHtml()
        ];

        $tos = [];
        foreach($this->getTos() as $to) {
            $tos[] = [
                'email' => $to['email'],
                'name' => $to['name']
            ];
        }
        $result['to'] = $tos;

        $ccs = [];
        foreach($this->getCcs() as $cc) {
            $ccs[] = [
                'email' => $cc['email'],
                'name' => $cc['name']
            ];
        }
        $result['cc'] = $ccs;

        $bccs = [];
        foreach($this->getBccs() as $bcc) {
            $bccs[] = [
                'email' => $bcc['email'],
                'name' => $bcc['name']
            ];
        }
        $result['bcc'] = $bccs;

        $attachments = [];
        foreach($this->getAttachments() as $attachment) {
            $attachments[] = [
                'file' => $attachment['file'],
                'name' => $attachment['name'],
                'type' => $attachment['type']
            ];
        }
        $result['attachments'] = $attachments;

        $headers = [];
        foreach($this->getHeaders() as $key => $value) {
            $headers[$key] = $value;
        }
        $result['headers'] = $headers;

        return $result;
    }

    /**
     * Sets the values by a multidimensional array given
     *
     * @param array $array - multidimensional array containing the mail values
     * @return void
     */
    public function fromArray(array $array)
    {
        if(isset($array['from']) && isset($array['from']['email'])) {
            $this->setFrom($array['from']['email']);

            if(isset($array['from']['name'])) {
                $this->setFromName($array['from']['name']);
            }
        }

        if(isset($array['to'])) {
            foreach($array['to'] as $to) {
                $this->addTo($to['email'], $to['name']);
            }
        }

        if(isset($array['cc'])) {
            foreach($array['cc'] as $cc) {
                $this->addCc($cc['email'], $cc['name']);
            }
        }

        if(isset($array['bcc'])) {
            foreach($array['bcc'] as $bcc) {
                $this->addBcc($bcc['email'], $bcc['name']);
            }
        }

        if(isset($array['replyTo']) && isset($array['replyTo']['email'])) {
            $this->setReplyTo($array['replyTo']['email']);
        }

        if(isset($array['subject'])) {
            $this->setSubject($array['subject']);
        }

        if(isset($array['text'])) {
            $this->setText($array['text']);
        }

        if(isset($array['html'])) {
            $this->setHtml($array['html']);
        }
        
        if(isset($array['attachments'])) {
            foreach($array['attachments'] as $attachment) {
                $this->addAttachment($attachment['file'], $attachment['name'], $attachment['type']);
            }
        }

        if(isset($array['headers'])) {
            foreach($array['headers'] as $key => $value) {
                $this->addHeader($key, $value);
            }
        }
        
    }

}
