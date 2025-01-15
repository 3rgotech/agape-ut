<?php

return [
    'generic' => [
        'back' => 'Retour',
    ],

    'legal'   => 'Mentions Légales',
    'contact' => [
        'title'                 => 'Contact',
        'project_call'          => 'Titre de l\'Appel à Projets',
        'project'               => 'Acronyme du projet',
        'message'               => 'Message',
        'send'                  => 'Envoyer le message',
        'contact_sent'          => 'Message envoyé avec succès !',
    ],

    'login' => [
        'title'       => 'Bienvenue sur Appel à Projets de l\'UT<br/>Veuillez vous connecter',
        'remember_me' => 'Se souvenir de moi',
        'login'       => 'Connexion',
        'register'    => 'Inscription',
    ],
    'register' => [
        'title'         => 'Créer un compte',
        'email_help'    => '',
        'password_help' => 'Minimum 10 caractères, dont au moins 1 majuscule et 1 chiffre',
        'login'         => 'Déjà inscrit·e ?',
        'register'      => 'Inscription',
    ],
    'forgot_password' => [
        'title'     => 'Mot de passe oublié ?',
        'text'      => 'Veuillez nous indiquer votre adresse e-mail et nous vous enverrons un lien de réinitialisation du mot de passe.',
        'send_link' => 'Envoyer le lien',
    ],
    'reset_password' => [
        'title' => 'Réinitialiser le mot de passe',
        'reset' => 'Enregistrer le nouveau mot de passe'
    ],

    'dashboard' => [
        'title'             => 'Tableau de bord',
        'planning'          => 'Calendrier',
        'view_project_call' => 'Voir l\'Appel à Projets',
        'candidate'         => [
            'open_calls'          => 'Appels à projets en cours',
            'no_open_calls'       => 'Aucun appel en cours',
            'past_calls'          => 'Appels à projets passés',
            'no_past_calls'       => 'Aucun appel passé',
            'create_application'  => 'Candidater',
            'edit_application'    => 'Modifier ma candidature',
            'view_application'    => 'Visualiser ma candidature',
            'correct_application' => 'Corriger ma candidature',
        ],
        'expert'         => [
            'open_offers'           => 'Propositions d\'évaluations',
            'pending_evaluations'   => 'Évaluations en cours',
            'past_evaluations'      => 'Évaluations passées',
            'accept'                => 'Accepter',
            'reject'                => 'Refuser',
            'reason'                => 'Raison du refus',
            'create_evaluation'     => 'Évaluer',
            'edit_evaluation'       => 'Modifier l\'évaluation',
            'view_evaluation'       => 'Visualiser l\'évaluation',
            'correct_evaluation'    => 'Corriger l\'évaluation',
            'evaluation_period'     => 'Période d\'évaluation',
            'application_by'        => ':title, par :applicant',
            'no_open_offer'         => 'Aucune proposition d\'évaluation',
            'no_pending_evaluation' => 'Aucune évaluation en cours',
            'no_past_evaluation'    => 'Aucune évaluation passée',
            'accept_modal_title'    => 'Accepter la proposition d\'évaluation ?',
            'accept_modal_text'     => 'En cliquant sur "Accepter", vous vous engagez à respecter la politique de confidentialité ci-dessous :',
        ]
    ],

    'view_project_call' => [
        'title' => 'Détails de l\'Appel à Projets',
    ],

    'apply' => [
        'title_create'  => 'Candidater à l\'Appel à Projets',
        'title_edit'    => 'Modifier ma candidature',
        'title_correct' => 'Corriger ma candidature',
        'help'          => 'Aide à la saisie',

        'sections'      => [
            'general'    => '1. Informations Générales',
            'scientific' => '2. Présentation scientifique du projet',
            'carriers'   => '3. Equipe porteuse du projet',
            'budget'     => '4. Budget/Financement du projet',
            'files'      => '5. Pièces complémentaires',
        ],

        'main_carrier_help'       => 'Vous ne pouvez avoir que 2 porteurs par équipe projet. Décochez-en un avant de pouvoir sélectionner celui-ci.',
        'add_carrier'             => 'Ajouter un membre de l\'équipe projet',
        'laboratories_partners'   => 'Laboratoires / Partenaires',
        'laboratories_help'       => 'Vous pouvez choisir et/ou saisir jusqu\'à :count laboratoires.<br/>Le premier laboratoire de la liste doit être le porteur du projet. Utilisez les boutons pour réordonner les choix.',
        'add_laboratory'          => 'Ajouter un Laboratoire',
        'create_laboratory'       => 'Créer un Laboratoire',
        'edit_laboratory'         => 'Modifier un Laboratoire existant',
        'add_keyword'             => 'Ajouter un mot-clé',
        'short_description_help'  => 'Vous devrez joindre une description détaillée dans le formulaire de candidature à attacher dans la section 4',
        'study_fields_help'       => 'Vous pouvez choisir et/ou saisir jusqu\'à :count éléments',
        'create_study_field'      => 'Créer un Champ Disciplinaire',
        'create_study_field_help' => 'Vous devez traduire l\'intitulé dans toutes les langues de l\'application',
        'amount_requested_help'   => 'Vous devrez joindre un budget précis et détaillé dans le formulaire financier à attacher dans la section 4',
        'other_fundings_help'     => '(ou autres soutiens financiers)',
        'add_laboratory_budget'   => 'Ajouter une structure',
        'other_attachments_help'  => ':count fichiers maximum',
        'download_template'       => 'Télécharger le modèle',
        'devalidated_title'       => 'Votre candidature a été dévalidée par le gestionnaire de la plateforme pour la raison suivante',
        'devalidated_help'        => 'Veuillez rectifier les points mentionnés ci-dessus avant de la soumettre à nouveau.',
        'submitted'               => 'Votre candidature a été soumise pour évaluation et ne peut plus être modifiée.',

        'back'                       => 'Retour',
        'save'                       => 'Enregistrer le brouillon',
        'submit'                     => 'Soumettre la candidature',
        'save_success'               => 'Votre brouillon de candidature a bien été enregistré.',
        'save_before_submitting'     => 'Vous devez sauvegarder vos modifications avant de pouvoir soumettre votre candidature.',
        'submit_confirmation_title'  => 'Valider la soumission de la candidature',
        'submit_confirmation_text'   => 'Veuillez vous assurer que votre candidature est complète avant de la soumettre. Cette action ne pourra pas être annulée.',
        'submit_confirmation_button' => 'Valider',
        'submit_error'               => 'Votre candidature n\'a pas été pu être soumise pour évaluation, car certains champs présentent des erreurs. Veuillez corriger ces erreurs avant de recommencer.',
        'submit_success'             => 'Votre candidature a bien été soumise pour évaluation.',
    ],

    'evaluate' => [
        'title_create'  => 'Évaluer la candidature',
        'title_edit'    => 'Modifier l\'évaluation',
        'title_correct' => 'Corriger l\'évaluation',
        'help'          => 'Aide à l\'évaluation',
        'use_tabs'      => 'Utilisez les onglets pour naviguer entre les différentes sections',

        'criteria_description' => '<strong>Description du critère</strong>',
        'grade'                => 'Note',
        'global_grade'         => 'Note globale',
        'comment'              => 'Commentaire',
        'global_comment'       => 'Commentaire global',

        'devalidated_title'       => 'Votre évaluation a été dévalidée par le gestionnaire de la plateforme pour la raison suivante',
        'devalidated_help'        => 'Veuillez rectifier les points mentionnés ci-dessus avant de la soumettre à nouveau.',
        'submitted'               => 'Votre évaluation a été soumise et ne peut plus être modifiée.',

        'back'                       => 'Retour',
        'save'                       => 'Enregistrer le brouillon',
        'submit'                     => 'Soumettre l\'évaluation',
        'save_success'               => 'Votre brouillon d\'évaluation a bien été enregistré.',
        'save_before_submitting'     => 'Vous devez sauvegarder vos modifications avant de pouvoir soumettre votre évaluation.',
        'submit_confirmation_title'  => 'Valider la soumission de l\'évaluation',
        'submit_confirmation_text'   => 'Veuillez vous assurer que votre évaluation est complète avant de la soumettre. Cette action ne pourra pas être annulée.',
        'submit_confirmation_button' => 'Valider',
        'submit_error'               => 'Votre évaluation n\'a pas été pu être soumise, car certains champs présentent des erreurs. Veuillez corriger ces erreurs avant de recommencer.',
        'submit_success'             => 'Votre évaluation a bien été soumise pour évaluation.',
    ]
];
