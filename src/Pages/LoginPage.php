<?php

declare(strict_types=1);

namespace Dzelenika\Agents\Pages;

use Mustache_Engine;

/**
 * Renders the login page using the master layout and login template.
 */
class LoginPage
{
    /**
     * @param Mustache_Engine $mustache The Mustache template engine.
     */
    public function __construct(private readonly Mustache_Engine $mustache) {}

    /**
     * Renders and outputs the full login HTML page.
     *
     * @return void
     */
    public function render(): void
    {
        $content  = $this->mustache->loadTemplate('login')->render([]);
        $template = $this->mustache->loadTemplate('master');

        echo $template->render([
            'title'   => 'Login',
            'content' => $content,
            'scripts' => '<script src="/js/login.js" type="module"></script>',
        ]);
    }
}
