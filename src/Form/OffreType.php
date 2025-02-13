<?php

namespace App\Form;

use App\Entity\Offre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateCandidature', null, [
                'widget' => 'single_text',
            ])
            ->add('entreprise')
            ->add('lieu')
            ->add('url')
            ->add('contact')
            ->add('reponse')
            ->add('reponse_at', null, [
                'widget' => 'single_text',
            ])
            ->add('lettre_motivation')
            ->add('type')
            ->add('relance_at', null, [
                'widget' => 'single_text',
            ])
            ->add('freelance')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offre::class,
        ]);
    }
}
