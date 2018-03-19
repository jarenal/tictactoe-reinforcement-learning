<?php

namespace Jarenal\Core\View;


class View
{
    public function render(string $template, $vars = [])
    {
        $content = file_get_contents(__DIR__ . "/../../Templates/" .$template);
        foreach ($vars as $tag => $value) {
            $content = str_replace('{{'.$tag.'}}', $value, $content);
        }
        return $content;
    }
}