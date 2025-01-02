<?php

return [
    "greeting"         => "Hello,",
    "greeting_error"   => "Oops,",
    "regards"          => "Regards,",
    "signature"        => "The :app_name Team",
    "trouble_clicking" => "If you are unable to click the button \":actionText\", copy and paste the following link into your browser: [:actionURL](:actionURL)",

    "email_verification" => [
        "title"  => "Email verification",
        "intro"  => "Please click the button below to confirm your email address.",
        "action" => "Verify my email address",
        "outro"  => "If you did not create an account on this site, you can ignore this message."
    ],

    "reset_password" => [
        "title"  => "Password reset request",
        "intro"  => "You are receiving this email because a password reset request was made for your account.",
        "action" => "Reset my password",
        "outro"  => "If you did not make this request, you can ignore this message."
    ],

    "new_application_submitted" => [
        "title"             => "New application submitted",
        "intro"             => "The applicant :name has submitted a new application to the project call :call.",
        "devalidation_line" => "This application was devalidated for the reason: :justification",
        "action"            => "View the application"
    ],

    "application_submitted" => [
        "title"  => "Application confirmation",
        "intro"  => "Your application to the project call :call has been submitted under the reference :reference.",
        "action" => "View the application"
    ],

    "application_force_submitted" => [
        "title"  => "Application submission",
        "intro"  => "Your application to the project call :call has been manually submitted by the The UT Call for Projects platform administrator under the reference :reference.",
        "action" => "View the application"
    ],

    "application_unsubmitted" => [
        "title"  => "Application devalidation",
        "intro"  => "Your application to the project call :call (reference: :reference) has been devalidated by the UT Call for Projects platform administrator.",
        "outro"  => "Reason: :justification",
        "action" => "Edit the application"
    ],

    "offer_created" => [
        "title"  => "Offer of evaluation",
        "intro"  => "The UT Call for Projects platform administrator has offered to evaluate a new applicant for the AAP [AAP].",
        "action" => "Respond",
        "outro"  => "Thank you for letting us know your decision as soon as possible."
    ],

    "offer_created_invite" => [
        "title"  => "Invitation to join the The UT Call for Projects platform",
        "intro"  => "The The UT Call for Projects platform administrator invites you to join the platform as :role, to participate in the evaluation of a candidate on the project call :projectcall",
        "action" => "Sign up",
        "outro"  => "Thank you for letting us know your decision as soon as possible."
    ],

    "offer_accepted" => [
        "title" => "Evaluation offer accepted",
        "intro" => "The expert :expert has accepted the evaluation offer for the applicant :candidat for the project call :call"
    ],

    "offer_declined" => [
        "title" => "Evaluation offer declined",
        "intro" => "The expert :expert has declined the evaluation offer for the applicant :candidat for the project call :call",
        "outro" => "Reason for declining: :justification"
    ],

    "offer_retry" => [
        "title"  => "Reminder: Evaluation offer to process",
        "intro"  => "We remind you that the evaluation offer for the applicant :candidat for the project call :call is waiting to be processed.",
        "action" => "View the offer"
    ],

    "offer_retry_invite" => [
        "title"  => "Reminder: Invitation to join the The UT Call for Projects platform",
        "intro"  => "We remind you that the The UT Call for Projects platform administrator invited you to join the platform as :role, to participate in the evaluation of a candidate on the project call :projectcall",
        "action" => "Sign up",
    ],

    "evaluation_retry" => [
        "title"             => "Reminder : Evaluation to perform",
        "intro"             => "We remind you that your evaluation of the applicant :candidat for the project call :call is waiting to be performed. The submission deadline is :deadline. Thank you for your diligence",
        "action"            => "View the evaluation",
    ],

    "new_evaluation_submitted" => [
        "title"             => "New evaluation submitted",
        "intro"             => "The expert :name has submitted a new evaluation for the project call :call.",
        "devalidation_line" => "This evaluation was devalidated for the reason: :justification",
        "action"            => "View the evaluation"
    ],

    "evaluation_submitted" => [
        "title"             => "Evaluation confirmation",
        "intro"             => "Your evaluation of the applicant :candidat for the project call :call has been submitted. Thank you for your contribution."
    ],

    "evaluation_force_submitted" => [
        "title" => "Submission of your Evaluation",
        "intro" => "Your evaluation of the applicant :candidat for the project call :call has been manually submitted by the The UT Call for Projects platform administrator."
    ],

    "evaluation_unsubmitted" => [
        "title"  => "Evaluation devalidation",
        "intro"  => "Your evaluation of the applicant :candidat for the project call :call has been devalidated by the The UT Call for Projects platform administrator.",
        "outro"  => "Reason: :justification",
        "action" => "Edit the evaluation"
    ],

    "invitation" => [
        "title"  => "Invitation to join the The UT Call for Projects platform",
        "intro"  => "The The UT Call for Projects platform administrator invites you to join the platform as :role",
        "action" => "Sign up"
    ],

    "invitation_signup" => [
        "title"  => "Sign up after invitation",
        "intro"  => "The user :user, invited as :role, has confirmed their sign up",
        "outro"  => "Note: The invitation was sent to a different email address (:email)."
    ],

    "invitation_retry" => [
        "title"  => "Reminder: Invitation to join the The UT Call for Projects platform",
        "intro"  => "The The UT Call for Projects platform administrator invites you to join the platform as :role",
        "action" => "Sign up"
    ],

    "role_change" => [
        "title"  => "Change of access rights",
        "intro"  => "The The UT Call for Projects platform administrator has changed your access rights. You now have the role :role."
    ],

    "contact" => [
        "title"        => "New message via the The UT Call for Projects contact form",
        "intro"        => ":type :name (:oversight_affiliation ; :email) sent the following message through the The UT Call for Projects contact form: <br/><br/><blockquote>:message</blockquote>",
        "type_visitor" => "The visitor",
        "type_user"    => "The user",
        "action"       => "Respond"
    ]
];
