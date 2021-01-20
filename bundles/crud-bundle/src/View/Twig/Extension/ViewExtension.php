<?php

namespace CrudBundle\View\Twig\Extension;

use CrudBundle\View\ModelInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ViewExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('view_render', [$this, 'render'], [
                'needs_environment' => true,
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function render(Environment $environment, string $name, ModelInterface $model): string
    {
        if (true === $model->hasChildren() && count($model->getChildrenByCaptureTo($name)) > 0) {
            /** @var ModelInterface $children */
            $model = current($model->getChildrenByCaptureTo($name));
        }
        $template = $model->getTemplate();

        if (!$template) {
            throw new \Exception('$template not found');
        }

        $context = [
            'view' => $model,
        ];

        return $environment->render($template, $context);
    }
}
