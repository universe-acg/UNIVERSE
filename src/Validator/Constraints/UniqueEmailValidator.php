<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Validator\Constraints;
    
    use App\Entity\User;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\Validator\Constraint;
    use Symfony\Component\Validator\ConstraintValidator;
    
    class UniqueEmailValidator extends ConstraintValidator
    {
        private $entityManager;
        
        public function __construct(EntityManagerInterface $entityManager)
        {
            $this->entityManager = $entityManager;
        }
        
        public function validate($value, Constraint $constraint)
        {
            if (!$value) {
                return;
            }
            
            $existingUser = $this->entityManager
                ->getRepository(User::class)
                ->findOneBy(['email' => $value]);
            
            if ($existingUser) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
