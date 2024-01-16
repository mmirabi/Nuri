<?php

namespace Botble\Base\Forms\FieldOptions;

use Closure;
use Illuminate\Support\Facades\Blade;

class HtmlFieldOption
{
    protected string $html = '';

    public static function make(): static
    {
        return new static();
    }

    public function view(string $view, array $data = [], array $mergeData = []): static
    {
        return $this->content(
            view($view, $data, $mergeData)->render()
        );
    }

    public function bladeContent(string|Closure $content, array $data = [], bool $deleteCachedView = false): static
    {
        return $this->content(
            Blade::render(value($content) ?: '', $data, $deleteCachedView)
        );
    }

    public function content(string|Closure $content): static
    {
        $this->html = value($content);

        return $this;
    }

    public function toArray(): array
    {
        return [
            'html' => $this->html,
        ];
    }
}
