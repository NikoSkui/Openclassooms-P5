<?php

namespace App\Controller;


use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class AppController extends Controller
{

    /**
     * @Route("/", name="home")
     * @Method("GET")
     * @Cache(smaxage="10")
     */
	public function homePage()
	{
        if ($this->getUser()) {
            # code...
            return $this->redirectToRoute('admin_home');
        }
        return $this->render('default/home.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     * @Cache(smaxage="10")
     */
	public function contact(Request $request)
	{
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $formData = $form->getData();

            $mailer = $this->get("mailer");
            $title = "Message de " . $formData['username'] .' "' .$formData['mail'] . '"';

            $message = (new \Swift_Message($title))
                ->setFrom('Openclassroom5pteam@smtp.openclass-cours.ovh')
                ->setTo('opclass-p5@openclass-cours.ovh')
                ->setBody( $this->renderView('mails/contactUs.html.twig',array(
                    "formData" => $formData)));
            $mailer->send($message);
        }

        return $this->render('default/contact.html.twig', [
            'form' => $form->createView(),
        ]);
	}
}
