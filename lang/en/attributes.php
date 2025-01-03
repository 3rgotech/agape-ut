<?php

return [

    // Project Call Type
    'lab_budget' => 'Ask for the budget of the laboratories carrying the application ?',

    // Project Call
    'project_call_type'      => 'Project Call Type',
    'year'                   => 'Year',
    'application_start_date' => 'Application Start Date',
    'application_end_date'   => 'Application End Date',
    'evaluation_start_date'  => 'Evaluation Start Date',
    'evaluation_end_date'    => 'Evaluation End Date',
    'privacy_clause'         => 'Privacy Clause',
    'invite_email'           => 'Invite Email',
    'help_experts'           => 'Online Expert Help',
    'help_candidates'        => 'Online Candidate Help',
    'notation'               => 'Evaluation Guide',
    'number_of_documents'    => 'Maximum Number of Files in "Other Attachments" Field',
    'number_of_laboratories' => 'Maximum Number of Laboratories',
    'number_of_study_fields' => 'Maximum Number of Study Fields',
    'number_of_keywords'     => 'Maximum Number of Keywords',
    'accepted_extensions'    => 'Accepted Extensions: :extensions',
    'file_size_limit'        => 'Loaded file cannot exceed :size MB',
    'files'                  => [
        'applicationForm'        => 'Application Form',
        'financialForm'          => 'Financial Form',
        'additionalInformation'  => 'Additional Information',
        'otherAttachments'       => 'Other Attachments',
    ],
    'project_call_status' => [
        'planned'                => 'Planned',
        'application'            => 'Application',
        'waiting_for_evaluation' => 'Waiting for Evaluation',
        'evaluation'             => 'Evaluation',
        'waiting_for_decision'   => 'Waiting for Decision',
        'finished'               => 'Finished',
        'archived'               => 'Archived',
    ],

    // Application
    'acronym'                   => 'Acronym',
    'short_description'         => 'Short Description',
    'summary_fr'                => 'Public Summary (FR)',
    'summary_en'                => 'Public Summary (EN)',
    'summary_help'              => 'Destined for wide distribution on TIRIS - Université de Toulouse media',
    'carrier'                   => 'Project Carrier',
    'carrier_status'            => 'Status',
    'contact_name'              => 'Contact Name',
    'main_laboratory'           => 'Main Laboratories',
    'other_laboratories'        => 'Other Laboratories and Partners',
    'keywords'                  => 'Keywords',
    'amount_requested'          => 'Amount Requested',
    'other_fundings'            => 'Total of Co-Fundings',
    'total_expected_income'     => 'Total Expected Income',
    'total_expected_outcome'    => 'Total Expected Outcome',
    'laboratory_budget'         => 'Laboratory(s) to be credited',
    'managing_structure_is_lab' => 'Is the managing structure a laboratory ?',
    'total_amount'              => 'Total Amount',
    'hr_expenses'               => 'HR Expenses',
    'operating_expenses'        => 'Operating Expenses',
    'investment_expenses'       => 'Investment Expenses',
    'submitted_at'              => 'Submission Date',
    'devalidation_message'      => 'Devalidation Message',
    'selection_comity_opinion'  => 'Selection Comity Opinion',

    // Project Call Type
    'label_long'         => 'Long Label',
    'label_short'        => 'Short Label',
    'dynamic_attributes' => [
        'title'       => 'Dynamic Properties',
        'label'       => 'Field Label (Translated)',
        'location'    => 'Location',
        'section'     => 'Section',
        'after_field' => 'After Field',
        'type'        => 'Field Type',
        'types'       => [
            'text'     => 'Short Text',
            'date'     => 'Date',
            'richtext' => 'Text Area with Formatting',
            'textarea' => 'Text Area',
            'checkbox' => 'Checkboxes',
            'select'   => 'Dropdown',
        ],
        'options'            => 'Options',
        'option_label'       => 'Option (Translated)',
        'option_multiple'    => 'Multiple Choices?',
        'choices'            => 'Choices',
        'choice_label'       => 'Choice (Translated)',
        'choice_description' => 'Description (Translated)',
        'rules'              => 'Validation Rules',
        'required'           => 'Required Field?',
        'min_value'          => 'Minimum Value',
        'max_value'          => 'Maximum Value',
        'repeatable_field'   => 'Repeatable Field',
        'repeatable'         => 'Multiple Values?',
        'min_items'          => 'Minimum Number of Items',
        'max_items'          => 'Maximum Number of Items',
    ],

    // Laboratory
    'unit_code'      => 'Pôle de recherche UT',
    'director_email' => 'Director Email',
    'regency'        => 'Laboratory Regencies (CNRS, University or Other)',

    // Carrier
    'main_carrier'            => 'Project Carrier ?',
    'linked_to_laboratory'    => 'Linked to a Laboratory ?',
    'job_title'               => 'Job Title',
    'job_title_other'         => 'Job Title (other)',
    'organization'            => 'Organization Name',
    'organization_type'       => 'Organization Type',
    'organization_type_other' => 'Organization Type (other)',

    // User
    'first_name'            => 'First Name',
    'last_name'             => 'Last Name',
    'email'                 => 'Email Address',
    'phone'                 => 'Phone',
    'role'                  => 'Role',
    'email_verified'        => 'Email Verified?',
    'last_active_at'        => 'Last Active',
    'managed_types'         => 'Managed Call Types',
    'password'              => 'Password',
    'password_confirmation' => 'Confirm Password',

    // Generic
    'name'        => 'Name',
    'title'       => 'Title',
    'description' => 'Description',
    'status'      => 'Status',
    'reference'   => 'Reference',
    'owner'       => 'Owner',
    'creator'     => 'Creator',
    'created_at'  => 'Created At',
    'updated_at'  => 'Updated At',
];
