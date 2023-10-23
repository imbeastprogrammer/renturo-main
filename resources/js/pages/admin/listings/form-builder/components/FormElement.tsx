import CheckboxField from './fields/CheckboxField';
import DateField from './fields/DateField';
import DropdownField from './fields/DropdownField';
import EmailField from './fields/EmailField';
import NumberField from './fields/NumberField';
import RadioField from './fields/RadioField';
import TextAreaField from './fields/TextAreaField';
import TextField from './fields/TextField';
import TimeField from './fields/TimeField';

export type ElementsType =
    | 'text-field'
    | 'textarea'
    | 'number'
    | 'email'
    | 'date'
    | 'time'
    | 'dropdown'
    | 'checkbox'
    | 'radio-button'
    | 'checklist'
    | 'attachment'
    | 'image';

export type FormElementInstance = {
    id: string;
    type: ElementsType;
    extraAttributes?: Record<string, any>;
};

export type FormElement = {
    type: ElementsType;
    construct: (id: string) => FormElementInstance;
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
    'text-field': TextField,
    textarea: TextAreaField,
    number: NumberField,
    email: EmailField,
    date: DateField,
    time: TimeField,
    dropdown: DropdownField,
    checkbox: CheckboxField,
    'radio-button': RadioField,
};
