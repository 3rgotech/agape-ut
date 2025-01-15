<?php

return [
    'generic' => [
        'back' => 'Back',
    ],

    'legal'   => 'Terms and Conditions',
    'contact' => [
        'title'                 => 'Contact',
        'project_call'          => 'Project Call Title',
        'project'               => 'Project Acronym',
        'message'               => 'Message',
        'send'                  => 'Send',
        'contact_sent'          => 'Message sent successfully!',
    ],

    'login' => [
        'title'       => 'Welcome to The UT Call for Projects Platform<br/>Please login',
        'remember_me' => 'Remember me',
        'login'       => 'Login',
        'register'    => 'Register',
    ],
    'register' => [
        'title'         => 'Create an account',
        'email_help'    => '',
        'password_help' => 'Minimum 10 characters, at least 1 uppercase and 1 number',
        'login'         => 'Already registered? ',
        'register'      => 'Register',
    ],
    'forgot_password' => [
        'title'     => 'Forgot your password?',
        'text'      => 'Please enter your email address and we will send you a link to reset your password.',
        'send_link' => 'Send link',
    ],
    'reset_password' => [
        'title' => 'Reset your password',
        'reset' => 'Save new password'
    ],

    'dashboard' => [
        'title'             => 'Dashboard',
        'planning'          => 'Calendar',
        'view_project_call' => 'View Project Call',
        'candidate'         => [
            'open_calls'          => 'Open Calls',
            'no_open_calls'       => 'No open calls',
            'past_calls'          => 'Past Calls',
            'no_past_calls'       => 'No past calls',
            'create_application'  => 'Apply',
            'edit_application'    => 'Edit Application',
            'view_application'    => 'View Application',
            'correct_application' => 'Correct Application',
        ],
        'expert'         => [
            'open_offers'           => 'Open Offers',
            'pending_evaluations'   => 'Pending Evaluations',
            'past_evaluations'      => 'Past Evaluations',
            'accept'                => 'Accept',
            'reject'                => 'Reject',
            'reason'                => 'Reason',
            'create_evaluation'     => 'Evaluate',
            'edit_evaluation'       => 'Edit Evaluation',
            'view_evaluation'       => 'View Evaluation',
            'correct_evaluation'    => 'Correct Evaluation',
            'evaluation_period'     => 'Evaluation Period',
            'application_by'        => ':title, by :applicant',
            'no_open_offer'         => 'No open offers',
            'no_pending_evaluation' => 'No pending evaluations',
            'no_past_evaluation'    => 'No past evaluations',
            'accept_modal_title'    => 'Accept the evaluation offer ?',
            'accept_modal_text'     => 'By clicking on "Accept", you agree to comply with the following privacy policy :',
        ]
    ],

    'view_project_call' => [
        'title' => 'Project Call Details',
    ],

    'apply' => [
        'title_create'  => 'Apply to Project Call',
        'title_edit'    => 'Edit Application',
        'title_correct' => 'Correct Application',
        'help'          => 'Application help',

        'sections'      => [
            'general'    => '1. General Information',
            'scientific' => '2. Scientific Project Overview',
            'carriers'   => '3. Project Team',
            'budget'     => '4. Budget/Funding Information',
            'files'      => '5. Additional Documents',
        ],

        'main_carrier_help'       => 'You can only have 2 main carriers per project team. Uncheck one to be able to select this one.',
        'add_carrier'             => 'Add a project team member',
        'laboratories_partners'   => 'Laboratories / Partners',
        'laboratories_help'       => 'You can choose and/or enter up to :count laboratories. The first laboratory on the list must be the lead of the project. Use the buttons to reorder your choices.',
        'add_laboratory'          => 'Add Laboratory',
        'create_laboratory'       => 'Create Laboratory',
        'edit_laboratory'         => 'Edit Existing Laboratory',
        'add_keyword'             => 'Add Keyword',
        'short_description_help'  => 'You will need to attach a detailed description in the application form in section 4',
        'study_fields_help'       => 'You can choose and/or enter up to :count items',
        'create_study_field'      => 'Create Study Field',
        'create_study_field_help' => 'You must translate the title into all application languages',
        'amount_requested_help'   => 'You will need to attach a detailed budget in the financial form and attach it to section 4',
        'other_fundings_help'     => '(or other financial support)',
        'add_laboratory_budget'   => 'Add a structure',
        'other_attachments_help'  => ':count maximum files',
        'download_template'       => 'Download Template',
        'devalidated_title'       => 'Your application was devalidated by the platform manager for the following reason',
        'devalidated_help'        => 'Please correct the points mentioned above before resubmitting.',
        'submitted'               => 'Your application has been submitted for evaluation and cannot be modified.',

        'back'                       => 'Back',
        'save'                       => 'Save Draft',
        'submit'                     => 'Submit Application',
        'save_success'               => 'Your draft application has been saved successfully.',
        'save_before_submitting'     => 'You must save your changes before submitting your application.',
        'submit_confirmation_title'  => 'Confirm Submission of Application',
        'submit_confirmation_text'   => 'Please ensure that your application is complete before submitting it. This action cannot be undone.',
        'submit_confirmation_button' => 'Confirm',
        'submit_error'               => 'Your application could not be submitted for evaluation because some fields have errors. Please correct these errors before trying again.',
        'submit_success'             => 'Your application has been submitted for evaluation.',
    ],

    'evaluate' => [
        'title_create'  => 'Evaluate Application',
        'title_edit'    => 'Edit Evaluation',
        'title_correct' => 'Correct Evaluation',
        'help'          => 'Evaluation help',
        'use_tabs'      => 'Use the tabs to navigate between the different sections',

        'criteria_description' => '<strong>Criterion Description</strong>',
        'grade'                => 'Grade',
        'global_grade'         => 'Overall Grade',
        'comment'              => 'Comment',
        'global_comment'       => 'Overall Comment',

        'devalidated_title'       => 'Your evaluation was devalidated by the platform manager for the following reason',
        'devalidated_help'        => 'Please correct the points mentioned above before resubmitting.',
        'submitted'               => 'Your evaluation has been submitted and cannot be modified.',

        'back'                       => 'Back',
        'save'                       => 'Save Draft',
        'submit'                     => 'Submit Evaluation',
        'save_success'               => 'Your draft evaluation has been saved successfully.',
        'save_before_submitting'     => 'You must save your changes before submitting your evaluation.',
        'submit_confirmation_title'  => 'Confirm Submission of Evaluation',
        'submit_confirmation_text'   => 'Please ensure that your evaluation is complete before submitting it. This action cannot be undone.',
        'submit_confirmation_button' => 'Confirm',
        'submit_error'               => 'Your evaluation could not be submitted because some fields have errors. Please correct these errors before trying again.',
        'submit_success'             => 'Your evaluation has been submitted for review.',
    ]
];
