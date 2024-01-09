import AttachmentField from './fields/AttachmentField';
import Body from './fields/Body';
import CheckboxField from './fields/CheckboxField';
import ChecklistField from './fields/ChecklistField';
import DateField from './fields/DateField';
import DropdownField from './fields/DropdownField';
import EmailField from './fields/EmailField';
import NumberField from './fields/NumberField';
import RadioField from './fields/RadioField';
import Heading from './fields/Heading';
import TextAreaField from './fields/TextAreaField';
import TextField from './fields/TextField';
import TimeField from './fields/TimeField';
import RatingField from './fields/RatingField';

export type ElementsType =
    | 'heading'
    | 'body'
    | 'text'
    | 'textarea'
    | 'number'
    | 'email'
    | 'date'
    | 'time'
    | 'select'
    | 'checkbox'
    | 'radio'
    | 'checklist'
    | 'rating'
    | 'file';
// | 'password'
// | 'multiselect'
// | 'attachment'
// | 'hidden'
// | 'color'
// | 'url';

export type FormElementInstance = {
    id: string | number;
    type: ElementsType;
    label: string;
    is_required: boolean;
    data?: Record<string, any>;
};

export type FormElement = {
    type: ElementsType;
    construct: (id: string | number) => FormElementInstance;
    designerComponent: React.FC<{
        element: FormElementInstance;
    }>;
    propertiesComponent: React.FC<{
        element: FormElementInstance;
    }>;
};

type FormElementsType = {
    [key in ElementsType]: FormElement;
};

export const FormElements: FormElementsType = {
    heading: Heading,
    body: Body,
    text: TextField,
    textarea: TextAreaField,
    number: NumberField,
    email: EmailField,
    date: DateField,
    time: TimeField,
    select: DropdownField,
    checkbox: CheckboxField,
    checklist: ChecklistField,
    radio: RadioField,
    file: AttachmentField,
    rating: RatingField,
};
