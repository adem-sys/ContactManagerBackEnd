<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

#[Route('/contacts')]
class ContactController extends AbstractController
{
    #[Route('/', name: 'app_contact_index', methods: ['GET'])]
    public function index(ContactRepository $contactRepository): JsonResponse
    {
        $contacts = $contactRepository->findAll();

        // Convertir les contacts en tableau associatif
        $data = array_map(function($contact) {
            return [
                'id' => $contact->getId(),
                'firstName' => $contact->getPrenom(),
                'lastName' => $contact->getNom(),
                'age' => $contact->getAge(),
                'country' => $contact->getPays(),
                'email' => $contact->getEmail(),
                'phone' => $contact->getTelephone(),
            ];
        }, $contacts);

        return new JsonResponse(['contacts' => $data]);
    }

    #[Route('/new', name: 'app_contact_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['firstName'], $data['lastName'], $data['age'], $data['country'], $data['email'], $data['phone'])) {
            $contact = new Contact();
            $contact->setNom($data['lastName']);
            $contact->setPrenom($data['firstName']);
            $contact->setAge($data['age']);
            $contact->setPays($data['country']);
            $contact->setEmail($data['email']);
            $contact->setTelephone($data['phone']);

            try {
                $entityManager->persist($contact);
                $entityManager->flush();
                
                $contactData = [
                    'id' => $contact->getId(),
                    'firstName' => $contact->getNom(),
                    'lastName' => $contact->getPrenom(),
                    'age' => $contact->getAge(),
                    'country' => $contact->getPays(),
                    'email' => $contact->getEmail(),
                    'phone' => $contact->getTelephone(),
                ];

                return new JsonResponse(['contact' => $contactData], Response::HTTP_CREATED);
            } catch (UniqueConstraintViolationException $e) {
                return new JsonResponse(['error' => 'L\'email existe déjà. Veuillez utiliser une adresse email différente.'], Response::HTTP_CONFLICT);
            }
        }

        return new JsonResponse(['error' => 'Données invalides'], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}/edit', name: 'app_contact_edit', methods: ['PUT'])]
    public function edit(Request $request, Contact $contact, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            // Mettez à jour le contact avec les nouvelles données
            if (isset($data['firstName'])) {
                $contact->setNom($data['firstName']);
            }
            if (isset($data['lastName'])) {
                $contact->setPrenom($data['lastName']);
            }
            if (isset($data['age'])) {
                $contact->setAge($data['age']);
            }
            if (isset($data['country'])) {
                $contact->setPays($data['country']);
            }
            if (isset($data['email'])) {
                $contact->setEmail($data['email']);
            }
            if (isset($data['phone'])) {
                $contact->setTelephone($data['phone']);
            }

            $entityManager->flush();

            return new JsonResponse(['message' => 'Contact updated successfully','contact' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            // En cas d'erreur, retournez une réponse JSON avec un message d'erreur
            return new JsonResponse(['error' => 'Une erreur s\'est produite lors de la mise à jour du contact'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    #[Route('/{id}', name: 'app_contact_delete', methods: ['DELETE'])]
    public function delete(Contact $contact, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $entityManager->remove($contact);
            $entityManager->flush();

            return new JsonResponse(['message' => 'Contact deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            // En cas d'erreur, retournez une réponse JSON avec un message d'erreur
            return new JsonResponse(['error' => 'Une erreur s\'est produite lors de la suppression du contact'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
