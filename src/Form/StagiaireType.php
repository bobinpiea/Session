<?php

// FORMULAIRE STAGIAIRE (StagiaireType)

// Ce fichier permet de construire automatiquement le formulaire lié à l'entité
// Stagiaire. Il contient la liste des champs à afficher
// (nom, prénom, email, etc.) et la configuration de chaque champ (type, label,
// options, lien avec d'autres entités comme Session).
// Symfony utilisera ce fichier pour générer le HTML du formulaire, valider
// les données, et hydrater automatiquement un objet Stagiaire.

// Ce fichier est appelé depuis le contrôleur pour créer ou modifier un stagiaire.



namespace App\Form;

use App\Entity\Session;
use App\Entity\Stagiaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class StagiaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse'
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville'
            ])
            ->add('cp', TextType::class, [
                'label' => 'Code postal'
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail'
            ])
            ->add('dateNaissance', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text', 
            ])
            ->add('sessions', EntityType::class, [
                'class' => Session::class,
                'choice_label' => function(Session $session) {
                    return $session->getNom(); //  on affiche le nom de la session
                },
                'label' => 'Sessions inscrites',
                'multiple' => true, // on peut s'inscrire à plusieurs sessions
                'expanded' => true 
            ])
            ->add('Valider', SubmitType::class, [
                'label' => 'Enregistrer le stagiaire'
            ]);
    }





    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stagiaire::class,
        ]);
    }
}
