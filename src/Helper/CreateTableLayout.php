<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Escaper;

class CreateTableLayout extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        Context $context,
        private readonly Escaper $escaper,
    ) {
        parent::__construct($context);
    }

    public function convertContentWithSpansToHtml(array $content): string
    {
        if (empty($content[0]['text'])) {
            return '';
        }

        $text = $content[0]['text'];
        $spans = $content[0]['spans'] ?? [];

        if (empty($spans)) {
            return $this->escaper->escapeHtml($text);
        }

        [$startTags, $endTags] = $this->buildSpanTags($spans);

        return $this->renderTextWithTags($text, $startTags, $endTags);
    }

    private function buildSpanTags(array $spans): array
    {
        $startTags = [];
        $endTags = [];

        foreach ($spans as $span) {
            [$tag, $attributes] = $this->resolveTagAndAttributes($span);

            if ($tag) {
                $startTags[$span['start']][] = "<{$tag}{$attributes}>";
                $endTags[$span['end']][] = "</{$tag}>";
            }
        }

        return [$startTags, $endTags];
    }

    private function resolveTagAndAttributes(array $span): array
    {
        $tag = null;
        $attributes = '';

        switch ($span['type']) {
            case 'strong':
                $tag = 'strong';
                break;
            case 'em':
                $tag = 'em';
                break;
            case 'hyperlink':
                $tag = 'a';
                if (!empty($span['data']['url'])) {
                    $href = $this->escaper->escapeHtml($span['data']['url']);
                    $target = !empty($span['data']['target'])
                        ? ' target="' . $this->escaper->escapeHtml($span['data']['target']) . '"'
                        : '';
                    $attributes = ' href="' . $href . '"' . $target;
                }
                break;
        }

        return [$tag, $attributes];
    }

    private function renderTextWithTags(string $text, array $startTags, array $endTags): string
    {
        $result = '';
        $length = mb_strlen($text);

        for ($i = 0; $i <= $length; $i++) {
            if (!empty($endTags[$i])) {
                $result .= implode('', array_reverse($endTags[$i]));
            }

            if (!empty($startTags[$i])) {
                $result .= implode('', $startTags[$i]);
            }

            if ($i < $length) {
                $result .= $this->escaper->escapeHtml(mb_substr($text, $i, 1));
            }
        }

        return $result;
    }
}
