<?php

namespace App\EventSubscriber;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Validator\Constraints\Date;

class LoginSubscriber implements EventSubscriberInterface
{
    //  on ne peut pas appeler nos objets dans le constructeur
    // on doit injecter le service de sécurité
    // on peut alors appeler les objets dans la méthode onLogin
    private $security;
    private $em;
    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
    }

    public function onLogin()
    {
        // MAJ date dernière connexion
        
        // $user = $this->getUser();  ne peut pas fonctionner dans un event subscriber
        $user = $this->security->getUser();
        // dd($user);
        $user->setLastLoginAt(new DateTime());
        $this->em->flush();




    }

    // fonction obligatoire à implémenter qui répertotiera les évènements dans un tableau
    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLogin',
        ];
    }



}