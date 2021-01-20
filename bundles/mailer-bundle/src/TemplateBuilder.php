<?php

namespace MailerBundle;

use MailerBundle\Exception\TemplateException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;

final class TemplateBuilder
{
    /**
     * @var Environment
     */
    private $templating;

    /**
     * @var array
     */
    private $templates = [];

    public function __construct(ParameterBagInterface $parameterBag, Environment $templating)
    {
        $mailerConfig = $parameterBag->get('mailer');

        $this->templates = $mailerConfig['templates'];

        $this->templating = $templating;
    }

    /**
     * @param $key
     * @param array $data
     *
     * @return Template
     *
     * @throws TemplateException
     */
    public function build($key, array $data = []): Template
    {
        $config = $this->getTemplateConfig($key);

        $subject = $config['subject'] ?? null;
        $template = $config['template'] ?? null;

        return new Template($subject, $template, $data);
    }

    /**
     * @param $key
     *
     * @return array|null
     *
     * @throws TemplateException
     */
    private function getTemplateConfig($key): ?array
    {
        $filter = array_filter($this->templates, static function ($v) use ($key) {
            return $v['key'] === $key;
        }, ARRAY_FILTER_USE_BOTH);

        if (empty($filter)) {
            throw new TemplateException(sprintf("Config template '%s' not found", $key));
        }

        return current($filter);
    }
}
