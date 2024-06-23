<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\Commentaire;
use function Symfony\Component\Clock\now;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class CommentaireCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commentaire::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            IdField::new('id')->hideOnForm(),
            TextField::new('contenu', 'Contenu'),
            AssociationField::new('publication', 'Publication')->hideOnForm(),
            IntegerField::new('likes','Likes'),
            AssociationField::new('auteur', 'Auteur')->hideOnForm(),
        ];

        if ($pageName === Crud::PAGE_NEW) {
            $fields[] = DateTimeField::new('created_at', 'Créé le');
        }
        
        if ($pageName === Crud::PAGE_EDIT) {
            $fields[] = DateTimeField::new('updated_at', 'Mis à jour le');
        }
        
        if ($pageName === Crud::PAGE_INDEX) {
            $fields[] = DateTimeField::new('created_at', 'Créé le');
            $fields[] = DateTimeField::new('updated_at', 'Mis à jour le');
        }

        if ($pageName === Action ::DETAIL) {
            $fields[] = DateTimeField::new('created_at', 'Créé le');
            $fields[] = DateTimeField::new('updated_at', 'Mis à jour le');
        }


        return $fields;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_DETAIL, 'Détails du commentaire')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modification du commentaire')
            ->setPageTitle(Crud::PAGE_NEW, 'Nouveau commentaire');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
