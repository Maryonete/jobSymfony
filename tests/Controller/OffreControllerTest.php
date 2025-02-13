<?php

namespace App\Tests\Controller;

use App\Entity\Offre;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class OffreControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $offreRepository;
    private string $path = '/offre/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->offreRepository = $this->manager->getRepository(Offre::class);

        foreach ($this->offreRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Offre index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
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
        $fixture->setDateCandidature('My Title');
        $fixture->setEntreprise('My Title');
        $fixture->setLieu('My Title');
        $fixture->setUrl('My Title');
        $fixture->setContact('My Title');
        $fixture->setReponse('My Title');
        $fixture->setReponse_at('My Title');
        $fixture->setLettre_motivation('My Title');
        $fixture->setType('My Title');
        $fixture->setRelance_at('My Title');
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
        $fixture->setDateCandidature('Value');
        $fixture->setEntreprise('Value');
        $fixture->setLieu('Value');
        $fixture->setUrl('Value');
        $fixture->setContact('Value');
        $fixture->setReponse('Value');
        $fixture->setReponse_at('Value');
        $fixture->setLettre_motivation('Value');
        $fixture->setType('Value');
        $fixture->setRelance_at('Value');
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
            'offre[reponse_at]' => 'Something New',
            'offre[lettre_motivation]' => 'Something New',
            'offre[type]' => 'Something New',
            'offre[relance_at]' => 'Something New',
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
        $fixture->setDateCandidature('Value');
        $fixture->setEntreprise('Value');
        $fixture->setLieu('Value');
        $fixture->setUrl('Value');
        $fixture->setContact('Value');
        $fixture->setReponse('Value');
        $fixture->setReponse_at('Value');
        $fixture->setLettre_motivation('Value');
        $fixture->setType('Value');
        $fixture->setRelance_at('Value');
        $fixture->setFreelance('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/offre/');
        self::assertSame(0, $this->offreRepository->count([]));
    }
}
