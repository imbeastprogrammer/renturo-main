import {
    CalendarIcon,
    CheckSquareIcon,
    ChevronDownSquareIcon,
    CircleIcon,
    ClockIcon,
    FileCheck,
    FileDigitIcon,
    FileTypeIcon,
    MailIcon,
    PaperclipIcon,
    TypeIcon,
} from 'lucide-react';

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
            { title: 'Text Field', icon: TypeIcon, id: FieldTypes.TEXT_FIELD },
            {
                title: 'Text Area',
                icon: FileTypeIcon,
                id: FieldTypes.TEXT_AREA,
            },
            { title: 'Number', icon: FileDigitIcon, id: FieldTypes.NUMBER },
            { title: 'Email', icon: MailIcon, id: FieldTypes.EMAIL },
        ],
    },
    {
        label: 'Date',
        items: [
            { title: 'Date', icon: CalendarIcon, id: FieldTypes.DATE },
            { title: 'Time', icon: ClockIcon, id: FieldTypes.TIME },
        ],
    },
    {
        label: 'Multi',
        items: [
            {
                title: 'Dropdown',
                icon: ChevronDownSquareIcon,
                id: FieldTypes.DROPDOWN,
            },
            {
                title: 'Checkbox',
                icon: CheckSquareIcon,
                id: FieldTypes.CHECKBOX,
            },
            {
                title: 'Radio Button',
                icon: CircleIcon,
                id: FieldTypes.RADIO_BUTTON,
            },
            { title: 'Check List', icon: FileCheck, id: FieldTypes.CHECKLIST },
        ],
    },
    {
        label: 'Media',
        items: [
            {
                title: 'Attachment',
                icon: PaperclipIcon,
                id: FieldTypes.ATTACHMENT,
            },
        ],
    },
];
