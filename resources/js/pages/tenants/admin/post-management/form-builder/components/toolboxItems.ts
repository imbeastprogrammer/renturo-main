import React from 'react';
import {
    AttachmentIcon,
    BodyIcon,
    CheckboxIcon,
    ChecklistIcon,
    DateIcon,
    DropdownIcon,
    EmailIcon,
    NumberIcon,
    RadioButtonIcon,
    TextAreaIcon,
    HeadingIcon,
    TextFieldIcon,
    TimeIcon,
} from '@/assets/form-builder';
import { ElementsType } from './FormElement';

type ToolboxItem = {
    label: string;
    items: { title: string; icon: React.FC; type: ElementsType }[];
};

export const toolboxItems: ToolboxItem[] = [
    {
        label: 'Text',
        items: [
            {
                title: 'Heading',
                icon: HeadingIcon,
                type: 'heading',
            },
            {
                title: 'Body',
                icon: BodyIcon,
                type: 'body',
            },
            {
                title: 'Text Field',
                icon: TextFieldIcon,
                type: 'text-field',
            },
            {
                title: 'Text Area',
                icon: TextAreaIcon,
                type: 'textarea',
            },
            { title: 'Number', icon: NumberIcon, type: 'number' },
            { title: 'Email', icon: EmailIcon, type: 'email' },
        ],
    },
    {
        label: 'Date',
        items: [
            { title: 'Date', icon: DateIcon, type: 'date' },
            { title: 'Time', icon: TimeIcon, type: 'time' },
        ],
    },
    {
        label: 'Multi',
        items: [
            {
                title: 'Dropdown',
                icon: DropdownIcon,
                type: 'dropdown',
            },
            {
                title: 'Checkbox',
                icon: CheckboxIcon,
                type: 'checkbox',
            },
            {
                title: 'Radio',
                icon: RadioButtonIcon,
                type: 'radio',
            },
            {
                title: 'Check List',
                icon: ChecklistIcon,
                type: 'checklist',
            },
        ],
    },
    {
        label: 'Media',
        items: [
            {
                title: 'Attachment',
                icon: AttachmentIcon,
                type: 'attachment',
            },
        ],
    },
];
