<?php

namespace MailProvider\Service;

use MailProvider\Provider\MailProvider;
use PHPMailer as PHPMailerLibrary;

/**
 * PHPMailer
 *
 * Sends an email with the PHPMailer class.
 *
 * @author Leo Flapper <info@leoflapper.nl>
 * @version 1.1.1
 * @since 1.1.0
 * @see https://github.com/PHPMailer/PHPMailer
 */
class PHPMailer extends MailProvider
{

    /**
     * Contains the original PHPMailer class.
     * Used for resetting after the email has been send.
     * @var PHPMailerLibrary
     */
    protected $originalClient;
    /**
     * The name of the mail service
     * @var string
     */
    protected $name = 'PHPMailer';
    /**
     * Contains the supported protocols by PHPMailer.
     * @var array
     */
    private $protocols = [
        'smtp' => true,
        'mail' => true,
        'sendmail' => true,
        'qmail' => true,
        'ssl' => true,
        'tls' => true
    ];

    /**
     * Sets the client by the PHPMailer class given or adds a new PHPMailer class.
     * @param PHPMailerLibrary|null $client the optional PHPMailer class.
     */
    public function __construct($client = null)
    {
        $this->setClient($client);
    }

    /**
     * Sets the client by the PHPMailer class given or adds a new PHPMailer class.
     * If an original client exists, this original client will be
     * cloned to use as client.
     * @param PHPMailerLibrary|null $client the optional PHPMailer class.
     */
    public function setClient(PHPMailerLibrary $client = null)
    {
        if ($client !== null) {
            $this->originalClient = $client;
        }

        if ($this->originalClient) {
            $this->client = clone $this->originalClient;
        } else {
            $this->client = new PHPMailerLibrary();
        }
    }

    /**
     * {@inheritdoc}. Sets the content type in
     * the PHPMailer class to 'text/html'.
     * @param string $html the html string.
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

        $this->client->isHTML(true);
        $this->html = $html;

        return $this;
    }

    /**
     * {@inheritdoc}. Sets the content type in
     * the PHPMailer class to 'text/plain'.
     * @param [type] $text [description]
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

        $this->client->isHTML(false);
        $this->text = $text;

        return $this;
    }

    /**
     * Sets the SMTP hostname to send emails.
     * @param string $host the host url
     */
    public function setHost($host)
    {
        if (!is_string($host)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($host) ? get_class($host) : gettype($host))
            ));
        }

        $this->client->Host = $host;

        return $this;
    }

    /**
     * Sets the SMTP server port number.
     * @param integer $port the port number.
     */
    public function setPort($port)
    {
        if (!is_numeric($port)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a numeric argument; received "%s"',
                __METHOD__,
                (is_object($port) ? get_class($port) : gettype($port))
            ));
        }

        if (587 === $port) {
            $this->client->SMTPSecure = 'tls';
        }

        $this->client->Port = $port;

        return $this;
    }

    /**
     * Sets the protocol to use.
     * @param string $protocol the protocol to use.
     */
    public function setProtocol($protocol)
    {
        if (!is_string($protocol)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($protocol) ? get_class($protocol) : gettype($protocol))
            ));
        }

        $protocol = strtolower($protocol);
        if (!isset($this->protocols[$protocol])) {
            throw new \Exception(
                sprintf('Protocol %s does not exist; Existing protocols are %s',
                    $protocol,
                    implode(', ', $this->protocols)
                )
            );
        }

        if ($protocol === 'smtp') {
            $this->client->isSMTP();
        } elseif ($protocol === 'mail') {
            $this->client->isMail();
        } elseif ($protocol === 'sendmail') {
            $this->client->isSendmail();
        }

        if ($protocol === 'ssl' || $protocol === 'tls') {
            $this->client->isSMTP();
            $this->client->SMTPSecure = $protocol;
        }

        return $this;
    }

    /**
     * Sets SMTP Login authentication
     * @param string $username the username
     * @param string $password the password
     */
    public function setLogin($username, $password)
    {
        if (!is_string($username)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($username) ? get_class($username) : gettype($username))
            ));
        }

        if (!is_string($password)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($password) ? get_class($password) : gettype($password))
            ));
        }

        $this->client->SMTPAuth = true;
        $this->client->Username = $username;
        $this->client->Password = $password;

        return $this;
    }

    /**
     * Returns a property by the key given
     *
     * @param $property
     * @return mixed
     */
    public function getProperty($property)
    {
        return $this->client->$property;
    }

    /**
     * Sets a property in the PHPMailer class.
     * @param string $property the property name.
     * @param mixed $value the property value.
     */
    public function setProperty($property, $value)
    {
        if (!is_string($property)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expects a string argument; received "%s"',
                __METHOD__,
                (is_object($property) ? get_class($property) : gettype($property))
            ));
        }

        $this->client->$property = $value;
    }

    public function addStringAttachment(
        $string,
        $filename,
        $encoding = 'base64',
        $type = '',
        $disposition = 'attachment'
    )
    {
        // If a MIME type is not specified, try to work it out from the file name
        if ($type == '') {
            $type = PHPMailerLibrary::filenameToType($filename);
        }
        // Append to $attachment array
        $this->attachments[] = array(
            0 => $string,
            1 => $filename,
            2 => basename($filename),
            3 => $encoding,
            4 => $type,
            5 => true, // isStringAttachment
            6 => $disposition,
            7 => 0
        );
    }

    /**
     * Sends the email with the PHPMailer class.
     * @return boolean true if mail send, false if not.
     */
    protected function doSend()
    {
        $this->client->setFrom($this->getFrom(), $this->getFromName());
        $this->client->addReplyTo($this->getReplyTo());

        $this->setToData();
        $this->setCcData();
        $this->setBccData();

        $this->setAttachmentData();

        $this->client->Subject = $this->getSubject();
        if ($this->client->ContentType === 'text/html') {
            $this->client->Body = $this->getHtml();
        } else {
            $this->client->Body = $this->getText();
        }

        $response = false;

        if ($this->client->send()) {
            $this->setClient();
            $response = true;

        } else {
            if ($this->getErrorInfo()) {
                $this->addError($this->getErrorInfo());
            }
        }

        return $response;
    }

    /**
     * Sets the 'to' email addresses.
     */
    private function setToData()
    {
        foreach ($this->getTos() as $to) {
            $this->client->addAddress($to['email'], $to['name']);
        }
    }

    /**
     * Sets the 'cc' email addresses.
     */
    private function setCcData()
    {
        foreach ($this->getCcs() as $cc) {
            $this->client->addCC($cc['email'], $cc['name']);
        }
    }

    /**
     * Sets the 'bcc' email addresses.
     */
    private function setBccData()
    {
        foreach ($this->getBccs() as $bcc) {
            $this->client->addBcc($bcc['email'], $bcc['name']);
        }
    }

    /**
     * Sets the attachment data in the desired PHPMailer format.
     */
    private function setAttachmentData()
    {
        foreach ($this->getAttachments() as $attachment) {
            if (isset($attachment['file'])) {
                $this->client->addAttachment(
                    $attachment['file']->getRealPath(),
                    $attachment['name'],
                    $encoding = 'base64',
                    $attachment['type']
                );
            }

            if (isset($attachment[0])) {
                $this->client->addStringAttachment(
                    $attachment[0],
                    $attachment[1],
                    $attachment[3],
                    $attachment[4],
                    $attachment[6]
                );
            }
        }
    }

    /**
     * Returns the PHP Mailer errors
     *
     * @return string
     */
    public function getErrorInfo()
    {
        return $this->client->ErrorInfo;
    }
}
