<?php

declare(strict_types=1);

namespace AppBundle\Email;

class Parsedown extends \Parsedown
{
    public function __construct()
    {
        $this->setBreaksEnabled(true);
    }

    protected function inlineLink($Excerpt)
    {
        $inlineLink = parent::inlineLink($Excerpt);

        $inlineLink['element']['attributes']['style'] = 'color: #4582db; text-decoration: underline;';

        return  $inlineLink;
    }

    protected function blockHeader($Line)
    {
        if (isset($Line['text'][1])) {
            $level = 1;

            while (isset($Line['text'][$level]) && $Line['text'][$level] === '#') {
                $level ++;
            }

            if ($level > 6) {
                return null;
            }

            $text = trim($Line['text'], '# ');


            $min = min(6, $level);

            if ($min == 1) {
                $Block = $this->customHeaderBlock($text);
            } else {
                $Block = [
                    'element' => [
                        'name' => 'h' . min(6, $level),
                        'text' => $text,
                        'handler' => 'line',
                    ],
                ];
            }


            return $Block;
        }
        return null;
    }

    private function customHeaderBlock(string $text): array
    {
        return [
            'element' => [
                'name' => 'div',
                'attributes' => [
                    'class' => 'title',
                    'style' => 'color: #4582db; font-size: 18px; font-weight: bold; line-height: 24px;',
                ],
                'text' => $text,
                'handler' => 'line',
            ],
        ];
    }
}
