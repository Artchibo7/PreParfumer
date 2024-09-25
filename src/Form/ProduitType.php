<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\SousCategorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProduitType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('Nom')
            ->add('Description')
            ->add('Prix')
            ->add('stock')
            ->add('image',
            FileType::class,
                [
                    'label' => 'image du produit',
                    // 'multiple' => true,
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'image/jpg',
                                'image/png',
                                'image/jpeg',
                            ],
                            'maxSizeMessage' => 'Le fichier est trop volumineux, la taille doit être inférieure à 1024ko',
                            'mimeTypesMessage' => 'Votre image doit être au format jpg, png ou jpeg',
                        ])
                    ]
                ]
            )
            ->add('SousCategories', EntityType::class, [
                'class' => SousCategorie::class,
                'choice_label' => 'Nom',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
