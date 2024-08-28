<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;

class ContactControllerTest extends WebTestCase
{
    public function testAddUpdateDeleteContact()
    {
        // Sauvegarder les gestionnaires d'exceptions avant le test
        $originalExceptionHandlers = set_exception_handler(null);

        try {
            // Créer un client HTTP pour interagir avec l'application
            $client = static::createClient();
            $container = $client->getContainer();
            $entityManager = $container->get(EntityManagerInterface::class);

            // Étape 1: Ajouter un nouveau contact
            $client->request('POST', '/contacts/new', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
                'firstName' => 'John',
                'lastName' => 'Doe',
                'age' => 30,
                'country' => 'USA',
                'email' => 'john.doe@example.com',
                'phone' => '123456789'
            ]));
            $this->assertEquals(201, $client->getResponse()->getStatusCode());
            $this->assertResponseIsSuccessful();
            $content = $client->getResponse()->getContent();
            $data = json_decode($content, true);

            $this->assertSame('Doe', $data['contact']['firstName']);
            $this->assertSame('John', $data['contact']['lastName']);

            $contactId = $data['contact']['id'];

            // Étape 2: Mettre à jour le contact
            $client->request('PUT', "/contacts/$contactId/edit", [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
                'firstName' => 'Jane',
                'lastName' => 'Smith'
            ]));

            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertResponseIsSuccessful();
            $content = $client->getResponse()->getContent();
         
            $data = json_decode($content, true);
            
            $this->assertSame('Jane', $data['contact']['firstName']);
            $this->assertSame('Smith', $data['contact']['lastName']);
     
            // Étape 3: Supprimer le contact
            $client->request('DELETE', "/contacts/$contactId");
            $this->assertResponseIsSuccessful();

            // Vérifier que le contact a bien été supprimé
            $deletedContact = $entityManager->getRepository(Contact::class)->find($contactId);
            $this->assertNull($deletedContact);  

        } finally {
            // Restaurer les gestionnaires d'exceptions après le test
            set_exception_handler($originalExceptionHandlers);
        }
    }
}
