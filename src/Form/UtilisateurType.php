<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

    class UtilisateurType extends AbstractType {
        public function buildForm(FormBuilderInterface $builder, array $options): void {
            $builder
                ->add('Nom')
                ->add('Prenom')
                ->add('email')
                ->add('roles', ChoiceType::class, [
                    'choices' => [
                        'Administrateur' => 'ROLE_ADMIN',
                        'Éditeur' => 'ROLE_EDITOR',
                        'Utilisateur' => 'ROLE_USER',
                    ],
                    'multiple' => true,
                    'expanded' => true,
                ])
                ->add('password', PasswordType::class);
        }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
