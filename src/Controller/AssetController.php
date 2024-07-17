<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AssetController extends AbstractController
{
    #[Route('/asset/{name}', name: 'asset')]
    public function index(string $name): Response
    {
        $response = new Response();

        if (str_ends_with($name, '.css')) {
            $response->headers->set('Content-Type', 'text/css');
            $response->setContent(<<<ASSET
                /* asset $name */
                body {
                    color: gray;
                    background-color: black;
                }

                ASSET
            );

        } elseif (str_ends_with($name, '.js')) {
            $response->headers->set('Content-Type', 'text/javascript');
            $response->setContent(<<<ASSET
                /* asset $name */
                console.log('in $name');

                ASSET
            );
        }

        return $response;
    }
}
