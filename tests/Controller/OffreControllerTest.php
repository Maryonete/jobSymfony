<?php

namespace App\Tests\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Offre;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class OffreControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $offreRepository;
    private string $path = '/admin/';


    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->offreRepository = $this->manager->getRepository(Offre::class);

        foreach ($this->offreRepository->findAll() as $object) {
            $this->manager->remove($object);
        }
        // Log in a user (if needed)
        $userRepo = $this->getContainer()->get('doctrine')->getRepository(User::class);
        $admin = $userRepo->findOneByEmail('test@test.fr');
        if (!$admin) {
            $admin = new User();
            $admin->setRoles(['ROLE_ADMIN']);
            $admin->setPassword('test');
            $admin->setEmail('test@test.fr');
            $this->manager->persist($admin);
        }
        $this->manager->flush();
    }




    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'offre[dateCandidature]' => 'Testing',
            'offre[entreprise]' => 'Testing',
            'offre[lieu]' => 'Testing',
            'offre[url]' => 'Testing',
            'offre[contact]' => 'Testing',
            'offre[reponse]' => 'Testing',
            'offre[reponse_at]' => 'Testing',
            'offre[lettre_motivation]' => 'Testing',
            'offre[type]' => 'Testing',
            'offre[relance_at]' => 'Testing',
            'offre[freelance]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->offreRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Offre();
        $fixture->setDateCandidature(new DateTime());
        $fixture->setEntreprise('My Title');
        $fixture->setLieu('My Title');
        $fixture->setUrl('My Title');
        $fixture->setContact('My Title');
        $fixture->setReponse('My Title');
        $fixture->setReponseAt(new DateTime());
        $fixture->setLettreMotivation('My Title');
        $fixture->setType('My Title');
        $fixture->setRelanceAt(new DateTime());
        $fixture->setFreelance('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Offre');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Offre();
        $fixture->setDateCandidature(new DateTime());
        $fixture->setEntreprise('Value');
        $fixture->setLieu('Value');
        $fixture->setUrl('Value');
        $fixture->setContact('Value');
        $fixture->setReponse('Value');
        $fixture->setReponseAt(new DateTime());
        $fixture->setLettreMotivation('Value');
        $fixture->setType('Value');
        $fixture->setRelanceAt(new DateTime());
        $fixture->setFreelance('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'offre[dateCandidature]' => 'Something New',
            'offre[entreprise]' => 'Something New',
            'offre[lieu]' => 'Something New',
            'offre[url]' => 'Something New',
            'offre[contact]' => 'Something New',
            'offre[reponse]' => 'Something New',
            'offre[reponse_at]' => new DateTime(),
            'offre[lettre_motivation]' => 'Something New',
            'offre[type]' => 'Something New',
            'offre[relance_at]' => new DateTime(),
            'offre[freelance]' => 'Something New',
        ]);

        self::assertResponseRedirects('/offre/');

        $fixture = $this->offreRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getDateCandidature());
        self::assertSame('Something New', $fixture[0]->getEntreprise());
        self::assertSame('Something New', $fixture[0]->getLieu());
        self::assertSame('Something New', $fixture[0]->getUrl());
        self::assertSame('Something New', $fixture[0]->getContact());
        self::assertSame('Something New', $fixture[0]->getReponse());
        self::assertSame('Something New', $fixture[0]->getReponse_at());
        self::assertSame('Something New', $fixture[0]->getLettre_motivation());
        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getRelance_at());
        self::assertSame('Something New', $fixture[0]->getFreelance());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Offre();
        $fixture->setDateCandidature(new DateTime());
        $fixture->setEntreprise('Value');
        $fixture->setLieu('Value');
        $fixture->setUrl('Value');
        $fixture->setContact('Value');
        $fixture->setReponse('Value');
        $fixture->setReponseAt(new DateTime());
        $fixture->setLettreMotivation('Value');
        $fixture->setType('Value');
        $fixture->setRelanceAt(new DateTime());
        $fixture->setFreelance('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/offre/');
        self::assertSame(0, $this->offreRepository->count([]));
    }
}
