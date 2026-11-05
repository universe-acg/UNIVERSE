<?php
namespace App\Service;
use Symfony\Component\Mime\Email;
/** Bridge for existing message builders while delivery uses Symfony Mailer. */
class MailMessage extends Email
{
    public function __construct(?string $subject = null) { parent::__construct(); if ($subject) $this->subject($subject); }
    public function setFrom($value, ?string $name = null): static { return $this->from(is_array($value) ? array_key_first($value) : $value); }
    public function setTo($value): static { return $this->to(...(array) $value); }
    public function setReplyTo($value): static { return $this->replyTo(...(array) $value); }
    public function setSubject($value): static { return $this->subject($value); }
    public function setBody($value, $type = 'text/plain'): static { return $type === 'text/html' ? $this->html($value) : $this->text($value); }
    public function addPart($value, $type = 'text/plain'): static { return $this->setBody($value, $type); }
}
