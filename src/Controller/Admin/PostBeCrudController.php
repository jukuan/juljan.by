<?php

namespace App\Controller\Admin;

use App\EasyAdmin\Field\TranslationsSimpleField;
use App\Entity\PostBe;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PostBeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PostBe::class;
    }

    /*public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('type'),

//            TranslationField::new('postTranslations', 'posts', [
//                'title' => [
//                    'field_type' => TextType::class,
//                ],
//                'text' => [
//                    'field_type' => TextType::class,
//                ]
//            ])->setRequired(true)
//                ->hideOnIndex()

        ];
    }*/

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('type'),
            TextEditorField::new('body'),

//            TranslationField::new('postTranslations', 'posts', [
//                'title' => [
//                    'field_type' => TextType::class,
//                ],
//                'text' => [
//                    'field_type' => TextType::class,
//                ]
//            ])->setRequired(true)
//                ->hideOnIndex()

        ];
    }
}
