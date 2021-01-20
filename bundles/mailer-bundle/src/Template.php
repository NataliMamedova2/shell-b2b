<?php

namespace MailerBundle;

final class Template
{
    private $subject;
    private $templatePath;
    private $data = [];

    public function __construct(string $subject, string $templatePath, array $data = [])
    {
        $this->subject = $subject;
        $this->templatePath = $templatePath;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
