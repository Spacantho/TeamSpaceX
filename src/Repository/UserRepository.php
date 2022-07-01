<?php

namespace App\Repository;

use App\Entity\EmailConfirmation;
use App\Entity\Email;
use App\Entity\MotDePasse;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserLoaderInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private EmailRepository $emailRepository,
        private UserPasswordHasherInterface $userPasswordHasherInterface    
    )
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function creationEmailMdp(
        string $email, string $mdp): User {
        $emailEnt = $this->emailRepository->find($email);
        if($emailEnt === null) {
            $emailEnt = new Email();
            $emailEnt->setEmail($email);
        }
        $mailIndication = new EmailConfirmation();
        $user = new User();
        $user->addEmailConfirmation($mailIndication);
        $emailEnt->addEmailConfirmation($mailIndication);

        $password = $this->userPasswordHasher->hashPassword(
            $user,
            $mdp
        );
        $mot_de_passe = new MotDePasse();
        $mot_de_passe->setValeur($password);
        $user->addMotDePass(
            $mot_de_passe
        );

        return $user;
    }

    public function remove(User $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" n est pas supporté.', \get_class($user)));
        }
        $password = new MotDePasse();
        $password->setValeur($newHashedPassword);

        $user->addMotDePass($password);
        $this->_em->persist($user);
        $this->_em->flush();
    }

     /**
     * @throws NonUniqueResultException
     */
    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        // TODO: On voudrait sortir une liste de Users et laisser à l'utilisateur le choix.
        $userManager = $this->getEntityManager();

        $res = $userManager->createQuery(
            'SELECT u.id, u.uuid, u.roles, e.email
                FROM App\Entity\User u
                JOIN App\Entity\EmailConfirmation ec
                JOIN App\Entity\Email e
                WHERE e.email = :query and e = ec.email and ec.user = u
                '
        )
            ->setParameter('query', $identifier)
            ->getOneOrNullResult();
        if ($res === null) {
            $res = $userManager->createQuery(
                'SELECT u.id, u.uuid, u.roles, e.email
                FROM App\Entity\User u
                JOIN App\Entity\EmailIndication ei 
                JOIN App\Entity\Email e
                WHERE e.email = :query and e = ei.email and ei.user = u
                '
            )->setParameter('query', $identifier)
                ->getOneOrNullResult();
        }
        if($res === null) {
            return null;
        }

        return $this->find($res["id"]);
    }


//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
