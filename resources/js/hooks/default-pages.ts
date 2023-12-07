import { v4 as uuidv4 } from 'uuid';
import { Page } from './useFormBuilder';

const contactPageFields: Page['fields'] = [
    {
        id: uuidv4(),
        type: 'text-field',
        extraAttributes: {
            is_required: true,
            label: 'First name',
        },
    },
    {
        id: uuidv4(),
        type: 'text-field',
        extraAttributes: {
            is_required: true,
            label: 'Last Name',
        },
    },
    {
        id: uuidv4(),
        type: 'number',
        extraAttributes: {
            is_required: true,
            label: 'Contact Number',
            type: 'mobile_number_input',
        },
    },
    {
        id: uuidv4(),
        type: 'text-field',
        extraAttributes: {
            is_required: true,
            label: 'Address',
        },
    },
];

const searchFilterFields: Page['fields'] = [
    {
        id: uuidv4(),
        type: 'number',
        extraAttributes: {
            is_required: true,
            label: 'Price Range',
            type: 'range_input',
        },
    },
    {
        id: uuidv4(),
        type: 'checkbox',
        extraAttributes: {
            is_required: true,
            label: 'Price Range',
            type: 'range_input',
            options: ['Category 1', 'Category 2', 'Category 3', 'Category 4'],
            multiple_answer_accepted: false,
        },
    },
    {
        id: uuidv4(),
        type: 'date',
        extraAttributes: {
            is_required: true,
            label: 'Date Range',
            type: 'date_range',
        },
    },
    {
        id: uuidv4(),
        type: 'number',
        extraAttributes: {
            is_required: true,
            label: 'Number of Guests',
            type: 'number_input',
        },
    },
    {
        id: uuidv4(),
        type: 'text-field',
        extraAttributes: {
            is_required: true,
            label: 'Amenities',
        },
    },
];

const defaultPages: Page[] = [
    {
        isDefault: true,
        page_id: uuidv4(),
        fields: contactPageFields,
        page_title: 'Contact Info',
    },
    {
        isDefault: true,
        page_id: uuidv4(),
        fields: searchFilterFields,
        page_title: 'Search Filter',
    },
];

export default defaultPages;
