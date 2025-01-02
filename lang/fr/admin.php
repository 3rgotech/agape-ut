<?php

return [
    'sections' => [
        'projectcalls' => 'Appels à Projets',
        'data'         => 'Référentiels',
        'admin'        => 'Administration',
    ],

    'dashboard' => [
        'title' => 'Tableau de bord',
    ],

    'roles' => [
        'administrator' => 'Administrateur',
        'manager'       => 'Gestionnaire',
        'applicant'     => 'Candidat',
        'expert'        => 'Expert',
    ],

    'users' => [
        'impersonate'                => 'Usurper',
        'blocked_filter'             => 'Etat de blocage',
        'all'                        => 'Tous',
        'unblocked'                  => 'Non Bloqués',
        'blocked'                    => 'Bloqués',
        'block'                      => 'Bloquer',
        'unblock'                    => 'Débloquer',
        'invite_user'                => 'Inviter un utilisateur',
        'invitation_language'        => 'Langue de l\'invitation',
        'invitation_language_all'    => 'Toutes',
        'invitation_duplicate_email' => 'L\'email saisi est déjà présent dans la base de données des utilisateurs ou des invitations.',
        'invitation_error'           => 'Une erreur est survenue lors de l\'envoi de l\'invitation :',
        'invitation_success'         => 'L\'invitation a été envoyée avec succès.',
    ],

    'invitations' => [
        'invitations_title' => 'Invitations en attente',
        'last_mail'         => 'Date de dernier envoi',
        'retry_count'       => 'Nombre de relances',
        'retry'             => 'Renvoyer',
        'cancel'            => 'Annuler',
    ],

    'translatable_fields' => [
        'title'       => 'Champs multilingues',
        'description' => 'Les valeurs affichées pour ces champs seront celles dans la langue choisie par l\'utilisateur.<br/>Attention de bien saisir les valeurs pour toutes les langues configurées dans l\'application !',
    ],

    'archived_records' => [
        'label' => 'Enregistrements archivés',
        'with'  => 'Non Archivés uniquement',
        'only'  => 'Archivés uniquement',
        'all'   => 'Tous',
    ],

    'public'                          => 'Public',
    'make_public'                     => 'Rendre public',
    'private'                         => 'Privé',
    'make_private'                    => 'Rendre privé',
    'preview_application_form'        => 'Prévisualiser la candidature',
    'zip_export'                      => 'Export ZIP',
    'pdf_export'                      => 'Export PDF',
    'pdf_export_anonymous'            => 'Export PDF Anonyme',
    'application_excel_export'        => 'Export Excel des Candidatures',
    'evaluation_pdf_export'           => 'Export PDF des Évaluations',
    'evaluation_pdf_export_anonymous' => 'Export PDF Anonyme des Évaluations',
    'close'                           => 'Fermer',
    'archive'                         => 'Archiver',
    'archived'                        => 'Archivé',

    'dynamic_attributes' => [
        'create'           => 'Ajouter un Champ Dynamique',
        'after_field_help' => 'Laisser vide pour ajouter le champ à la fin de la section',
        'add_option'       => 'Ajouter une option',
        'add_choice'       => 'Ajouter un choix',
    ],

    'dates'                    => 'Calendrier',
    'notation_description'     => 'Les critères seront présentés aux experts dans l\'ordre indiqué.',
    'files'                    => 'Modèles de fichiers',
    'files_description'        => 'Ces modèles seront proposés au téléchargement aux candidats. Si un modèle n\'est pas fourni, le champ fichier correspondant ne sera pas inclus dans le formulaire de candidature.',
    'default_number_help'      => 'Si cette valeur est mise à zéro, le champ correspondant du formulaire de candidature sera caché.',
    'never'                    => 'Jamais',
    'unsubmit'                 => 'Dévalider',
    'force_submit'             => 'Soumettre manuellement',
    'force_submit_error'       => 'La soumission n\'a pas pu être effectuée car la validation a échoué. Veuillez d\'abord rectifier les erreurs via la page de modification.',
    'force_submit_error_title' => 'Erreurs lors de la soumission',
    'force_submit_error_body'  => 'La soumission n\'a pas pu être effectuée car la validation a échoué avec les erreurs suivantes :',
    'submission_status'        => [
        'draft'       => 'Brouillon',
        'submitted'   => 'Soumise',
        'devalidated' => 'Dévalidée',
    ],

    'project_call_type' => [
        'lab_budget_help' => 'Si activé, les candidats devront saisir les budgets des laboratoires porteurs de la candidature (montant total, dépenses RH, dépenses de fonctionnement et dépenses d\'investissement.',
    ],

    'application' => [
        'offers'                       => 'Experts (:count)',
        'evaluations'                  => 'Évaluations (:count)',
        'add_expert'                   => 'Ajouter un expert',
        'existing_expert'              => 'Expert déjà enregistré',
        'new_expert'                   => 'Email de l\'expert à inviter',
        'add_selection_comity_opinion' => 'Comité de Sélection',
        'export_name'                  => 'Formulaire de Candidature',
    ],

    'evaluation_offer' => [
        'list_title'        => 'Offres d\'évaluation pour la candidature :application',
        'rejection_title'   => 'Offre d\'évaluation refusée pour la raison suivante :',
        'status'            => [
            'accepted' => 'Acceptée',
            'rejected' => 'Refusée',
            'pending'  => 'En attente',
        ],
        'retries'     => 'Relances',
        'retry'       => 'Envoyer un rappel',
        'cancel'      => 'Annuler',
        'override'    => 'Réinitialiser le choix',
        'show_reason' => 'Raison du refus',

        'success_sent'             => 'Une proposition d\'évaluation vient d\'être envoyée à l\'expert choisi.',
        'success_invited'          => 'Une invitation à rejoindre la plateforme vient d\'être envoyée à l\'expert choisi.',
        'success_linked'           => 'L\'expert choisi a déjà été invité mais n\'a pas encore rejoint la plateforme. La proposition d\'évaluation a été liée à son invitation et une notification lui a été envoyée.',
        'error_no_expert_or_email' => 'Vous devez choisir un expert ou saisir une adresse e-mail pour envoyer une invitation.',
    ],

    'evaluation' => [
        'list_title' => 'Évaluations pour la candidature :application',
        'show_title' => 'Évaluation de la candidature :application par :expert',
        'status'     => [
            'accepted' => 'Acceptée',
            'rejected' => 'Refusée',
            'pending'  => 'En attente',
        ],
        'grades'      => 'Notes',
        'retries'     => 'Relances',
        'retry'       => 'Envoyer une relance',
        'cancel'      => 'Annuler',
        'show_reason' => 'Raison du refus',
        'export_name' => 'Grille d\'Évaluation',
    ],

    'settings' => [
        'title'    => 'Paramètres',

        'sections' => [
            'projectCalls'   => 'Appels à projets',
            'defaultNumbers' => 'Nombre par défaut de ...',
            'evaluation'     => 'Évaluation',
        ],

        'fields' => [
            'defaultNumberOfWorkshopDates' => 'Dates prévisionnelles (Workshop)',
            'defaultNumberOfExperts'       => 'Experts',
            'defaultNumberOfDocuments'     => 'Documents joints',
            'defaultNumberOfLaboratories'  => 'Laboratoires',
            'defaultNumberOfStudyFields'   => 'Champs disciplinaires',
            'defaultNumberOfKeywords'      => 'Mots-clés',
            'enableBudgetIncomeOutcome'    => 'Champs de budget prévisionnel total (recettes & dépenses)',
            'forbiddenDomains'             => 'Domaines d\'email interdits pour les inscriptions',
            'applicationForm'              => 'Formulaire de Candidature',
            'financialForm'                => 'Formulaire Financier',
            'additionalInformation'        => 'Informations complémentaires',
            'otherAttachments'             => 'Autres Fichiers',
            'enable'                       => 'Activer ?',
            'extensions'                   => 'Extensions Autorisées',
            'grades'                       => 'Notes',
            'gradeGrade'                   => 'Note',
            'gradeLabel'                   => 'Libellé (traduit)',
            'notation'                     => 'Critères de notation',
            'notationTitle'                => 'Intitulé du critère (traduit)',
            'notationDescription'          => 'Description du critère (traduit)',
        ],

        'description' => [
            'grades'   => 'La première note est la plus basse, la dernière la plus haute',
            'notation' => 'Ces critères de notation sont les valeurs par défaut de chaque Appel à Projet. Ils seront présentés aux experts dans l\'ordre indiqué.'
        ],
        'actions' => [
            'addGrade'    => 'Ajouter une note',
            'addNotation' => 'Ajouter un critère',
        ],
    ]
];
