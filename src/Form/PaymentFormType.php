<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Payment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PaymentFormType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('service', null, [
                'label' => $this->translator->trans('form.service', [], 'messages', $options['locale']),
            ])
            ->add('sum', null, [
                'label' => $this->translator->trans('form.sum', [], 'messages', $options['locale']),
            ])
            ->add('email', null, [
                'label' => $this->translator->trans('form.email', [], 'messages', $options['locale']),
            ])
            ->add('text', null, [
                'label' => $this->translator->trans('form.text', [], 'messages', $options['locale']),
            ])
            ->add('send', SubmitType::class, [
                'label' => $this->translator->trans('form.send', [], 'messages', $options['locale'])
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Payment::class,
            'locale' => 'ru', // Default locale, TODO
        ]);

        $resolver->setAllowedTypes('locale', 'string');
    }
}