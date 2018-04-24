<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Form\User\RegisterType;
use MsgPhp\User\Command\CreateUserCommand;
use SimpleBus\SymfonyBridge\Bus\CommandBus;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 * @Route("/register", name="register")
 */
final class RegisterController
{
    public function __invoke(
        Request $request,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        Environment $twig,
        CommandBus $bus
    ): Response {
        $form = $formFactory->createNamed('', RegisterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bus->handle(new CreateUserCommand($form->getData()));
            $flashBag->add('success', 'You\'re successfully registered.');

            return new RedirectResponse('/login');
        }

        return new Response($twig->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
