<?php

namespace App\Repository;

use App\Entity\Todo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Todo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Todo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Todo[]    findAll()
 * @method Todo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $manager
    ) {
        parent::__construct($registry, Todo::class);
        $this->manager = $manager;
    }

    public function saveTodo($title, $detail)
    {
        $newTodo = new Todo();

        $newTodo
            ->setTitle($title)
            ->setDetails($detail);

        $this->manager->persist($newTodo);
        $this->manager->flush();
    }
    public function updateTodo(Todo $todo): Todo
    {
        $this->manager->persist($todo);
        $this->manager->flush();

        return $todo;
    }

    public function removeTodo(Todo $todo)
    {
        $this->manager->remove($todo);
        $this->manager->flush();
    }

    // /**
    //  * @return Todo[] Returns an array of Todo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Todo
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
