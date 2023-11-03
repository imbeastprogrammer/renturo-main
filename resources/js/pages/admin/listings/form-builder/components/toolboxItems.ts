import React from 'react';
import {
    AttachmentLogo,
    BodyLogo,
    CheckboxLogo,
    ChecklistLogo,
    DateLogo,
    DropdownLogo,
    EmailLogo,
    NumberLogo,
    RadioLogo,
    TextAreaLogo,
    HeadingLogo,
    TextfieldLogo,
    TimeLogo,
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
                icon: HeadingLogo,
                type: 'heading',
            },
            {
                title: 'Body',
                icon: BodyLogo,
                type: 'body',
            },
            {
                title: 'Text Field',
                icon: TextfieldLogo,
                type: 'text-field',
            },
            {
                title: 'Text Area',
                icon: TextAreaLogo,
                type: 'textarea',
            },
            { title: 'Number', icon: NumberLogo, type: 'number' },
            { title: 'Email', icon: EmailLogo, type: 'email' },
        ],
    },
    {
        label: 'Date',
        items: [
            { title: 'Date', icon: DateLogo, type: 'date' },
            { title: 'Time', icon: TimeLogo, type: 'time' },
        ],
    },
    {
        label: 'Multi',
        items: [
            {
                title: 'Dropdown',
                icon: DropdownLogo,
                type: 'dropdown',
            },
            {
                title: 'Checkbox',
                icon: CheckboxLogo,
                type: 'checkbox',
            },
            {
                title: 'Radio',
                icon: RadioLogo,
                type: 'radio',
            },
            {
                title: 'Check List',
                icon: ChecklistLogo,
                type: 'checklist',
            },
        ],
    },
    {
        label: 'Media',
        items: [
            {
                title: 'Attachment',
                icon: AttachmentLogo,
                type: 'attachment',
            },
        ],
    },
];
