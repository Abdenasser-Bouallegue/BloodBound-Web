<?php

namespace App\Controller;

use FFI;
use App\Entity\Donation;
use App\Entity\Facility;
use App\Entity\Emergency;
use App\Form\FacilityType;
use App\Entity\Appointment;
use App\Form\AppointmentType;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FacilityAppoController extends AbstractController
{
    #[Route('/facility/donation/confirm', name: 'app_confirm_donation')]
    public function confirmDonation(Request $request, EntityManagerInterface $em): Response
    {
        $session = $this->get('session');
        $facilityData = $session->get('facility_data');
        $emergencyData = $session->get('emergency_data');
        $appoData = $session->get('appointment_data');
        
        if (!$facilityData || !$appoData || !$emergencyData) {
            return $this->redirectToRoute('app_select_facility');
        }

        $appointment = new Appointment();
        $appointment->setRdv($appoData->getRdv());
        $appointment->setStatus($appoData->getStatus());
        $user = $this->getUser();
        $appointment->setUser($user);
        $facility = $em->getRepository(Facility::class)->find($facilityData->getId());
        $appointment->setFacility($facility);
        $em->persist($appointment);

        $donation = new Donation();
        $emergency = $em->getRepository(Emergency::class)->find($emergencyData->getId());
        $emergency->setStatus("Completed");
        $donation->setEmergency($emergency);
        $donation->setAppointment($appointment);
        $em->persist($donation);
        
        $em->flush();

        $session->remove('emergency_data');
        $session->remove('facility_data');
        $session->remove('appointment_data');

        return $this->render('FrontOffice/emergency/show.html.twig',[
            'emergency' => $emergency,
        ]);
    }

    #[Route('/facility/donation', name: 'app_index_donation')]
    public function indexDonation(Request $request, EntityManagerInterface $em): Response
    {
        $session = $this->get('session');
        $facilityData = $session->get('facility_data');
        $emergencyData = $session->get('emergency_data');
        $appoData = $session->get('appointment_data');
        
        if (!$facilityData || !$appoData || !$emergencyData) {
            return $this->redirectToRoute('app_select_facility');
        }

        $appointment = new Appointment();
        $appointment->setRdv($appoData->getRdv());
        $appointment->setStatus($appoData->getStatus());
        $user = $this->getUser();
        $appointment->setUser($user);
        $facility = $em->getRepository(Facility::class)->find($facilityData->getId());
        $appointment->setFacility($facility);

        $donation = new Donation();
        $emergency = $em->getRepository(Emergency::class)->find($emergencyData->getId());
        $donation->setEmergency($emergency);
        $donation->setAppointment($appointment);

        return $this->render('FrontOffice/FacilityAppo/test3.html.twig',[
            'emergency' => $emergency,
            'donation' => $donation,
            'appointment' => $appointment,
            'facility' => $facility
        ]);
    }


    #[Route('/facility/appo', name: 'app_select_appo')]
    public function selectAppo(Request $request): Response
    {
        $session = $this->get('session');
        $facilityData = $session->get('facility_data');
        $emergencyData = $session->get('emergency_data');

        if (!$facilityData || !$emergencyData) {
            return $this->redirectToRoute('app_select_facility');
        }
        
        $appointment = new Appointment();
        $form =  $this->createForm(AppointmentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rdv = $form->get('rdv')->getData()->format('Y-m-d H:i:s');
            $appointment->setRdv(new DateTimeImmutable($rdv));
            $user = $this->getUser();
            $appointment->setStatus("Booked");
            $appointment->setFacility($facilityData);
            $appointment->setUser($user);

            $session = $this->get('session');
            $session->set('appointment_data',$appointment);

            return $this->redirectToRoute('app_index_donation');
        }


        return $this->render('FrontOffice/FacilityAppo/test2.html.twig', [
            'form' =>  $form->createView(),
        ]);
    }

    #[Route('/{id}/facility', name: 'app_select_facility')]
    public function selectFacility(Request $request, EntityManagerInterface $em, $id): Response
    {
        $emergency = $em->getRepository(Emergency::class)->find($id);
        $facilities = $em->getRepository(Facility::class)->findAll();
        $form = $this->createForm(FacilityType::class, null, [
            'facilities' => $facilities,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $facility = $form->getData()['name'];
            $session = $this->get('session');
            $session->set('emergency_data', $emergency);
            $session->set('facility_data', $facility);

            return $this->redirectToRoute('app_select_appo');
        }


        return $this->render('FrontOffice/FacilityAppo/test.html.twig', [
            'form' =>  $form->createView(),
        ]);
    }
    
    
}
