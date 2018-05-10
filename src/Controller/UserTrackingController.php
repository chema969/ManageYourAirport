<?php

namespace App\Controller;

use App\Entity\Location;
use App\Entity\LocationsCollection;
use App\Entity\User;
use App\Form\LocationType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserTrackingController extends Controller
{
    /**
     * @Route("/track", name="user_tracking")
     */
    public function index(Request $request)
    {
        $location=new Location();
        $form = $this->createFormBuilder()
            ->add('latitude', HiddenType::class)
            ->add('longitude', HiddenType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $latitude=$request->request->get('latitude');
            $longitude=$request->request->get('longitude');


            $location->setLatitude((float)$latitude);
            $location->setLongitude((float)$longitude);

            $user=$this->getUser();
            $user->setLocation($location);
            $locationsCollection = $this->getDoctrine()->getRepository('App:LocationsCollection')->getCollection(1);
            $locationsCollection[0]->addLocation($location);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->persist($locationsCollection[0]);
            $manager->flush();
            return $this->redirectToRoute('homepage');
        }

        return $this->render('user_tracking/index.html.twig',[
            'form' => $form->createView(),
        ]);
    }
}