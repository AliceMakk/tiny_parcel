<?php

namespace App\Repository;

use App\Entity\Parcel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Parcel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parcel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parcel[]    findAll()
 * @method Parcel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParcelRepository extends ServiceEntityRepository
{
    private $em;
    private $validator;

    public function __construct(ValidatorInterface $validator, EntityManagerInterface $manager, ManagerRegistry $registry)
    {
        $this->em = $manager;
        $this->validator = $validator;
        parent::__construct($registry, Parcel::class);
    }

    public function saveParcel(array $data)
    {
        $newParcel = new Parcel();

        $newParcel
            ->setName($data['name'])
            ->setWeight($data['weight'])
            ->setVolume($data['volume'])
            ->setDeclaredValue($data['declared_value'])
            ->setQuote($data['maxPrice'])
            ->setRate($data['rateEntity']);

        $errors = $this->validator->validate($newParcel);
        if (count($errors) > 0) {
            throw new \Exception((string) $errors);
        }

        $this->em->persist($newParcel);
        $this->em->flush();

        return $newParcel->getId();
    }

    public function updateParcel(Parcel $parcel, array $data)
    {
        $parcel
            ->setName($data['name'])
            ->setWeight($data['weight'])
            ->setVolume($data['volume'])
            ->setDeclaredValue($data['declared_value'])
            ->setQuote($data['maxPrice'])
            ->setRate($data['rateEntity']);

        $errors = $this->validator->validate($parcel);
        if (count($errors) > 0) {
            throw new \Exception((string) $errors);
        }

        $this->em->persist($parcel);
        $this->em->flush();

        return $parcel->getId();
    }

    public function deleteParcel($id) {
        $parcel = $this->findOneBy(['id' => $id]);
        if (!$parcel instanceof Parcel) {
            throw new \Exception('Parcel for deletion was not found');
        }
        $this->em->remove($parcel);
        $this->em->flush();
    }
}
