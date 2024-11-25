<?php
/**
 * Webkul Software.
 *
 * @category    Webkul
 * @package     Webkul_OrderHistoryCommunication
 * @author      Webkul
 * @copyright   Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license     https://store.webkul.com/license.html
 */

namespace Webkul\OrderHistoryCommunication\Model\Mail\Template;

use Laminas\Mime\Mime;
use Laminas\Mime\PartFactory;
use Laminas\Mail\MessageFactory as MailMessageFactory;
use Laminas\Mime\MessageFactory as MimeMessageFactory;

class Message implements \Magento\Framework\Mail\MailMessageInterface 
{
    /**
     * @var \Laminas\Mime\PartFactory
     */
    protected $partFactory;
    /**
     * @var \Laminas\Mime\MessageFactory
     */
    protected $mimeMessageFactory;
    /**
     * @var \Laminas\Mail\Message
     */
    private $zendMessage;
    /**
     * @var \Laminas\Mime\Part[]
     */
    protected $parts = [];
    /**
     * Message constructor.
     *
     * @param \Laminas\Mime\PartFactory $partFactory
     * @param \Laminas\Mime\MessageFactory $mimeMessageFactory
     * @param string $charset
     */
    public function __construct(
        PartFactory $partFactory,
        MimeMessageFactory $mimeMessageFactory,
        $charset = 'utf-8'
    ) {
        $this->partFactory = $partFactory;
        $this->mimeMessageFactory = $mimeMessageFactory;
        $this->zendMessage = MailMessageFactory::getInstance();
        $this->zendMessage->setEncoding($charset);
    }
    /**
     * Add the HTML mime part to the message.
     *
     * @param string $content
     * @return $this
     */
    public function setBodyText($content)
    {
        $textPart = $this->partFactory->create();
        $textPart->setContent($content)
            ->setType(Mime::TYPE_TEXT)
            ->setCharset($this->zendMessage->getEncoding());
        $this->parts[] = $textPart;
        return $this;
    }
    /**
     * Add the text mime part to the message.
     *
     * @param string $content
     * @return $this
     */
    public function setBodyHtml($content)
    {
        $htmlPart = $this->partFactory->create();
        $htmlPart->setContent($content)
            ->setType(Mime::TYPE_HTML)
            ->setCharset($this->zendMessage->getEncoding());
        $this->parts[] = $htmlPart;
        return $this;
    }
    /**
     * Add the attachment mime part to the message.
     *
     * @param string $content
     * @param string $fileName
     * @param string $fileType
     * @return $this
     */
    public function setBodyAttachment($content, $fileName, $fileType)
    {
        $attachmentPart = $this->partFactory->create();
        $attachmentPart->setContent($content)
            ->setType($fileType)
            ->setFileName($fileName)
            ->setDisposition(Mime::DISPOSITION_ATTACHMENT)
            ->setEncoding(Mime::ENCODING_BASE64);
        $this->parts[] = $attachmentPart;
        return $this;
    }
    /**
     * Set parts to Laminas message body.
     *
     * @return $this
     */
    public function setPartsToBody()
    {
        $mimeMessage = $this->mimeMessageFactory->create();
        $mimeMessage->setParts($this->parts);
        $this->zendMessage->setBody($mimeMessage);
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function setBody($body)
    {
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function setSubject($subject)
    {
        $this->zendMessage->setSubject($subject);
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->zendMessage->getSubject();
    }
    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->zendMessage->getBody();
    }
    /**
     * {@inheritdoc}
     */
    public function setFrom($fromAddress)
    {
        $this->zendMessage->setFrom($fromAddress);
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function addTo($toAddress)
    {
        $this->zendMessage->addTo($toAddress);
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function addCc($ccAddress)
    {
        $this->zendMessage->addCc($ccAddress);
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function addBcc($bccAddress) 
    {
        $this->zendMessage->addBcc($bccAddress);
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function setReplyTo($replyToAddress)
    {
        $this->zendMessage->setReplyTo($replyToAddress);
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function getRawMessage()
    {
        return $this->zendMessage->toString();
    }
    /**
     * @inheritDoc
     */
    public function setMessageType($type)
    {
        return $this;
    }
}
