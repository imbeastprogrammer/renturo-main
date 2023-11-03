import {
    AttachmentLogo,
    CheckboxLogo,
    ChecklistLogo,
    DateLogo,
    DropdownLogo,
    EmailLogo,
    NumberLogo,
    RadioLogo,
    TextAreaLogo,
    TextfieldLogo,
    TimeLogo,
} from '@/assets/form-builder';

export enum FieldTypes {
    TEXT_FIELD = 'text-field',
    TEXT_AREA = 'textarea',
    NUMBER = 'number',
    EMAIL = 'email',
    DATE = 'date',
    TIME = 'time',
    DROPDOWN = 'dropdown',
    CHECKBOX = 'checkbox',
    RADIO_BUTTON = 'radio-button',
    CHECKLIST = 'checklist',
    ATTACHMENT = 'attachment',
}

export const toolboxItems = [
    {
        label: 'Text',
        items: [
            {
                title: 'Text Field',
                icon: TextfieldLogo,
                id: FieldTypes.TEXT_FIELD,
            },
            {
                title: 'Text Area',
                icon: TextAreaLogo,
                id: FieldTypes.TEXT_AREA,
            },
            { title: 'Number', icon: NumberLogo, id: FieldTypes.NUMBER },
            { title: 'Email', icon: EmailLogo, id: FieldTypes.EMAIL },
        ],
    },
    {
        label: 'Date',
        items: [
            { title: 'Date', icon: DateLogo, id: FieldTypes.DATE },
            { title: 'Time', icon: TimeLogo, id: FieldTypes.TIME },
        ],
    },
    {
        label: 'Multi',
        items: [
            {
                title: 'Dropdown',
                icon: DropdownLogo,
                id: FieldTypes.DROPDOWN,
            },
            {
                title: 'Checkbox',
                icon: CheckboxLogo,
                id: FieldTypes.CHECKBOX,
            },
            {
                title: 'Radio',
                icon: RadioLogo,
                id: FieldTypes.RADIO_BUTTON,
            },
            {
                title: 'Check List',
                icon: ChecklistLogo,
                id: FieldTypes.CHECKLIST,
            },
        ],
    },
    {
        label: 'Media',
        items: [
            {
                title: 'Attachment',
                icon: AttachmentLogo,
                id: FieldTypes.ATTACHMENT,
            },
        ],
    },
];
