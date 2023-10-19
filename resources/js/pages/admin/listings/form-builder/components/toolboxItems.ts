import {
    CalendarIcon,
    CheckSquareIcon,
    ChevronDownSquareIcon,
    CircleIcon,
    ClockIcon,
    FileCheck,
    FileDigitIcon,
    FileTypeIcon,
    ImageIcon,
    MailIcon,
    PaperclipIcon,
    TypeIcon,
} from 'lucide-react';

export const toolboxItems = [
    {
        label: 'Text',
        items: [
            { title: 'Text Field', icon: TypeIcon, id: 'text-field' },
            { title: 'Text Area', icon: FileTypeIcon, id: 'text-area' },
            { title: 'Number', icon: FileDigitIcon, id: 'number' },
            { title: 'Email', icon: MailIcon, id: 'email' },
        ],
    },
    {
        label: 'Date',
        items: [
            { title: 'Date', icon: CalendarIcon, id: 'date' },
            { title: 'Time', icon: ClockIcon, id: 'time' },
        ],
    },
    {
        label: 'Multi',
        items: [
            { title: 'Dropdown', icon: ChevronDownSquareIcon, id: 'dropdown' },
            { title: 'Checkbox', icon: CheckSquareIcon, id: 'checkbox' },
            { title: 'Radio Button', icon: CircleIcon, id: 'radio-button' },
            { title: 'Check List', icon: FileCheck, id: 'checklist' },
        ],
    },
    {
        label: 'Media',
        items: [
            { title: 'Attachment', icon: PaperclipIcon, id: 'attachment' },
            { title: 'Image', icon: ImageIcon, id: 'image' },
        ],
    },
];
