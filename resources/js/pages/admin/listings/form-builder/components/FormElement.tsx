import TextField from './fields/TextField';

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

// partial to be removed soon
export const FormElements: FormElementsType = {
    'text-field': TextField,
};
