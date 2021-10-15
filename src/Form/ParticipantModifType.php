<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantModifType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            //->add('password',null,["mapped"=> false])
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('email')
            //->add('actif')
            //->add('estInscrit')
            ->add('campus',EntityType::class,[
                'class'=>Campus::class,
                'choice_label'=>'nom',
                'expanded'=>true
            ])
                ->add('image', FileType::class, [
                    'label' => 'Votre photo',
                    'mapped' => false,
                    'required' => false,])
            ;
        }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
