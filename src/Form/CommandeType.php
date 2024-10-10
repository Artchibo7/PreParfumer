<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'attr' => ['class' => 'form form-control']
            ])
            ->add('prenom', null, [
                'attr' => ['class' => 'form form-control']
            ])
            ->add('email', null, [
                'attr' => ['class' => 'form form-control']
            ])
            ->add('telephone', null, [
                'attr' => ['class' => 'form form-control']
            ])
            ->add('adresse', null, [
                'attr' => ['class' => 'form form-control']
            ])
            // ->add('createdAt', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                    'attr' => ['class' => 'form form-control']
            ])
            ->add('payOnDelivery', null, [
                'label' => 'Payer à la livraison',
            ]);
            // ->add('payOnPlace', null, [
            //     'label'    => 'Payer sur place lors du retrait',
            // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
