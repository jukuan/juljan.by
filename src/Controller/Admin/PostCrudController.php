<?php

namespace App\Controller\Admin;

use __\Post;
use App\EasyAdmin\Field\TranslationsSimpleField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
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
        yield TranslationsSimpleField::new('postTranslations', '123', [
            'title' => [
                'field_type' => TextType::class,
                'required'   => true,
            ],
//            'slug'  => [
//                'field_type' => SlugType::class,
//                'required'   => true,
//            ],
//            'body'  => [
//                'field_type' => TextEditorType::class,
//                'required'   => true,
//            ],
        ]);
    }
}
