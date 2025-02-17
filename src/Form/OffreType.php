<?php

namespace App\Form;

use App\Entity\Offre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class OffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('description', TextType::class, [
                'label' => 'Poste',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Poste'],
            ])
            ->add('dateCandidature', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de candidature',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('entreprise', TextType::class, [
                'label' => 'Nom de l\'entreprise',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Nom de l\'entreprise'],
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ville ou adresse'],
            ])
            ->add('url', UrlType::class, [
                'label' => 'Lien vers l\'offre',
                'attr' => ['class' => 'form-control', 'placeholder' => 'https://'],
            ])
            ->add('contact', TextareaType::class, [
                'label' => 'Contact',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Informatique' => 'Informatique',
                    'Autre' => 'Autre',
                ],
                'expanded' => true,
                'multiple' => false,
                'choice_attr' => function () {
                    return ['class' => 'form-check-input'];
                },
                'label_attr' => ['class' => 'form-check-label'],
                'row_attr' => ['class' => 'form-check form-check-inline'],
                'data' => 'Informatique',
                'label_html' => true,
                'choice_label' => function ($choice, $key, $value) {
                    return sprintf('<span class="form-option-text">%s</span>', $choice);
                }
            ])
            ->add('reponse', ChoiceType::class, [
                'label' => 'Réponse',
                'choices' => [
                    'en attente'    => 'ATTENTE',
                    'Oui' => 'OUI',
                    'Non' => 'NON',
                ],
                'expanded' => true,
                'data' => 'ATTENTE',
            ])
            ->add('reponse_at', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de réponse',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('lettre_motivation_checkbox', CheckboxType::class, [
                'label' => 'Lettre de motivation requise',
                'required' => false,
                'mapped' => false,
            ])

            ->add('relance_at', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de relance',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('freelance', CheckboxType::class, [
                'label' => 'Freelance ?',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ]);
        // Écouteurs d'événements pour gérer la transformation entre checkbox et valeur de chaîne
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $offre = $event->getData();
            $form = $event->getForm();

            // Si l'offre existe et que la lettre de motivation est "oui", cocher la case
            if ($offre && $offre->getLettreMotivation() === 'oui') {
                $form->get('lettre_motivation_checkbox')->setData(true);
            }
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $offre = $event->getData();

            // Convertir l'état de la checkbox en "oui" ou "non"
            $checked = $form->get('lettre_motivation_checkbox')->getData();
            $offre->setLettreMotivation($checked ? 'oui' : 'non');
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offre::class,
        ]);
    }
}
