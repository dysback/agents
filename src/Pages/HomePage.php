<?php

declare(strict_types=1);

namespace Dzelenika\Agents\Pages;

use Mustache_Engine;

/**
 * Renders the home page using the master layout and home template.
 *
 * JWT auth state is managed client-side via localStorage.
 */
class HomePage
{
    /**
     * @param Mustache_Engine $mustache The Mustache template engine.
     */
    public function __construct(private readonly Mustache_Engine $mustache) {}

    /**
     * Renders and outputs the full home HTML page.
     *
     * @return void
     */
    public function render(): void
    {
        $content  = $this->mustache->loadTemplate('home')->render([]);
        $template = $this->mustache->loadTemplate('master');

        echo $template->render([
            'title'   => 'Home',
            'content' => $content,
            'scripts' => '<script src="/js/index.js" type="module"></script>',
        ]);
    }
}
