<?php

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use App\Entity\Offre;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthTest extends WebTestCase
{

    private PDO $pdo;
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $offreRepository;
    private string $path = '/admin/';
    public function setUp(): void
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

    public function testLogout(): void
    {
        // 🔹 Récupérer l'utilisateur
        $userRepo = $this->getContainer()->get("doctrine")->getRepository(User::class);
        $user = $userRepo->findOneBy(['email' => 'test@test.fr']);
        $this->assertNotNull($user, "L'utilisateur admin doit exister avant de tester la déconnexion.");

        // 🔹 Se connecter
        $this->client->loginUser($user);

        // 🔹 Vérifier que l'utilisateur est bien connecté (accès à une page admin)
        $this->client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();

        // 🔹 Se déconnecter
        $this->client->request('GET', '/logout');

        // 🔹 Vérifier que l'utilisateur est redirigé vers /login après déconnexion
        $this->assertResponseRedirects('/'); // Vérifie que la redirection est vers /login

        // 🔹 Suivre la redirection vers la page de login
        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion'); // Vérifie que la page de login s'affiche
    }


    public function testIsNotLogged()
    {
        $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }
    public function testLogAsAdmin(): void
    {
        $userRepo = $this->getContainer()->get("doctrine")->getRepository(User::class);
        $user = $userRepo->findOneByEmail('test@test.fr');
        $this->client->loginUser($user);

        $this->client->request('GET', '/');
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Dashboard');
    }
}
