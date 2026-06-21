<?php

return [
    'error' => [
        'fallback' => 'I am still here. Could you rephrase that so I can help you better?',
        'fallback_complaint' => 'I am sorry, something went wrong for a moment. I still have your complaint progress and we can continue.',
    ],

    'complaint' => [
        'check_in' => 'It sounds like this may not be going smoothly. If you want, I can help route this for follow-up.',
        'awaiting_issue' => 'I am sorry this has been frustrating. Please tell me what went wrong so our team can follow up.',
        'awaiting_phone' => 'Thanks, I recorded the issue. Please send an Egyptian mobile number so the team can follow up.',
        'invalid_phone_retry' => 'That phone number does not look valid. Please send an Egyptian mobile number like 01XXXXXXXXX, or say no if you prefer not to share one.',
        'saved' => 'Thanks, I saved your complaint for follow-up.',
        'declined' => 'Thanks, I saved your complaint without a phone number.',
        'default' => 'I am sorry this has been frustrating. Please tell me what went wrong.',
        'frustration' => 'I am sorry this has been frustrating. Please describe the issue and our team can follow up.',
    ],

    'installment' => 'Installments are not supported right now. Would you like to continue with cash listings?',

    'show_more' => [
        'exhausted' => 'Those are all the retained listings I have right now. If you want more, change the budget or preferences.',
        'no_results' => 'Please share the property type, location, and budget first so I can search.',
        'here' => 'Here are more listings from the current search.',
    ],

    'photos' => [
        'available' => 'Here are the available photos for that property.',
        'unavailable' => 'I do not have photos for that property right now. I can still help with the available listing details.',
    ],

    'seller_contact' => [
        'available' => 'The seller phone for that property is :phone.',
        'unavailable' => 'Seller contact is not currently available for that property. I can still help with the listing details.',
    ],

    'property_detail' => 'For :title, :facts. :missing Would you like to see photos?',
    'property_detail_no_facts' => 'For :title, I only have limited details available. :missing Would you like to see photos?',
    'property_detail_missing' => 'Some requested information is not available.',

    'resolution_clarification' => 'Which :label do you mean? :candidates',
    'resolution_clarification_simple' => 'Could you clarify the :label so I can continue?',
    'slot_clarification' => 'Could you clarify the :slotName so I can continue?',

    'property_reference' => [
        'unresolved' => 'Which property do you mean? Please choose from the numbered properties currently shown.',
        'clarification' => ':clarification_prompt',
    ],

    'chitchat' => 'How can I help with your property search?',
    'unclear' => 'Could you clarify whether you want to search for a property or ask about one already shown?',

    'search' => [
        'results' => 'I found :count matching listings. :more Would you like to see photos?',
        'results_more' => 'Ask for more if you want the next options.',
        'budget_fallback' => 'I could not find a listing within that budget. :minimum If you want, increase the budget and I will search again.',
        'budget_fallback_minimum' => 'The minimum available price in this scope is EGP :minimum.',
        'no_results' => 'I could not find active cash listings in that scope. You can change the location or property type.',
        'exhausted' => 'Those are all the retained listings I have right now. If you want more, change the budget or preferences.',
    ],

    'optional_preferences' => 'I have the main details. If you want, share area, bedrooms, bathrooms, or features too.',
    'awaiting_slot' => 'Got it. Please share your :slot so I can continue.',
    'saved_preferences' => 'Got it. I saved your search preferences.',
    'saved_preferences_checkin' => 'Got it. I saved your search preferences. If this is not working well, I can help route your concern for follow-up.',
];
