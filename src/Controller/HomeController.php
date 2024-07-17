<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\WebLink\Link;

class HomeController extends AbstractController
{
    #[Route('/index.html', name: 'home')]
    public function index(Request $request): Response
    {
        $this->addLink($request, (new Link('preload', '/asset/app.js'))->withAttribute('as', 'script')->withAttribute('crossorigin', 'anonymous'));
        $this->addLink($request, (new Link('preload', '/asset/app.css'))->withAttribute('as', 'style')->withAttribute('crossorigin', 'anonymous'));

        return new Response(<<<HTML
            <!DOCTYPE html>
            <html lang="en">
                <head>
                    <title>Demo</title>
                    <link rel="stylesheet" href="/asset/app.css">
                    <script type="javascript" src="/asset/app.js"></script>
                </head>
                <body>
                    <h1>Demo</h1>
                </body>
            </html>
            HTML
        );
    }
}
