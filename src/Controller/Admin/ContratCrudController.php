<?php

namespace App\Controller\Admin;

use App\Entity\Contrat;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\Response;

class ContratCrudController extends AbstractCrudController
{

    public const CONTRATS_BASE_PATH = 'upload/images/contrats';
    public const CONTRATS_UPLOAD_DIR ='public/upload/images/contrats';
    public const ACTION_DUPLICATE ='duplicate';

    public static function getEntityFqcn(): string
    {
        return Contrat::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $duplicate = Action::new(self::ACTION_DUPLICATE)
        ->linkToCrudAction('duplicateContrat')
        ->setCssClass('btn btn-info');

        return $actions
        ->add(Crud::PAGE_EDIT, $duplicate)
        ->reorder(Crud::PAGE_EDIT, [self::ACTION_DUPLICATE, Action::SAVE_AND_RETURN]);
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextEditorField::new('description'),
            MoneyField::new('price')->setCurrency('EUR'),

            ImageField::new('image')
            ->setBasePath(self::CONTRATS_BASE_PATH)
            ->setUploadDir(self::CONTRATS_UPLOAD_DIR)
            ->setSortable(false),

            BooleanField::new('active'),
            AssociationField::new('category'),
            DateTimeField::new('updatedAt')->hideOnForm(),
            DateTimeField::new('createdAt')->hideOnForm(),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
       if (!$entityInstance instanceof Contrat) return;
        
       $entityInstance->setCreatedAt(new \DateTimeImmutable);

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function duplicateContrat(
        AdminContext $context,
        AdminUrlGenerator $adminUrlGenerator,
        EntityManagerInterface $em
    ): Response {
        /** @var Contrat $contrat */
        $contrat = $context->getEntity()->getInstance();

        $duplicatedContrat = clone $contrat;

        parent::persistEntity($em, $duplicatedContrat);

        $url = $adminUrlGenerator->setController(self::class)
            ->setAction(Action::DETAIL)
            ->setEntityId($duplicatedContrat->getId())
            ->generateUrl();

        return $this->redirect($url);
    }
}
