import { TrashIcon } from 'lucide-react';
import { FormElement, FormElementInstance } from '../FormElement';
import { Separator } from '@/components/ui/separator';
import { Label } from '@/components/ui/label';
import useFormBuilder from '@/hooks/useFormBuilder';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';

const extraAttributes = {
    is_required: false,
    label: 'Editable Label',
    options: [],
};
const RadioField: FormElement = {
    type: 'radio-button',
    construct: (id: string) => ({
        id,
        type: 'radio-button',
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
                <RadioGroup>
                    {elementInstance.extraAttributes.options.map((option) => (
                        <div
                            key={option}
                            className='flex items-center gap-4 rounded-lg bg-metalic-blue/5 p-2 px-4 text-metalic-blue'
                        >
                            <RadioGroupItem
                                value={option}
                                className='border-metalic-blue'
                            />
                            <Label>{option}</Label>
                        </div>
                    ))}
                </RadioGroup>
            </div>
        </div>
    );
}

function PropertiesComponent() {
    return <div>TextField</div>;
}

export default RadioField;
