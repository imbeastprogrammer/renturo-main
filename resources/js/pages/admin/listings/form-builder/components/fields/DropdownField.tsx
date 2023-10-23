import { TrashIcon } from 'lucide-react';
import { FormElement, FormElementInstance } from '../FormElement';
import { Separator } from '@/components/ui/separator';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import useFormBuilder from '@/hooks/useFormBuilder';

const extraAttributes = {
    is_required: false,
    label: 'Editable Label',
};
const DropdownField: FormElement = {
    type: 'dropdown',
    construct: (id: string) => ({
        id,
        type: 'dropdown',
        extraAttributes,
    }),
    designerComponent: DesignerComponent,
    propertiesComponent: PropertiesComponent,
};

type DesignerComponentProps = {
    element: FormElementInstance;
};
function DesignerComponent({ element }: DesignerComponentProps) {
    const { removeField, setSelectedField } = useFormBuilder();
    const elementInstance = element as FormElementInstance & {
        extraAttributes: typeof extraAttributes;
    };

    return (
        <div
            className='w-full rounded-lg border bg-white p-4 shadow-lg'
            onSelect={() => setSelectedField(element)}
        >
            <div className='flex justify-between'>
                <h1>{element.type}</h1>
                <TrashIcon onClick={() => removeField(element.id)} />
            </div>
            <Separator className='my-2' />
            <div className='space-y-2'>
                <Label className='text-[20px]'>
                    {elementInstance.extraAttributes.label}
                </Label>
                <Select>
                    <SelectTrigger className='pointer-events-none'>
                        <SelectValue placeholder='Select a fruit' />
                    </SelectTrigger>
                    <SelectContent></SelectContent>
                </Select>
            </div>
        </div>
    );
}

function PropertiesComponent() {
    return <div>TextField</div>;
}

export default DropdownField;
