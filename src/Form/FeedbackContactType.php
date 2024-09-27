<?php

namespace App\Form;

use App\Entity\Feedback;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FeedbackContactType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', HiddenType::class)
            ->add('name', null, [
                'label' => $this->translator->trans('form.name'),
            ])
            ->add('email', null, [
                'label' => $this->translator->trans('form.email'),
            ])
            ->add('text', null, [
                'label' => $this->translator->trans('form.text'),
            ])
//            ->add('browser')
//            ->add('ip_addr')
//            ->add('page')
            ->add('send', SubmitType::class, [
                'label' => $this->translator->trans('form.send')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Feedback::class,
        ]);
    }
}
