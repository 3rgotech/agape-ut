<?php

return [
    "greeting"         => "Bonjour,",
    "greeting_error"   => "Oups,",
    "regards"          => "Cordialement,",
    "signature"        => "L'équipe :app_name",
    "trouble_clicking" => "Si vous n'arrivez pas à cliquer sur le bouton \":actionText\", copiez et coller l'adresse suivante dans votre navigateur: [:actionURL](:actionURL)",

    "email_verification" => [
        "title"  => "Vérification de votre adresse email",
        "intro"  => "Veuillez cliquer sur le bouton ci-dessous pour confirmer votre adresse email.",
        "action" => "Vérifier mon adresse email",
        "outro"  => "Si vous n'avez pas créé de compte sur ce site, vous pouvez ignorer ce message."
    ],

    "reset_password" => [
        "title"  => "Demande de réinitialisation de mot de passe",
        "intro"  => "Vous recevez ce message car une réinitialisation de mot de passe a été demandée pour votre compte.",
        "action" => "Réinitialiser mon mot de passe",
        "outro"  => "Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer ce message.",
    ],

    "new_application_submitted" => [
        "title"             => "Ajout d'une nouvelle candidature",
        "intro"             => "Le candidat :name vient de soumettre sa candidature à l'appel à projets :call.",
        "devalidation_line" => "Cette candidature avait été dévalidée pour le motif suivant : :justification",
        "action"            => "Visualiser la candidature",
    ],

    "application_submitted" => [
        "title"  => "Confirmation de candidature",
        "intro"  => "Votre candidature à l'appel à projets :call a bien été soumise sous la référence :reference.",
        "action" => "Visualiser la candidature",
    ],

    "application_force_submitted" => [
        "title"  => "Soumission de votre candidature",
        "intro"  => "Votre candidature à l'appel à projets :call a été soumise manuellement par l'Administrateur de la plateforme AGAPE sous la référence :reference.",
        "action" => "Visualiser la candidature",
    ],

    "application_unsubmitted" => [
        "title"  => "Dévalidation de votre candidature",
        "intro"  => "Votre candidature à l'appel à projets :call (référence :reference) a été dévalidée par l'Administrateur de la plateforme AGAPE.",
        "outro"  => "Justification : :justification",
        "action" => "Modifier la candidature",
    ],

    "offer_created" => [
        "title"  => "Proposition d'évaluation",
        "intro"  => "L'administrateur de la plateforme AGAPE vous a proposé d'évaluer une nouvelle candidature pour l'AAP [AAP].",
        "action" => "Répondre",
        "outro"  => "Merci de nous faire connaître votre choix dans les plus brefs délais.",
    ],

    "offer_accepted" => [
        "title" => "Proposition d'évaluation acceptée",
        "intro" => "L'expert :expert vient d'accepter la proposition d'évaluation de la candidature de :candidat pour l'appel à projets :call"
    ],

    "offer_declined" => [
        "title" => "Proposition d'évaluation refusée",
        "intro" => "L'expert :expert vient de refuser la proposition d'évaluation de la candidature de :candidat pour l'appel à projets :call",
        "outro" => "Justification du refus : :justification"
    ],

    "offer_retry" => [
        "title"  => "Rappel : Proposition d'évaluation à traiter",
        "intro"  => "Nous vous rappelons que la proposition d'évaluation de la candidature de :candidat pour l'appel à projets :call est en attente de traitement.",
        "action" => "Voir la proposition"
    ],

    "new_evaluation_submitted" => [
        "title"             => "Envoi d'une nouvelle évaluation",
        "intro"             => "L'expert :name vient de soumettre son évaluation pour l'appel à projets :call.",
        "devalidation_line" => "Cette evaluation avait été dévalidée pour le motif suivant : :justification",
        "action"            => "Visualiser l'évaluation",
    ],

    "evaluation_submitted" => [
        "title"             => "Confirmation d'évaluation",
        "intro"             => "Votre évaluation de la candidature de :candidat pour l'appel à projets :call a bien été envoyée. Merci pour votre contribution",
    ],

    "evaluation_force_submitted" => [
        "title" => "Soumission de votre Évaluation",
        "intro" => "Votre évaluation de la candidature de :candidat pour l'appel à projets :call a été soumise manuellement par l'Administrateur de la plateforme AGAPE."
    ],

    "evaluation_unsubmitted" => [
        "title"  => "Dévalidation de votre évaluation",
        "intro"  => "Votre évaluation de la candidature de :candidat pour l'appel à projets :call a été dévalidée par l'Administrateur de la plateforme AGAPE.",
        "outro"  => "Justification : :justification",
        "action" => "Modifier l'évaluation",
    ],

    "invitation" => [
        "title"  => "Invitation à rejoindre la plateforme AGAPE",
        "intro"  => "L'administrateur de la plateforme AGAPE vous invite à rejoindre l'application en tant que :role",
        "action" => "Inscription"
    ],

    "invitation_signup" => [
        "title"  => "Inscription suite à invitation",
        "intro"  => "L'utilisateur :user, invité en tant que :role, a confirmé son inscription",
        "outro"  => "Attention : L'invitation avait été envoyée sur une adresse différente (:email)."
    ],

    "invitation_retry" => [
        "title"  => "Rappel : Invitation à rejoindre la plateforme AGAPE",
        "intro"  => "L'administrateur de la plateforme AGAPE vous invite à rejoindre l'application en tant que :role",
        "action" => "Inscription"
    ],

    "invitation_offer" => [
        "title"  => "Invitation à rejoindre la plateforme AGAPE",
        "intro"  => "L'administrateur de la plateforme AGAPE vous invite à rejoindre l'application en tant que :role, afin de participer à l'évaluation d'une candidature sur l'appel à projets :projectcall",
        "action" => "Inscription",
        "outro"  => "Merci de nous faire connaître votre choix dans les plus brefs délais."
    ],

    "role_change" => [
        "title"  => "Modification de vos droits d'accès",
        "intro"  => "L'administrateur de la plateforme AGAPE a modifié vos droits d'accès. Vous possédez désormais le rôle :role."
    ],

    "contact" => [
        "title"        => "Nouveau message via le formulaire de contact AGAPE",
        "intro"        => ":type :name (:oversight_affiliation ; :email) vient d'envoyer le message suivant avec le formulaire de contact de la plateforme AGAPE: <br/><br/><blockquote>:message</blockquote>",
        "type_visitor" => "Le visiteur",
        "type_user"    => "L'utilisateur",
        "action"       => "Répondre"
    ]
];
